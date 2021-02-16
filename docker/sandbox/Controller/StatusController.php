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

namespace App\Controller;

use App\Entity\Shipment;
use App\Entity\ShipmentStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validation;

/**
 * Order Status Controller
 */
class StatusController extends AbstractController
{
    /**
     * Update Shipment Status via Patch Operation
     *
     * @param int            $id
     * @param string         $status
     * @param ShipmentStatus $data
     *
     * @return JsonResponse
     */
    public function indexAction(int $id, string $status, ShipmentStatus $data): JsonResponse
    {
        //====================================================================//
        // Find Shipment By Id
        /** @var null|Shipment $shipment */
        $shipment = $this->getDoctrine()->getManager()->find(Shipment::class, $id);
        if (!$shipment) {
            return new JsonResponse(array("response" => "Not Found"), 404);
        }
        //====================================================================//
        // Validate Reason
        if (empty($data->reasonMessage) || !is_string($data->reasonMessage)) {
            return new JsonResponse(array("response" => "No Reason Provided"), 406);
        }
        //====================================================================//
        // Update Shipment Status
        $shipment->status = $status;
        //====================================================================//
        // Validate Shipment Status
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();
        $errors = $validator->validateProperty($shipment, "status");
        if (count($errors) > 0) {
            return new JsonResponse(array("response" => (string) $errors), 406);
        }
        //====================================================================//
        // Save
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(array("response" => "Ok => ".$status));
    }
}
