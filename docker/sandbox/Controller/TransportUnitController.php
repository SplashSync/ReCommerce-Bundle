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

namespace App\Controller;

use App\Entity\Box;
use App\Entity\Shipment;
use App\Entity\TransportUnit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Transport Unit Controller: Custom operations to work with Shipment's Boxes
 */
class TransportUnitController extends AbstractController
{
    /**
     * Get List of Transport Units for a Shipment
     *
     * @param Request $request
     * @param int     $shipment
     *
     * @return JsonResponse
     */
    public function listAction(Request $request, int $shipment): JsonResponse
    {
        $limit = $request->get("limit");
        $page = $request->get("page");

        $results = $this->getDoctrine()->getManager()->getRepository(TransportUnit::class)->findBy(
            array("shipment" => $shipment),
            null,
            $limit,
            $page ? ($page - 1) * $limit : null,
        );

        $total = count($this->getDoctrine()->getManager()->getRepository(TransportUnit::class)->findBy(array(
            "shipment" => $shipment
        )));

        return new JsonResponse(array(
            "_links" => array(),
            "_embedded" => array(
                "transportUnit" => $this->get('serializer')->normalize($results)
            ),
            "page_count" => $page ? 1 + ((int) ($total / $limit)): 1,
            "total_items" => $total,
            "page" => $page ?: 1,
        ));
    }

    /**
     * Add Transport Unit to a Shipment
     *
     * @param int           $shipment
     * @param TransportUnit $data
     *
     * @return JsonResponse
     */
    public function postAction(int $shipment, TransportUnit $data): JsonResponse
    {
        //====================================================================//
        // Identify Parent Shipment
        /** @var null|Shipment $parent */
        $parent = $this->getDoctrine()->getManager()
            ->getRepository(Shipment::class)
            ->find($shipment);
        if (!$parent) {
            throw new NotFoundHttpException();
        }
        $data->shipment = $parent;
        //====================================================================//
        // Identify Boxes
        foreach ($data->boxes as $index => $boxArray) {
            $box = null;
            if (isset($boxArray['boxName'])) {
                /** @var null|Box $box */
                $box = $this->getDoctrine()->getManager()->getRepository(Box::class)->findOneBy(array(
                    "shipment" => $shipment,
                    "name" => $boxArray['boxName'],
                ));
            }
            if ($box) {
                $box->transportUnit = $data;
                $data->box->add($box);
            }
        }
        //====================================================================//
        // Save to Database
        $this->getDoctrine()->getManager()->persist($data);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse($this->get('serializer')->normalize($data));
    }

    /**
     * Get Transport Unit by Name for a Shipment
     *
     * @param int    $shipment
     * @param string $id
     *
     * @return JsonResponse
     */
    public function getAction(int $shipment, string $id): JsonResponse
    {
        /** @var null|TransportUnit $unit */
        $unit = $this->getDoctrine()->getManager()->getRepository(TransportUnit::class)->findOneBy(array(
            "shipment" => $shipment,
            "id" => $id,
        ));

        if (!$unit) {
            throw new NotFoundHttpException();
        }

        //====================================================================//
        // Identify Boxes
        $boxesArray = array();
        foreach ($unit->box as $box) {
            $boxesArray[] = $this->get('serializer')->normalize($box);
        }

        return new JsonResponse(array_merge(
            $this->get('serializer')->normalize($unit),
            array(
                "_embedded" => array(
                    "boxes" => $boxesArray,
                )
            )
        ));
    }

    /**
     * Delete Transport Unit by Name for a Shipment
     *
     * @param int    $shipment
     * @param string $id
     *
     * @return JsonResponse
     */
    public function deleteAction(int $shipment, string $id): JsonResponse
    {
        /** @var null|TransportUnit $box */
        $box = $this->getDoctrine()->getManager()->getRepository(TransportUnit::class)->findOneBy(array(
            "shipment" => $shipment,
            "id" => $id,
        ));

        if (!$box) {
            throw new NotFoundHttpException();
        }

        $this->getDoctrine()->getManager()->remove($box);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(array("Deleted"));
    }
}
