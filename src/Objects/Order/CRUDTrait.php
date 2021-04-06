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

use Splash\Connectors\ReCommerce\DataTransformer\AssetTransformer;
use Splash\OpenApi\Models\Objects\CRUDTrait as OpenApiCRUDTrait;

/**
 * ReCommerce Orders CRUD Functions
 */
trait CRUDTrait
{
    use OpenApiCRUDTrait{
        OpenApiCRUDTrait::load as coreLoad;
        OpenApiCRUDTrait::update as coreUpdate;
    }

    /**
     * {@inheritDoc}
     */
    public function load($objectId)
    {
        //====================================================================//
        // Execute Core Action
        $response = $this->coreLoad($objectId);
        if (false == $response) {
            return false;
        }
        //====================================================================//
        // Setup Assets Transformer
        AssetTransformer::configure($this->getVisitor(), $objectId);
        //====================================================================//
        // Return Hydrated Object
        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function update(bool $needed)
    {
        //====================================================================//
        // Execute Core Action
        $response = $this->coreUpdate($needed);
        if (false == $response) {
            return false;
        }
        //====================================================================//
        // Execute Post Update Status Changes
        $this->postUpdateStatus();

        return $response;
    }
}
