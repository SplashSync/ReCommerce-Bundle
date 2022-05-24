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
use Splash\Models\Objects\Order\Status;

class StatusTransformer
{
    /**
     * List of Optilog Orders Status for Splash
     *
     * Negative Status >> NOT Send to Optilog
     *
     * @var array
     */
    const SPLASH = array(
        //====================================================================//
        // Real ReCommerce Statuses
        "failed" => Status::PROBLEM,            // In Error
        "draft" => Status::DRAFT,               // Draft
        "pending" => Status::PAYMENT_DUE,       // Pending
        "processing" => Status::PROCESSING,     // Processing, Shipment has been taken into account.
        "processed" => Status::PROCESSED,       // Processed, Shipment has been well prepared.
        "ready-to-ship" => Status::PICKUP,      // Ready, Shipment is ready to be shipped, but is still not to ship.
        "to-ship" => Status::TO_SHIP,           // To Ship, Shipment has to be shipped.
        "shipped" => Status::IN_TRANSIT,        // Shipped, Shipment has been shipped.
        "shipping" => Status::IN_TRANSIT,       // Shipping, Shipment has been taken into account and being prepared.
        "closed" => Status::DELIVERED,          // Closed, Shipment is complete, and stock has been updated.
        "cancelled" => Status::CANCELED,        // Cancelled.
    );

    /**
     * Get All Available Splash Status
     *
     * @return array
     */
    public static function getAll(): array
    {
        $allStatuses = Status::getAllChoices(true);
        if (Splash::isDebugMode()) {
            unset(
                $allStatuses[Status::RETURNED],
                $allStatuses[Status::PAYMENT_DUE],
                $allStatuses[Status::OUT_OF_STOCK]
            );
        }

        return $allStatuses;
    }

    /**
     * Convert Raw ReCommerce Status Id to Splash Status
     *
     * @param string $status
     *
     * @return string
     */
    public static function toSplash(string $status): string
    {
        return isset(self::SPLASH[$status])
            ? self::SPLASH[$status]
            : "Unknown"
        ;
    }

    /**
     * Convert Splash Status to ReCommerce Status ID
     *
     * @param string $status
     *
     * @return null|string
     */
    public static function toReCommerce(string $status): ?string
    {
        $index = array_search($status, self::SPLASH, true);
        if (false === $index) {
            return null;
        }

        return (string) $index;
    }

    /**
     * Check if Order Status Code is Validated
     *
     * @param string $reStatus ReCommerce Order Status Code
     *
     * @return bool
     */
    public static function isValidated(string $reStatus): bool
    {
        return ("pending" == $reStatus);
    }

    /**
     * Check if Order Status Code is Canceled
     *
     * @param string $reStatus ReCommerce Order Status Code
     *
     * @return bool
     */
    public static function isToShip(string $reStatus): bool
    {
        return in_array($reStatus, array("to-ship", "toShip", "processed"), true);
    }

    /**
     * Check if Order Status Code is Canceled
     *
     * @param string $status Order Status Code
     *
     * @return bool
     */
    public static function isCanceled(string $status): bool
    {
        return Status::isCanceled(self::toSplash($status));
    }

    /**
     * Check if Order Status Code is Updated by Splash
     *
     * @param string $reStatus Order Status Code
     *
     * @return bool
     */
    public static function isAllowedUpdates(string $reStatus): bool
    {
        return in_array($reStatus, array(
            "failed",               // In Error
            "processing",           // Processing, Shipment has been taken into account and is being prepared.
            "processed",            // Processed, Shipment has been well prepared.
            "ready-to-ship",        // Ready, Shipment is ready to be shipped, but is still not to ship.
            "shipping",             // Shipping, Shipment has been taken into account and being prepared for shipping.
            "shipped",              // Shipped, Shipment has been shipped.
            "closed",               // Closed, Shipment is complete, and stock has been updated in other platforms.
            "cancelled",            // Cancelled.
        ), true);
    }
}
