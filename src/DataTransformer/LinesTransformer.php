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

use Splash\Connectors\ReCommerce\Models\Api\Line;

class LinesTransformer
{
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
                $expendedLines[] = $line->getAccessoryCopy($accessory);
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

            //====================================================================//
            // Add Line to Parcel
            if (!isset($parcels[$parcelId])) {
                $parcels[$parcelId] = array(
                    "id" => "Parcel-".$parcelId,
                    "trackingNumber" => md5($line->getArticleCode()),
                    "trackingUrl" => "https://track.my.parcel.com/?id=".md5($line->getArticleCode()),
                    "weight" => strlen($line->getLabel()) / 10,
                    "contents" => array(),
                    "sscc" => "SSCC-".($parcelId % 2 + 1),
                );
            }
            $parcels[$parcelId]["contents"][] = $line->getId();
        }

        ksort($parcels);

        return $parcels;
    }
}
