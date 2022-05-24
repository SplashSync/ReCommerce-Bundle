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

namespace Splash\Connectors\ReCommerce\Objects;

use Exception;
use Splash\Bundle\Interfaces\Objects\TrackingInterface;
use Splash\Bundle\Models\AbstractStandaloneObject;
use Splash\Client\Splash;
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
    use ApiModels\SimpleFieldsTrait;
    use ApiModels\ListFieldsGetTrait;
    use ApiModels\ObjectsListTrait;

    //====================================================================//
    // ReCommerce Order Traits
    use Order\CRUDTrait;
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
    protected static string $name = "Customer Order";

    /**
     * {@inheritdoc}
     */
    protected static string $description = "ReCommerce Order Object";

    /**
     * {@inheritdoc}
     */
    protected static string $ico = "fa fa-shopping-cart";

    //====================================================================//
    // General Class Variables
    //====================================================================//

    /**
     * {@inheritdoc}
     *
     * @phpstan-var array<string, null|array<string, null|array|scalar|Api\Box|Api\TransportUnit>|scalar>
     */
    protected array $in;

    /**
     * @phpstan-var  Shipment
     */
    protected object $object;

    /**
     * Open Api Shipment Visitor
     *
     * @var Visitor
     */
    protected Visitor $visitor;

    /**
     * @var ReCommerceConnector
     */
    protected ReCommerceConnector $connector;

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
            static::$allowPushCreated = false;
            static::$allowPushDeleted = false;
            static::$enablePushCreated = false;
            static::$enablePushDeleted = false;
        }

        return parent::description();
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
