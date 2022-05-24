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

namespace Splash\Connectors\ReCommerce\Controller;

use Splash\Bundle\Models\AbstractConnector;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Splash ReCommerce Connector WebHooks Controller
 */
class WebHooksController extends AbstractController
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var int
     */
    private $counter;

    /**
     * Execute WebHook Public Action
     *
     * @param Request           $request
     * @param AbstractConnector $connector
     *
     * @throws BadRequestHttpException
     *
     * @return JsonResponse
     */
    public function indexAction(Request $request, AbstractConnector $connector): JsonResponse
    {
        //==============================================================================
        // Safety Check
        $error = $this->verify($request, $connector);
        if ($error) {
            return $error;
        }

        //====================================================================//
        // Extract Data from Request
        $error = $this->extractData($request);
        if ($error) {
            return $error;
        }

        //==============================================================================
        // Commit Changes
        $error = $this->executeCommits($connector);
        if ($error) {
            return $error;
        }

        return $this->getResponse(
            JsonResponse::HTTP_OK,
            sprintf('%d Changes notified', $this->counter)
        );
        ;
    }

    /**
     * Execute Changes Commits
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *
     * @param AbstractConnector $connector
     *
     * @return null|JsonResponse
     */
    private function executeCommits(AbstractConnector $connector) : ?JsonResponse
    {
        //====================================================================//
        // Walk on Submitted Items
        foreach ($this->data as $item) {
            //====================================================================//
            // Validate Item
            if (!isset($item["type"]) || !in_array($item["type"], array("Order"), true)) {
                return $this->getResponse(JsonResponse::HTTP_BAD_REQUEST, 'Wrong object type');
            }
            if (!isset($item["ids"]) || !is_iterable($item["ids"])) {
                return $this->getResponse(JsonResponse::HTTP_BAD_REQUEST, 'Wrong objects ids');
            }
            //====================================================================//
            // Walk on Item Ids
            foreach ($item["ids"] as $objectId) {
                //==============================================================================
                // Commit Change for Object
                $connector->commit(
                    $item["type"],
                    (string) $objectId,
                    SPL_A_UPDATE,
                    isset($item["user"]) ? (string) $item["user"] : 'Unknown',
                    isset($item["reason"]) ? (string) $item["reason"] : 'No reason privided',
                );
                $this->counter++;
            }
        }

        return null;
    }

    /**
     * Verify Request is Valid
     *
     * @param Request           $request
     * @param AbstractConnector $connector
     *
     * @return null|JsonResponse
     */
    private function verify(Request $request, AbstractConnector $connector) : ?JsonResponse
    {
        //====================================================================//
        // Verify Request is POST
        if (!$request->isMethod('POST')) {
            return $this->getResponse(JsonResponse::HTTP_BAD_REQUEST, 'Only POST method is supported');
        }
        //====================================================================//
        // Verify Api Key is Found
        $apiKey = $request->headers->get("api-key");
        if (empty($apiKey) || !is_string($apiKey)) {
            return $this->getResponse(JsonResponse::HTTP_FORBIDDEN, 'Wrong or empty API Key');
        }
        //====================================================================//
        // Verify Api Key is Setup Locally
        $config = $connector->getConfiguration();
        if (!isset($config["ApiKey"]) || empty($config["ApiKey"]) || !is_string($config["ApiKey"])) {
            return $this->getResponse(JsonResponse::HTTP_FORBIDDEN, 'Wrong or empty API Key');
        }
        //====================================================================//
        // Verify Api Keys are Similar
        $config = $connector->getConfiguration();
        if ($apiKey != $config["ApiKey"]) {
            return $this->getResponse(JsonResponse::HTTP_FORBIDDEN, 'Wrong or empty API Key');
        }

        return null;
    }

    /**
     * Extract Data from Request
     *
     * @param Request $request
     *
     * @return null|JsonResponse
     */
    private function extractData(Request $request): ?JsonResponse
    {
        /** @var null|array $rawData */
        $rawData = $request->getContent()
            ? json_decode((string) $request->getContent(), true)
            : $request->request->all();

        if (empty($rawData) || !isset($rawData["commit-item"]) || !is_array($rawData["commit-item"])) {
            return $this->getResponse(JsonResponse::HTTP_BAD_REQUEST, 'Malformed or missing data...');
        }

        $this->data = $rawData["commit-item"];

        return null;
    }

    /**
     * @param int   $code
     * @param mixed $message
     *
     * @return JsonResponse
     */
    private function getResponse($code, $message): JsonResponse
    {
        return new JsonResponse(
            array(
                'code' => $code,
                'type' => JsonResponse::$statusTexts[$code],
                'message' => $message,
            ),
            $code
        );
    }
}
