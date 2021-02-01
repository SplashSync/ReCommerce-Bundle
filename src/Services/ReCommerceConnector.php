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

namespace Splash\Connectors\ReCommerce\Services;

use ArrayObject;
use Exception;
use Splash\Bundle\Interfaces\Connectors\TrackingInterface;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Bundle\Models\Connectors\GenericObjectMapperTrait;
use Splash\Bundle\Models\Connectors\GenericWidgetMapperTrait;
use Splash\Connectors\ReCommerce\Form\EditFormType;
use Splash\Connectors\ReCommerce\Models\Api;
use Splash\Core\SplashCore as Splash;
use Splash\OpenApi\Action;
use Splash\OpenApi\Client;
use Splash\OpenApi\Connexion\JsonConnexion;
use Splash\OpenApi\Connexion\JsonHalConnexion;
use Splash\OpenApi\Helpers\ApiObjectVisitor;
use Splash\OpenApi\Hydrator\Hydrator;
use Splash\OpenApi\Models\Connexion\ConnexionInterface;

/**
 * ReCommerce REST API Connector for Splash
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class ReCommerceConnector extends AbstractConnector implements TrackingInterface
{
    use GenericObjectMapperTrait;
    use GenericWidgetMapperTrait;

    /**
     * Objects Type Class Map
     *
     * @var array
     */
    protected static $objectsMap = array(
        "Order" => "Splash\\Connectors\\ReCommerce\\Objects\\Order",
    );

    /**
     * Widgets Type Class Map
     *
     * @var array
     */
    protected static $widgetsMap = array(
        "SelfTest" => "Splash\\Connectors\\ReCommerce\\Widgets\\SelfTest",
    );

    /**
     * @var ConnexionInterface
     */
    private $connexion;

    /**
     * Object Hydrator
     *
     * @var Hydrator
     */
    private $hydrator;

    /**
     * @var string
     */
    private $metaDir;

    /**
     * Setup Cache Dir for Metadata
     */
    public function setMetaDir(string $metaDir) : void
    {
        $this->metaDir = $metaDir."/metadata/recommerce";
    }

    /**
     * {@inheritdoc}
     */
    public function ping() : bool
    {
        //====================================================================//
        // Safety Check => Verify Self-test Pass
        if (!$this->selfTest()) {
            return false;
        }
        //====================================================================//
        // Perform Ping Test
        $ping = new Action\Ping($this->getConnexion(), "/product-code-type");

        return $ping->isSuccessful();
    }

    /**
     * {@inheritdoc}
     */
    public function connect() : bool
    {
        //====================================================================//
        // Safety Check => Verify Self-test Pass
        if (!$this->selfTest()) {
            return false;
        }
        //====================================================================//
        // Perform Connect Test
        $connect = new Action\Connect($this->getConnexion(), "/product-code-type");

        return $connect->isSuccessful();
    }

    /**
     * {@inheritdoc}
     */
    public function informations(ArrayObject  $informations) : ArrayObject
    {
        //====================================================================//
        // Server General Description
        $informations->shortdesc = "ReCommerce API";
        $informations->longdesc = "Splash Integration for ReCommerce OpenApi V2.8";
        //====================================================================//
        // Company Informations
        $informations->company = "ReCommerce";
        $informations->address = "54 avenue Lénine";
        $informations->zip = "94250";
        $informations->town = "Gentilly";
        $informations->country = "France";
        $informations->www = "https://www.recommerce.com/";
        $informations->email = "commercial@optilog-fr.com";
        $informations->phone = "+33 (0) 1 57 21 71 52";
        //====================================================================//
        // Server Logo & Ico
        $informations->icoraw = Splash::file()->readFileContents(
            dirname(dirname(__FILE__))."/Resources/public/img/ReCommerce-Ico.png"
        );
        $informations->logourl = null;
        $informations->logoraw = Splash::file()->readFileContents(
            dirname(dirname(__FILE__))."/Resources/public/img/ReCommerce-Ico.png"
        );
        //====================================================================//
        // Server Informations
        $informations->servertype = "ReCommerce Api V2.8";
        $informations->serverurl = "www.recommerce.com";
        //====================================================================//
        // Module Informations
        $informations->moduleauthor = "Splash Official <www.splashsync.com>";
        $informations->moduleversion = "master";

        return $informations;
    }

    /**
     * {@inheritdoc}
     */
    public function selfTest() : bool
    {
        $config = $this->getConfiguration();
        //====================================================================//
        // Verify Webservice Url is Set
        //====================================================================//
        if (!isset($config["WsHost"]) || empty($config["WsHost"]) || !is_string($config["WsHost"])) {
            Splash::log()->err("Webservice Host is Invalid");

            return false;
        }
        //====================================================================//
        // Verify Api Key is Set
        //====================================================================//
        if (!isset($config["ApiKey"]) || empty($config["ApiKey"]) || !is_string($config["ApiKey"])) {
            Splash::log()->err("Api Key is Invalid");

            return false;
        }

        return true;
    }

    //====================================================================//
    // Files Interfaces
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public function getFile(string $filePath, string $fileMd5)
    {
        //====================================================================//
        // Safety Check => Verify Self-test Pass
        if (!$this->selfTest()) {
            return false;
        }

        Splash::log()->err("There are No Files Reading for ReCommerce Up To Now!");

        return false;
    }

    //====================================================================//
    // Profile Interfaces
    //====================================================================//

    /**
     * Get Connector Profile Informations
     *
     * @return array
     */
    public function getProfile() : array
    {
        return array(
            'enabled' => true,                                      // is Connector Enabled
            'beta' => false,                                        // is this a Beta release
            'type' => self::TYPE_HIDDEN,                            // Connector Type or Mode
            'name' => 'recommerce',                                 // Connector code (lowercase, no space allowed)
            'connector' => 'splash.connectors.recommerce',          // Connector Symfony Service
            'title' => 'profile.card.title',                        // Public short name
            'label' => 'profile.card.label',                        // Public long name
            'domain' => 'ReCommerceBundle',                         // Translation domain for names
            'ico' => '/bundles/recommerce/img/ReCommerce-Ico.png',  // Public Icon path
            'www' => 'https://www.recommerce.com',                  // Website Url
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectedTemplate() : string
    {
        return "@ReCommerce/Profile/connected.html.twig";
    }

    /**
     * {@inheritdoc}
     */
    public function getOfflineTemplate() : string
    {
        return "@ReCommerce/Profile/offline.html.twig";
    }

    /**
     * {@inheritdoc}
     */
    public function getNewTemplate() : string
    {
        return "@ReCommerce/Profile/new.html.twig";
    }

    /**
     * {@inheritdoc}
     */
    public function getFormBuilderName() : string
    {
        $this->selfTest();

        return EditFormType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getMasterAction()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicActions() : array
    {
        return array(
            "index" => "ReCommerceBundle:WebHooks:index",
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSecuredActions() : array
    {
        return array(
        );
    }

    //====================================================================//
    // Open API Connector Interfaces
    //====================================================================//

    /**
     * Get Connector Api Connexion
     *
     * @return ConnexionInterface
     * @throws Exception
     */
    public function getConnexion() : ConnexionInterface
    {
        //====================================================================//
        // Connexion already created
        if (isset($this->connexion)) {
            return $this->connexion;
        }
        //====================================================================//
        // Safety check
        if (!$this->selfTest()) {
            throw new Exception("Self-test fails... Unable to create API Connexion!");
        }
        $config = $this->getConfiguration();
        //====================================================================//
        // Setup Api Connexion
        $this->connexion = new JsonHalConnexion(
            $config["WsHost"],
            array('api-key' => $config["ApiKey"])
        );

        return $this->connexion;
    }

    /**
     * @return Hydrator
     */
    public function getHydrator(): Hydrator
    {
        //====================================================================//
        // Configure Object Hydrator
        if (!isset($this->hydrator)) {
            $this->hydrator = new Hydrator($this->metaDir);
        }


        return $this->hydrator;
    }
}
