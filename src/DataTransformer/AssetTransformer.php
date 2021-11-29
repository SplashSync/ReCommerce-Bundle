<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Connectors\ReCommerce\DataTransformer;

use Psr\Cache\InvalidArgumentException;
use Splash\Connectors\ReCommerce\Models\Api\Asset;
use Splash\Models\Objects\FilesTrait;
use Splash\OpenApi\Visitor\AbstractVisitor;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class AssetTransformer
{
    use FilesTrait;

    /**
     * @var string
     */
    const CACHE_NAMESPACE = "splash.recommerce.connector.asset.meta";

    /**
     * @var AbstractVisitor
     */
    private static AbstractVisitor $visitor;

    /**
     * @var string
     */
    private static string $shipmentId;

    /**
     * @var ApcuAdapter
     */
    private static ApcuAdapter $apcu;

    /**
     * @var Asset Asset Object for Cache Search
     */
    private static Asset $cacheAsset;

    /**
     * @var int Files Info Cache Lifetime
     */
    private static int $cacheTtl = 604800;

    /**
     * Configure API Visitor
     *
     * @param AbstractVisitor $visitor
     * @param string $shipmentId
     * @return void
     */
    public static function configure(AbstractVisitor $visitor, string $shipmentId): void
    {
        self::$visitor = $visitor;
        self::$shipmentId = $shipmentId;
    }

    /**
     * Load Asset Information Array from Cache or API
     *
     * @param Asset $asset
     *
     * @return null|array
     */
    public static function getInfos(Asset $asset): ?array
    {
        //====================================================================//
        // Ensure Cache Exists
        static::$apcu = static::$apcu ?? new ApcuAdapter();
        static::$cacheAsset = $asset;
        //====================================================================//
        // Load Splash Image from Cache or API
        try {
            $fromCache = static::$apcu->get(self::getCacheKey($asset), function (ItemInterface $item) {
                //====================================================================//
                // Setup Cache Item
                $item->expiresAfter(self::$cacheTtl);
                //====================================================================//
                // Load Splash Image from Api
                return self::getMetadataFromApi(static::$cacheAsset);
            });
        } catch (InvalidArgumentException $ex) {
            return null;
        }
        //====================================================================//
        // Check if Splash Image is In Cache
        if ($fromCache) {
            return $fromCache;
        }
        //====================================================================//
        // Loading Splash Image Fail
        try {
            static::$apcu->delete(self::getCacheKey($asset));
        } catch (InvalidArgumentException $e) {
            return null;
        }

        return null;
    }

    /**
     * Load Asset Information Array from Api
     *
     * @param Asset $asset
     *
     * @return null|array
     */
    private static function getMetadataFromApi(Asset  $asset): ?array
    {
        //====================================================================//
        // Build Splash File Name
        $filename = !empty($asset->name) ? $asset->name : basename((string) parse_url($asset->url, PHP_URL_PATH));
        //====================================================================//
        // Load Image from API
        $splashFile = null;
        for ($count = 0; $count < 3; $count++) {
            //====================================================================//
            // Build Request Api Url
            $apiPath = "/shipment/".self::$shipmentId."/asset/".$asset->id."/download";
            //====================================================================//
            // Read File Contents via Raw Get Request
            $rawResponse = self::$visitor->getConnexion()->getRaw($apiPath);
            if (!$rawResponse) {
                break;
            }
            //====================================================================//
            // Build File Array
            $splashFile = array();
            $splashFile["name"] = $splashFile["filename"] = $filename;
            $splashFile["path"] = $apiPath;
            $splashFile["url"] = $apiPath;
            $splashFile["md5"] = md5($rawResponse);
            $splashFile["size"] = strlen($rawResponse);
            $splashFile["ttl"] = 10;
        }

        return $splashFile;
    }

    /**
     * Build Asset Cache Key
     *
     * @param Asset $asset
     *
     * @return string
     */
    private static function getCacheKey(Asset $asset): string
    {
        //====================================================================//
        // Build Cache Key
        return implode(
            ".",
            array(self::CACHE_NAMESPACE, $asset->id, md5($asset->url))
        );
    }
}
