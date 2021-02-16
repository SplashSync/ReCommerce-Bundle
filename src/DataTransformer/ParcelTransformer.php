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

use Splash\Client\Splash;
use Splash\Connectors\ReCommerce\Models\Api;

/**
 * Convert Parcels Lists to Boxes & Transport Units
 */
class ParcelTransformer
{
    /**
     * Transforms Parcels Lists to Boxes List.
     *
     * @param null|array $parcels
     *
     * @return Api\Box[]
     */
    public static function toBoxes(?array $parcels): array
    {
        $boxes = array();
        //====================================================================//
        // Safety Check
        if (empty($parcels)) {
            return $boxes;
        }
        //====================================================================//
        // Walk on Parcels
        foreach ($parcels as $parcel) {
            //====================================================================//
            // Create Associated Box
            $box = Api\Box::createFromParcel($parcel);
            if ($box) {
                $boxes[] = $box;
            }
        }

        return $boxes;
    }

    /**
     * Transforms Parcels Lists to Transport Units List.
     *
     * @param null|array $parcels
     *
     * @return Api\TransportUnit[]
     */
    public static function toUnits(?array $parcels): array
    {
        /** @var Api\TransportUnit[] $units */
        $units = array();
        //====================================================================//
        // Safety Check
        if (empty($parcels)) {
            return $units;
        }
        //====================================================================//
        // Walk on Parcels
        foreach ($parcels as $parcel) {
            //====================================================================//
            // Safety Check
            if (empty($parcel["sscc"])) {
                Splash::log()->err("Parcel has no SSCC Defined...");

                continue;
            }
            $name = (string) $parcel["sscc"];
            //====================================================================//
            // Create Transport Unit if Needed
            if (!isset($units[$name])) {
                $units[$name] = new Api\TransportUnit($name);
            }
            //====================================================================//
            // Add Parcel to Transport Unit
            $units[$name]->addParcel($parcel);
        }

        return $units;
    }
}
