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

use Psr\SimpleCache\InvalidArgumentException;
use Splash\Connectors\ReCommerce\Models\Api\Asset;
use Splash\Models\Objects\FilesTrait;
use Splash\OpenApi\Visitor\AbstractVisitor;
use Symfony\Component\Cache\Simple\ApcuCache;

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
    private static $visitor;

    /**
     * @var ApcuCache
     */
    private static $apcu;

    /**
     * @var int Files Info Cache Lifetime
     */
    private static $cacheTtl = 604800;

    /**
     * Configure API Visitor
     *
     * @param AbstractVisitor $visitor
     *
     * @return void
     */
    public static function configure(AbstractVisitor $visitor): void
    {
        self::$visitor = $visitor;
    }

    /**
     * Load Asset Informations Array from Cache or API
     *
     * @param Asset $asset
     *
     * @return null|array
     */
    public static function getInfos(Asset $asset): ?array
    {
        //====================================================================//
        // Check if Splash File is In Cache
        $fromCache = self::getMetadataFromCache($asset);
        if ($fromCache) {
            return $fromCache;
        }
        //====================================================================//
        // Load Splash Image from Url
        $fromUrl = self::getMetadataFromUrl($asset->url, $asset->name);
        if ($fromUrl) {
            //====================================================================//
            // Save Splash File is In Cache
            self::setMetadataInCache($asset, $fromUrl);

            return $fromUrl;
        }
        //====================================================================//
        // Loading  Splash Image Fail
        return null;
    }

    /**
     * Load Asset Informations Array from Url
     *
     * @param string      $absoluteUrl
     * @param null|string $filename
     *
     * @return null|array
     */
    private static function getMetadataFromUrl(string $absoluteUrl, ?string $filename): ?array
    {
        //====================================================================//
        // Build Image File Name
        $filename = !empty($filename) ? $filename : basename((string) parse_url($absoluteUrl, PHP_URL_PATH));
        //====================================================================//
        // Load Image from API
        $splashFile = null;
        for ($count = 0; $count < 3; $count++) {
            //====================================================================//
            // Read File Contents via Raw Get Request
            $rawResponse = self::$visitor->getConnexion()->getRaw($absoluteUrl, null, true);
            if (!$rawResponse) {
                break;
            }
            //====================================================================//
            // Build File Array
            $splashFile = array();
            $splashFile["name"] = $splashFile["filename"] = $filename;
            $splashFile["path"] = $absoluteUrl;
            $splashFile["url"] = $absoluteUrl;
            $splashFile["raw"] = base64_encode((string) $rawResponse);
            $splashFile["md5"] = md5($rawResponse);
            $splashFile["size"] = strlen($rawResponse);
        }

        return $splashFile;
    }

    /**
     * Load Asset Informations from Cache
     *
     * @param Asset $asset
     *
     * @return null|array
     */
    private static function getMetadataFromCache(Asset  $asset): ?array
    {
        //====================================================================//
        // Build Cache Key
        $cacheKey = self::getCacheKey($asset);
        //====================================================================//
        // Ensure Cache Exists
        if (!isset(static::$apcu)) {
            static::$apcu = new ApcuCache();
        }
        //====================================================================//
        // Check if Asset is In Cache
        try {
            if (static::$apcu->has($cacheKey)) {
                return static::$apcu->get($cacheKey);
            }
        } catch (InvalidArgumentException $e) {
            return null;
        }

        return null;
    }

    /**
     * Save Asset Informations in Cache
     *
     * @param Asset $asset
     * @param array $splashFile
     *
     * @return void
     */
    private static function setMetadataInCache(Asset $asset, array $splashFile): void
    {
        //====================================================================//
        // Ensure Cache Exists
        if (!isset(static::$apcu)) {
            static::$apcu = new ApcuCache();
        }
        //====================================================================//
        // Store In Cache
        try {
            static::$apcu->set(self::getCacheKey($asset), $splashFile, static::$cacheTtl);
        } catch (InvalidArgumentException $e) {
            return;
        }
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
