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

trait TrackingTrait
{
    /**
     * @var string[][]
     */
    private static $trackedStatuses = array(
        array("status" => "pending"),
        array("status" => "toShip"),
        array(
            "status" => "processed",
            "transportUnitTypeLabel" => "pallet",
            "salesChannelLabel" => "readyMadeBox",
        )
    );

    /**
     * {@inheritDoc}
     */
    public function getTrackingDelay(): int
    {
        return 360;
    }

    /**
     * {@inheritDoc}
     */
    public function getUpdatedIds(): array
    {
        $visitor = clone $this->getVisitor();
        $orderIds = array();
        //====================================================================//
        // Walk on tracked Order Statuses
        foreach (self::$trackedStatuses as $trackedStatus) {
            //====================================================================//
            // Build List Request Parameters
            $queryArgs = array(
                "max" => 50,
                "extraArgs" => $trackedStatus
            );
            //====================================================================//
            // Load List of Orders from API
            $listResponse = $visitor->list(null, $queryArgs);
            if (!$listResponse->isSuccess()) {
                continue;
            }
            //====================================================================//
            // Load List of Orders from API
            $orderIds = array_merge(
                $orderIds,
                self::extractOrderIds($listResponse->getResults())
            );
        }

        return array_values($orderIds);
    }

    /**
     * {@inheritDoc}
     */
    public function getDeletedIds(): array
    {
        return array();
    }

    /**
     * Extract Orders Ids from Raw List Response
     *
     * @param array $rawList
     *
     * @return array
     */
    private function extractOrderIds(array $rawList): array
    {
        $orderIds = array();
        //====================================================================//
        // Remove meta item
        if (isset($rawList["meta"])) {
            unset($rawList["meta"]);
        }
        //====================================================================//
        // Walk on List Results
        foreach ($rawList as $listItem) {
            $orderIds[$listItem["id"]] = $listItem["id"];
        }

        return $orderIds;
    }
}
