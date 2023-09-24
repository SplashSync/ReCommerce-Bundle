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

namespace Splash\Connectors\ReCommerce\Objects\Order;

use Exception;
use Splash\Connectors\ReCommerce\DataTransformer\AssetTransformer;
use Splash\Connectors\ReCommerce\Models\Api\Shipment;
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
     *
     * @throws Exception
     */
    public function load(string $objectId): ?Shipment
    {
        //====================================================================//
        // Execute Core Action
        $response = $this->coreLoad($objectId);
        if (!$response instanceof Shipment) {
            return null;
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
     *
     * @throws Exception
     */
    public function update(bool $needed): ?string
    {
        //====================================================================//
        // Execute Core Action
        $response = $this->coreUpdate($needed);
        if (!$response) {
            return null;
        }

        //====================================================================//
        // Execute Post Update Status Changes
        return $this->postUpdateStatus() ? $response : null;
    }
}
