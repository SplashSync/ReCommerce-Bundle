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

use Splash\Client\Splash;
use Splash\Connectors\ReCommerce\DataTransformer\StatusTransformer;

/**
 * Order Status Trait
 */
trait StatusTrait
{
    /**
     * Build Status Fields
     *
     * @return void
     */
    protected function buildStatusFields(): void
    {
        //====================================================================//
        // ORDER STATUS
        //====================================================================//

        //====================================================================//
        // Order Current Status
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("splashStatut")
            ->Name("Order status")
            ->Description("Status of the order")
            ->MicroData("http://schema.org/Order", "orderStatus")
            ->addChoices(StatusTransformer::getAll())
        ;

        //====================================================================//
        // ORDER STATUS FLAGS
        //====================================================================//

        //====================================================================//
        // Is Validated
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->Identifier("isValidated")
            ->Name("Is Valid")
            ->MicroData("http://schema.org/OrderStatus", "OrderProcessing")
            ->isReadOnly();

        //====================================================================//
        // Is To Ship
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->Identifier("isToShip")
            ->Name("Is To Ship")
            ->MicroData("http://schema.org/OrderStatus", "OrderProcessing")
            ->isReadOnly();

        //====================================================================//
        // Is Canceled
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->Identifier("isCanceled")
            ->Name("Is Canceled")
            ->MicroData("http://schema.org/OrderStatus", "OrderCancelled")
            ->isReadOnly();
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    protected function getStatusFields($key, $fieldName): void
    {
        //====================================================================//
        // READ Fields
        switch ($fieldName) {
            case 'splashStatut':
                $this->out[$fieldName] = StatusTransformer::toSplash($this->object->getStatus());

                break;
            case 'isValidated':
                $this->out[$fieldName] = StatusTransformer::isValidated($this->object->getStatus());

                break;
            case 'isToShip':
                $this->out[$fieldName] = StatusTransformer::isToShip($this->object->getStatus());

                break;
            case 'isCanceled':
                $this->out[$fieldName] = StatusTransformer::isCanceled($this->object->getStatus());

                break;
            default:
                return;
        }

        unset($this->in[$key]);
    }

    /**
     * Write Given Fields
     *
     * @param string $fieldName Field Identifier / Name
     * @param mixed  $fieldData Field Data
     */
    protected function setStatusFields($fieldName, $fieldData): void
    {
        //====================================================================//
        // WRITE Field
        switch ($fieldName) {
            case 'splashStatut':
                $reStatus = StatusTransformer::toReCommerce($fieldData);
                //====================================================================//
                // COMPARE STATUS
                if (empty($reStatus) || ($reStatus == $this->object->getStatus())) {
                    break;
                }
                //====================================================================//
                // CHECK IF UPDATE ALLOWED
                if (!$this->connector->isSandbox() && !StatusTransformer::isAllowedUpdates($reStatus)) {
                    break;
                }
                //====================================================================//
                // UPDATE SHIPMENT STATUS
                $uri = $this->visitor->getItemUri((string) $this->getObjectIdentifier());
                $uri .= "/status/".$reStatus;
                $body = array(
                    "reasonMessage" => "Updated by Splash"
                );
                if (!$this->getVisitor()->getConnexion()->patch($uri, $body)) {
                    Splash::log()->err("An error occurred while updating Shipment Status");

                    return;
                }

                break;
            default:
                return;
        }
        unset($this->in[$fieldName]);
    }
}