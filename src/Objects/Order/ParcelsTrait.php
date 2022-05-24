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

namespace Splash\Connectors\ReCommerce\Objects\Order;

use Exception;
use Splash\Connectors\ReCommerce\DataTransformer\ParcelTransformer;

/**
 * Order Shipment Parcels Details
 */
trait ParcelsTrait
{
    /**
     * @var string
     */
    private static string $parcelsList = "parcels";

    /**
     * @var null|array[]
     */
    private ?array $parcels;

    /**
     * Build Objects Fields.
     *
     * @return void
     */
    protected function buildParcelsFields(): void
    {
        //====================================================================//
        // PARCEL - Identifier
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("id")
            ->name("Identifier")
            ->inList(self::$parcelsList)
            ->microdata("https://schema.org/ParcelDelivery", "identifier")
            ->isNotTested()
        ;
        //====================================================================//
        // PARCEL - Tracking Number
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("trackingNumber")
            ->name("Tracking")
            ->inList(self::$parcelsList)
            ->microdata("https://schema.org/ParcelDelivery", "trackingNumber")
            ->isNotTested()
        ;
        //====================================================================//
        // PARCEL - Tracking Url
        $this->fieldsFactory()->create(SPL_T_URL)
            ->identifier("trackingUrl")
            ->name("Url")
            ->inList(self::$parcelsList)
            ->microdata("https://schema.org/ParcelDelivery", "trackingUrl")
            ->isNotTested()
        ;
        //====================================================================//
        // PARCEL - Contents
        $this->fieldsFactory()->create(SPL_T_INLINE)
            ->identifier("contents")
            ->name("Contents")
            ->inList(self::$parcelsList)
            ->microdata("https://schema.org/ParcelDelivery", "itemShipped")
            ->isNotTested()
        ;
        //====================================================================//
        // PARCEL - Serials
        $this->fieldsFactory()->create(SPL_T_INLINE)
            ->identifier("serials")
            ->name("Serials")
            ->inList(self::$parcelsList)
            ->microdata("https://schema.org/ParcelDelivery", "itemSerials")
            ->isNotTested()
        ;
        //====================================================================//
        // PARCEL - Serial Shipping Container Code
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("sscc")
            ->name("SSCC")
            ->description("Serial Shipping Container Code")
            ->inList(self::$parcelsList)
            ->microdata("https://schema.org/ParcelDelivery", "disambiguatingDescription")
            ->isNotTested()
        ;
    }

    /**
     * Build Fields using FieldFactory
     */
    protected function buildParcelsDimFields(): void
    {
        //====================================================================//
        // PARCEL - Weight
        $this->fieldsFactory()->create(SPL_T_DOUBLE)
            ->identifier("weight")
            ->name("Weight (kg)")
            ->inList(self::$parcelsList)
            ->microdata("https://schema.org/ParcelDelivery", "weight")
            ->isNotTested()
        ;
        //====================================================================//
        // PARCEL - Height
        $this->fieldsFactory()->create(SPL_T_DOUBLE)
            ->identifier("height")
            ->name("Height (m)")
            ->inList(self::$parcelsList)
            ->microdata("https://schema.org/ParcelDelivery", "height")
            ->isNotTested()
        ;
        //====================================================================//
        // PARCEL - Width
        $this->fieldsFactory()->create(SPL_T_DOUBLE)
            ->identifier("width")
            ->name("Width (m)")
            ->inList(self::$parcelsList)
            ->microdata("https://schema.org/ParcelDelivery", "width")
            ->isNotTested()
        ;
        //====================================================================//
        // PARCEL - Depth
        $this->fieldsFactory()->create(SPL_T_DOUBLE)
            ->identifier("depth")
            ->name("Depth (m)")
            ->inList(self::$parcelsList)
            ->microdata("https://schema.org/ParcelDelivery", "length")
            ->isNotTested()
        ;
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     *
     * @throws Exception
     */
    protected function getParcelsFields(string $key, string $fieldName): void
    {
        //====================================================================//
        // Check if List field & Init List Array
        $fieldId = self::lists()->initOutput($this->out, self::$parcelsList, $fieldName);
        if (!$fieldId) {
            return;
        }
        //====================================================================//
        // Simulate Parcels List if Necessary
        if (!isset($this->parcels) && $this->connector->isSandbox()) {
            $this->parcels = $this->object->getParcelSimulation();
        }
        //====================================================================//
        // Fill Boxes List with Data
        if (is_array($this->parcels)) {
            foreach ($this->parcels as $index => $parcel) {
                //====================================================================//
                // Read Raw value
                $value = self::getParcelValue($parcel, $fieldId);
                //====================================================================//
                // Insert Data in List
                self::lists()->Insert($this->out, self::$parcelsList, $fieldName, $index, $value);
            }
        }
        unset($this->in[$key]);
    }

    /**
     * Write Given Fields
     *
     * @param string $fieldName Field Identifier / Name
     * @param array|null $fieldData Field Data
     *
     * @throws Exception
     */
    protected function setParcelsFields(string $fieldName, ?array $fieldData): void
    {
        //====================================================================//
        // Check if Parcel List Field
        if (self::$parcelsList != $fieldName) {
            return;
        }
        //====================================================================//
        // If Order Require Boxes Updates
        if (!$this->object->isBoxesToLinesOrder()) {
            //====================================================================//
            // Convert Parcels to Boxes
            $this->in["boxes"] = $boxes = ParcelTransformer::toBoxes($fieldData);
            //====================================================================//
            // Update Shipment Boxes
            $this->updateBoxes($boxes);
        }
        //====================================================================//
        // Convert Parcels to Transport Units
        $this->in["transportUnits"] = $units = ParcelTransformer::toUnits(
            $fieldData,
            $this->object->getTransportUnitTypeLabel()
        );
        //====================================================================//
        // Update Shipment Transport Units
        $this->updateTransportUnits($units);

        unset($this->in[$fieldName]);
    }

    /**
     * Read requested Parcel Field Value
     *
     * @param array  $parcel  Input List Key
     * @param string $fieldId Field Identifier / Name
     *
     * @return null|float|string
     */
    protected static function getParcelValue(array $parcel, string $fieldId)
    {
        //====================================================================//
        // Read Raw value
        switch ($fieldId) {
            case "weight":
                return isset($parcel[$fieldId]) ? (float) $parcel[$fieldId] : null;
            case "contents":
            case "serials":
                return (isset($parcel[$fieldId]) && is_array($parcel[$fieldId]))
                    ? (string) json_encode($parcel[$fieldId])
                    : null;
            default:
                return isset($parcel[$fieldId]) ? $parcel[$fieldId] : null;
        }
    }
}
