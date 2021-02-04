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

namespace Splash\Connectors\ReCommerce\Objects;

//use Splash\Bundle\Interfaces\Objects\TrackingInterface;
use Exception;
use Splash\Bundle\Models\AbstractStandaloneObject;
use Splash\Client\Splash;
use Splash\Connectors\ReCommerce\Models\Api\Shipment;
use Splash\Connectors\ReCommerce\Services\ReCommerceConnector;
use Splash\Models\Objects\IntelParserTrait;
use Splash\Models\Objects\ListsTrait;
use Splash\Models\Objects\ObjectsTrait;
use Splash\Models\Objects\SimpleFieldsTrait;
use Splash\Connectors\ReCommerce\Models\Api;
//use Splash\OpenApi\Helpers\Descriptor;
//use Splash\OpenApi\Helpers\Reader;
//use Splash\OpenApi\Helpers\ApiObjectVisitor;
use Splash\OpenApi\Hydrator\Hydrator;
use Splash\OpenApi\Visitor\JsonHalVisitor;
//use Splash\OpenApi\Models\AbstractApiObject;
use Splash\OpenApi\Models\Connexion\ConnexionInterface as Connexion;
use Splash\OpenApi\Models\Objects as ApiModels;
//use Splash\OpenApi\Models\OpenApiAwareInterface;
//use Splash\OpenApi\Models\OpenApiAwareTrait;

use Splash\OpenApi\Fields as ApiFields;
use Splash\OpenApi\Visitor\AbstractVisitor as Visitor;
use Splash\OpenApi\Visitor\JsonVisitor;

/**
 * Optilog Implementation of Customers Orders
 */
class Order extends AbstractStandaloneObject
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
    use ApiModels\ObjectsListTrait;
    use ApiModels\SimpleFieldsTrait;
    use ApiModels\ListFieldsTrait;

    //====================================================================//
    // ReCommerce Order Traits


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
     * @throws Exception
     */
    public function __construct(ReCommerceConnector $parentConnector)
    {
        $this->connector = $parentConnector;
        //====================================================================//
        // Connect Open Api Interfaces
        $this->model = Api\Shipment::class;
        $this->connexion = $parentConnector->getConnexion();
        $this->hydrator = $parentConnector->getHydrator();
        //====================================================================//
        //  Load Translation File
        Splash::translator()->load('local');
        //====================================================================//
        // Prepare Api Visitor
        $this->getVisitor();
        ApiFields\Descriptor::load($this->connector->getHydrator(), $this->model);
    }

    /**
     * Get Shipment API Visitor
     *
     * @throws Exception
     */
    public function getVisitor(): Visitor
    {
        if (!isset($this->visitor)) {
            $this->visitor =  new JsonHalVisitor(
                $this->connector->getConnexion(),
                $this->connector->getHydrator(),
                Api\Shipment::class
            );
            $this->visitor->setModel(Api\Shipment::class,"/shipment","/shipment/{id}");
        }

        return $this->visitor;
    }
}
