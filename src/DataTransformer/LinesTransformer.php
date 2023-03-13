<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Connectors\ReCommerce\DataTransformer;

use Splash\Connectors\ReCommerce\Models\Api\Line;

class LinesTransformer
{
    /**
     * Temporary Storage for Accessories Ean Codes
     *
     * @var array<string, string>
     */
    private static array $accCodeToEan = array();

    /**
     * Transforms Shipments Lines to Expended Line List.
     *
     * @param Line[] $lines
     *
     * @return array
     */
    public static function expend(array $lines): array
    {
        $expendedLines = array();
        //====================================================================//
        // Walk on Sources Lines
        foreach ($lines as $line) {
            //====================================================================//
            // Push Original Line
            $expendedLines[] = clone $line;
            //====================================================================//
            // Check if Line Has Accessories
            $accessories = $line->getAccessories();
            if (!is_array($accessories) || empty($accessories)) {
                continue;
            }
            //====================================================================//
            // Walk on Line Accessories
            foreach ($accessories as $accessory) {
                //====================================================================//
                // Push Original Line
                $expendedLines[] = $line->getAccessoryCopy(
                    (string) $accessory["productCodeReference"],
                    self::getEan($accessory["productCodeReference"]),
                );
            }
        }

        return $expendedLines;
    }

    /**
     * Transforms Shipments Lines to Expended Line List.
     *
     * @param Line[] $lines
     *
     * @return array
     */
    public static function simParcels(array $lines): array
    {
        $parcels = array();
        //====================================================================//
        // Walk on Sources Lines
        foreach ($lines as $line) {
            //====================================================================//
            // Build Parcel ID
            $parcelId = $line->getId() % 3;
            $parcelMd5 = md5($line->getArticleCode());
            //====================================================================//
            // Add Line to Parcel
            if (!isset($parcels[$parcelId])) {
                $parcels[$parcelId] = array(
                    "id" => "Parcel.".strtoupper($parcelMd5),
                    "trackingNumber" => $parcelMd5,
                    "trackingUrl" => "https://track.my.parcel.com/?id=".$parcelMd5,
                    "weight" => strlen($line->getLabel()) / 10,
                    "height" => rand(10, 100) / 100,
                    "width" => rand(10, 100) / 100,
                    "depth" => rand(10, 100) / 100,
                    "contents" => array(),
                    "sscc" => "SSCC-".($parcelId % 2 + 1),
                );
            }
            //====================================================================//
            // Add LineId to Parcel
            $parcels[$parcelId]["contents"][] = $line->getId();
            //====================================================================//
            // Fake Products Serials (1/3)
            $parcels[$parcelId]["serials"][] = ($line->getId() % 2)
                ? implode(" | ", str_split($parcelMd5, 8))
                : "";
        }

        ksort($parcels);

        return $parcels;
    }

    /**
     * Import Accessories Ean from Embedded information.
     *
     * @param array<string, array|string> $embedded Raw Embedded infos
     *
     * @return int Number of imported Ean
     */
    public static function populateEan(array $embedded): int
    {
        //====================================================================//
        // Reset Ean Storage
        self::$accCodeToEan = array();
        //====================================================================//
        // Safety Check
        if (!isset($embedded["productCodes"]) || !is_array($embedded["productCodes"])) {
            return 0;
        }
        //====================================================================//
        // Walk on Product Codes to Extract Ean
        foreach ($embedded["productCodes"] as $productItem) {
            //====================================================================//
            // Safety Check
            if (!isset($productItem["reference"]) || !isset($productItem["ean"])) {
                continue;
            }
            //====================================================================//
            // Add Ean to Table
            self::$accCodeToEan[(string) $productItem["reference"]] = (string) $productItem["ean"];
        }

        return count(self::$accCodeToEan);
    }

    /**
     * Import Accessories Ean from Embedded informations.
     *
     * @param string $productCode
     *
     * @return null|string
     */
    public static function getEan(string $productCode): ?string
    {
        if (!isset(self::$accCodeToEan[$productCode])) {
            return null;
        }

        return self::$accCodeToEan[$productCode];
    }
}
