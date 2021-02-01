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

namespace Splash\Connectors\ReCommerce\Objects\Order;

use Exception;
use Splash\Connectors\Optilog\Models\RestHelper as API;
use Splash\Core\SplashCore      as Splash;
use stdClass;
use Splash\OpenApi\Fields as ApiFields;

use Splash\OpenApi\Action\Json as JsonActions;

/**
 * ReCommerce Orders CRUD Functions
 */
trait CRUDTrait
{
    /**
     * Load Request Object
     *
     * @param string $objectId Object id
     *
     * @return false|stdClass
     */
    public function load($objectId)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Perform Connect Test
        $loadRequest = new \Splash\OpenApi\Action\JsonHal\Get($this, "/shipment/".$objectId);

dump($loadRequest->getResults());

        return $loadRequest->getResults();
    }

    /**
     * Create Request Object
     *
     * @return false|stdClass New Object
     * @throws Exception
     */
    public function create()
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Collect Required Fields
        $newObject = ApiFields\Getter::getRequiredFields($this, (object) $this->in);
        if (!$newObject) {
            return False;
        }
        //====================================================================//
        // Create Remote Object
        $createRequest = new JsonActions\Post($this, "/shipment", $newObject, true);
        //====================================================================//
        // Create Remote Object
        if (!$createRequest->isSuccessful() ) {
            return False;
        }
        $model = $this->getModel();
        $object = $createRequest->getResults();

        return ($object instanceof $model) ? $object : false;

    }

    /**
     * Update Request Object
     *
     * @param bool $needed Is This Update Needed
     *
     * @return false|string Object Id of False if Failed to Update
     */
    public function update(bool $needed)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // No Update Required
        if (!$needed) {
            return $this->getObjectIdentifier();
        }
        //====================================================================//
        // Update Remote Object
        $updateRequest = new JsonActions\Patch($this, $this->getItemUrl(), $this->object);
        //====================================================================//
        // Update Remote Object
        return $updateRequest->isSuccessful()
            ? $this->getObjectIdentifier()
            : Splash::log()->errTrace(
                "Unable to Update Order (".$this->getObjectIdentifier().")."
            )
        ;
    }

    /**
     * Delete requested Object
     *
     * @param null|string $objectId Object Id
     *
     * @return bool
     */
    public function delete($objectId = null)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Load Remote Object
        $object = $this->load($objectId);
        if (empty($object)) {
            return Splash::log()->warTrace("Trying to Delete an Unknown Order (".$objectId.").");
        }
        //====================================================================//
        // Update Remote Object
        $this->object = $object;
        $deleteRequest = new JsonActions\Delete($this, $this->getItemUrl());
        //====================================================================//
        // Update Remote Object
        return $deleteRequest->isSuccessful();
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectIdentifier()
    {
        if (empty($this->object->getId())) {
            return false;
        }

        return $this->object->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getItemUrl()
    {
        if (empty($this->object->getId())) {
            return "/shipment";
        }

        return "/shipment/".$this->object->getId();
    }
}
