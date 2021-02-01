<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2020 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace   Splash\Connectors\ReCommerce\Objects\Order;

use Splash\Bundle\Helpers\Objects\CachedListHelper;
use Splash\Client\Splash;
use Splash\Connectors\Optilog\Models\RestHelper as API;
use Splash\Connectors\Optilog\Models\StatusCodes;
use Splash\OpenApi\Action\Json as JsonActions;
use Splash\OpenApi\Action\JsonHal as ApiActions;

/**
 * ReCommerce Orders Objects List Functions
 */
trait ObjectsListTrait
{
    /**
     * {@inheritdoc}
     *
     * @note Order Listing Always uses API V1. "*" search doesn't work on API V2.
     */
    public function objectsList($filter = null, $params = null)
    {
        $filters = array();
        //====================================================================//
        // Setup Pagination
        if (!empty($filter)) {
            $filters['order'] = $filter;
        }
        //====================================================================//
        // Setup Pagination
        if (!empty($params["max"])) {
            $filters['limit'] = $params["max"];
        }
        if (!empty($params["offset"])) {
            $filters['page'] = 1 + (int) ($params["offset"] / $params["max"]);
        }
        //====================================================================//
        // Perform List Request
        $connect = new ApiActions\Collection($this, "/shipment", $filters);

        return $connect->getResults();
    }
}
