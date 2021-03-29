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

namespace Splash\Connectors\ReCommerce\Objects;

use Exception;
use Splash\Bundle\Interfaces\Objects\TrackingInterface;
use Splash\Bundle\Models\AbstractStandaloneObject;
use Splash\Client\Splash;
use Splash\Connectors\ReCommerce\DataTransformer\AssetTransformer;
use Splash\Connectors\ReCommerce\Models\Api;
use Splash\Connectors\ReCommerce\Models\Api\Shipment;
use Splash\Connectors\ReCommerce\Services\ReCommerceConnector;
use Splash\Models\Objects\IntelParserTrait;
use Splash\Models\Objects\ListsTrait;
use Splash\Models\Objects\ObjectsTrait;
use Splash\Models\Objects\SimpleFieldsTrait;
use Splash\OpenApi\Action\JsonHal;
use Splash\OpenApi\Models\Objects as ApiModels;
use Splash\OpenApi\Visitor\AbstractVisitor as Visitor;
use Splash\OpenApi\Visitor\JsonHalVisitor;
use stdClass;

/**
 * Optilog Implementation of Customers Orders
 */
class Order extends AbstractStandaloneObject implements TrackingInterface
{
    //====================================================================//
    // Splash Php Core Traits
    use IntelParserTrait;
    use SimpleFieldsTrait;
    use ObjectsTrait;
    use ListsTrait;

    //====================================================================//
    // OpenApi Traits
    use ApiModels\CRUDTrait;
    use ApiModels\SimpleFieldsTrait;
    use ApiModels\ListFieldsGetTrait;
    use ApiModels\ObjectsListTrait;

    //====================================================================//
    // ReCommerce Order Traits
    use Order\StatusTrait;
    use Order\BoxesTrait;
    use Order\BoxesLinesTrait;
    use Order\TransportUnitsTrait;
    use Order\ParcelsTrait;
    use Order\TrackingTrait;

    //====================================================================//
    // Object Definition Parameters
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    protected static $NAME = "Customer Order";

    /**
     * {@inheritdoc}
     */
    protected static $DESCRIPTION = "ReCommerce Order Object";

    /**
     * {@inheritdoc}
     */
    protected static $ICO = "fa fa-shopping-cart";

    //====================================================================//
    // General Class Variables
    //====================================================================//

    /**
     * @var Shipment
     */
    protected $object;

    /**
     * Open Api Shipment Visitor
     *
     * @var Visitor
     */
    protected $visitor;

    /**
     * @var ReCommerceConnector
     */
    protected $connector;

    /**
     * Class Constructor
     *
     * @param ReCommerceConnector $parentConnector
     *
     * @throws Exception
     */
    public function __construct(ReCommerceConnector $parentConnector)
    {
        $this->connector = $parentConnector;
        //====================================================================//
        //  Load Translation File
        Splash::translator()->load('local');
        //====================================================================//
        // Prepare Api Visitor
        $this->getVisitor();
    }

    /**
     * {@inheritdoc}
     */
    public function description(): array
    {
        if (!$this->connector->isSandbox()) {
            static::$ALLOW_PUSH_CREATED = false;
            static::$ALLOW_PUSH_DELETED = false;
            static::$ENABLE_PUSH_CREATED = false;
            static::$ENABLE_PUSH_DELETED = false;
        }

        return parent::description();
    }

    /**
     * Load Request Object
     *
     * @param string $objectId Object id
     *
     * @throws Exception
     *
     * @return false|stdClass
     */
    public function load($objectId)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Load Remote Object
        $loadResponse = $this->visitor->load($objectId);
        if (!$loadResponse->isSuccess()) {
            return false;
        }
        //====================================================================//
        // Setup Assets Transformer
        AssetTransformer::configure($this->getVisitor(), $objectId);
        //====================================================================//
        // Return Hydrated Object
        return $loadResponse->getResults();
    }

    /**
     * Get Shipment API Visitor
     *
     * @throws Exception
     */
    public function getVisitor(): Visitor
    {
        if (!isset($this->visitor)) {
            $this->visitor = new JsonHalVisitor(
                $this->connector->getConnexion(),
                $this->connector->getHydrator(),
                Api\Shipment::class
            );
            $this->visitor->setModel(
                Api\Shipment::class,
                "/shipment",
                "/shipment/{id}",
                array("id", "boxes", "transportUnits", "parcels")
            );
            $this->visitor->setListAction(
                JsonHal\ListAction::class,
                array("filterKey" => "order")
            );
        }

        return $this->visitor;
    }
}
