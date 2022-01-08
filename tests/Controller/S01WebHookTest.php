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

namespace Splash\Connectors\ReCommerce\Test\Controller;

use Exception;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Connectors\ReCommerce\Services\ReCommerceConnector;
use Splash\Tests\Tools\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Test of ReCommerce Connector WebHook Controller
 */
class S01WebHookTest extends TestCase
{
    /**
     * Connector Server ID
     */
    const CONNECTOR = 'ThisIsSandBoxWsId';

    /**
     * Connector Webhook Action
     */
    const ACTION = 'webhook';

    /**
     * Test Connector Loading
     *
     * @throws Exception
     */
    public function testConnectorLoading(): void
    {
        //====================================================================//
        // Load Connector
        $connector = $this->getConnector(self::CONNECTOR);
        $this->assertInstanceOf(ReCommerceConnector::class, $connector);
    }

    /**
     * Test WebHook HTTP Methods
     *
     * @throws Exception
     */
    public function testWebhookMethods(): void
    {
        //====================================================================//
        // Load Connector
        $connector = $this->getConnector(self::CONNECTOR);
        $this->assertInstanceOf(ReCommerceConnector::class, $connector);

        //====================================================================//
        // PING -> OK
        $this->assertPublicActionWorks($connector, null, array(), "POST");
        $this->assertNotEmpty($this->getResponseContents());
        //====================================================================//
        // POST -> FORBIDDEN
        $this->assertPublicActionFail($connector, self::ACTION, array(), "POST");
        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $this->getResponseCode());
        //====================================================================//
        // GET -> BAD_REQUEST
        $this->assertPublicActionFail($connector, self::ACTION, array(), "GET");
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $this->getResponseCode());
        //====================================================================//
        // PUT -> BAD_REQUEST
        $this->assertPublicActionFail($connector, self::ACTION, array(), "PUT");
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $this->getResponseCode());
        //====================================================================//
        // PATCH -> BAD_REQUEST
        $this->assertPublicActionFail($connector, self::ACTION, array(), "PATCH");
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $this->getResponseCode());
        //====================================================================//
        // DELETE -> BAD_REQUEST
        $this->assertPublicActionFail($connector, self::ACTION, array(), "DELETE");
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $this->getResponseCode());

        //====================================================================//
        // Wrong Key
        //====================================================================//

        $this->getTestClient()->setServerParameter("HTTP_api-key", "This-Key-Is-Wrong");
        $this->assertPublicActionFail($connector, self::ACTION, array(), "POST");
        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $this->getResponseCode());
    }

    /**
     * Test WebHook with Errors
     *
     * @throws Exception
     */
    public function testWebhookErrors(): void
    {
        //====================================================================//
        // Load Connector
        $connector = $this->getConnector(self::CONNECTOR);
        $this->assertInstanceOf(ReCommerceConnector::class, $connector);
        //====================================================================//
        // Setup Client
        $this->configure($connector);

        //====================================================================//
        // Empty Contents
        //====================================================================//

        $this->assertPublicActionFail($connector, self::ACTION, array(), "POST");
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $this->getResponseCode());

        //====================================================================//
        // Partial Contents
        //====================================================================//

        $partial = array("commit-items" => array());
        $this->assertPublicActionFail($connector, self::ACTION, $partial, "POST");
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $this->getResponseCode());

        //====================================================================//
        // No Type Contents
        //====================================================================//

        $noType = array("commit-items" => array(array(
            "ids" => array(uniqid(), uniqid(), uniqid())
        )));
        $this->assertPublicActionFail($connector, self::ACTION, $noType, "POST");
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $this->getResponseCode());

        //====================================================================//
        // No Ids Contents
        //====================================================================//

        $noIds = array("commit-items" => array(array(
            "type" => "Order"
        )));
        $this->assertPublicActionFail($connector, self::ACTION, $noIds, "POST");
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $this->getResponseCode());

        //====================================================================//
        // Wrong Ids Contents
        //====================================================================//

        $wrongIds = array("commit-items" => array(array(
            "type" => "Order",
            "ids" => uniqid(),
        )));
        $this->assertPublicActionFail($connector, self::ACTION, $wrongIds, "POST");
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $this->getResponseCode());
    }

    /**
     * Test WebHook Updates
     *
     * @dataProvider webHooksInputsProvider
     *
     * @param array  $data
     * @param string $objectType
     * @param string $action
     * @param string $objectId
     *
     * @throws Exception
     *
     * @return void
     */
    public function testWebhookOkRequest(
        array $data,
        string $objectType,
        string $action,
        string $objectId
    ): void {
        //====================================================================//
        // Load Connector
        $connector = $this->getConnector(self::CONNECTOR);
        //====================================================================//
        // Setup Client
        $this->configure($connector);

        //====================================================================//
        // POST MODE
        $this->assertPublicActionWorks($connector, self::ACTION, $data, "POST");
        $this->assertEquals(JsonResponse::HTTP_OK, $this->getResponseCode());
        $this->assertIsLastCommitted($action, $objectType, $objectId);

        //====================================================================//
        // JSON POST MODE
        $this->assertPublicActionWorks($connector, self::ACTION, $data, "JSON");
        $this->assertEquals(JsonResponse::HTTP_OK, $this->getResponseCode());
        $this->assertIsLastCommitted($action, $objectType, $objectId);
    }

    /**
     * Generate Fake Inputs for WebHook Requests
     *
     * @return array
     */
    public function webHooksInputsProvider(): array
    {
        $hooks = array();

        for ($i = 0; $i < 50; $i++) {
            //====================================================================//
            // Add Order WebHook Test
            $hooks[] = self::getOrderWebHook(SPL_A_UPDATE);
        }

        return $hooks;
    }

    /**
     * Configure Client Headers for Requests
     *
     * @param AbstractConnector $connector
     *
     * @return void
     */
    private function configure(AbstractConnector $connector): void
    {
        $this->getTestClient()->setServerParameter("HTTP_api-key", $connector->getParameter("ApiKey"));
    }

    /**
     * Generate Fake Order Inputs for WebHook Requests
     *
     * @param string $action
     *
     * @return array
     */
    private static function getOrderWebHook(string $action) : array
    {
        $orderId = uniqid();

        return array(
            array("commit-item" => array(array(
                "type" => "Order",
                "ids" => array($orderId),
                "user" => "PhpUnit",
                "reason" => "ReCommerce Connector Unit Tests"
            ))),
            "Order",
            $action,
            $orderId,
        );
    }

    /**
     * Get Framework Client Response Code.
     *
     * @return int
     */
    private function getResponseCode() : int
    {
        $jsonResponse = $this->getResponseContents();
        $this->assertIsString($jsonResponse);
        $response = json_decode($jsonResponse, true);
        $this->assertIsArray($response);
        $this->assertArrayHasKey("code", $response);
        $this->assertIsInt($response["code"]);

        return $response["code"];
    }
}
