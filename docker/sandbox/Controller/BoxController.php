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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Box Controller: Custom operations to work with Shipment's Boxes
 */
class BoxController extends AbstractController
{
    const PAGE_SIZE = 5;

    /**
     * Get List of Boxes for a Shipment
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

        $results = $this->getDoctrine()->getManager()->getRepository(Box::class)->findBy(
            array("shipment" => $shipment),
            null,
            $limit,
            $page ? ($page - 1) * $limit : null,
        );

        $total = count($this->getDoctrine()->getManager()->getRepository(Box::class)->findBy(
            array("shipment" => $shipment),
        ));

        return new JsonResponse(array(
            "_links" => array(),
            "_embedded" => array(
                "box" => $this->get('serializer')->normalize($results)
            ),
            "page_count" => $page ? 1 + ((int) ($total / $limit)): 1,
            "total_items" => $total,
            "page" => $page ?: 1,
        ));
    }

    /**
     * Add Box to a Shipment
     *
     * @param int $shipment
     * @param Box $data
     *
     * @return JsonResponse
     */
    public function postAction(int $shipment, Box $data): JsonResponse
    {
        /** @var null|Shipment $parent */
        $parent = $this->getDoctrine()->getManager()->getRepository(Shipment::class)->find($shipment);
        if (!$parent) {
            throw new NotFoundHttpException();
        }

        $data->id = uniqid("b", true);
        $data->shipment = $parent;

        $this->getDoctrine()->getManager()->persist($data);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse($this->get('serializer')->normalize($data));
    }

    /**
     * Get Box by Name for a Shipment
     *
     * @param int    $shipment
     * @param string $name
     *
     * @return JsonResponse
     */
    public function getAction(int $shipment, string $name): JsonResponse
    {
        /** @var null|Box $box */
        $box = $this->getDoctrine()->getManager()->getRepository(Box::class)->findOneBy(array(
            "shipment" => $shipment,
            "name" => $name,
        ));

        if (!$box) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse($this->get('serializer')->normalize($box));
    }

    /**
     * Delete Box by Name for a Shipment
     *
     * @param int    $shipment
     * @param string $name
     *
     * @return JsonResponse
     */
    public function deleteAction(int $shipment, string $name): JsonResponse
    {
        /** @var null|Box $box */
        $box = $this->getDoctrine()->getManager()->getRepository(Box::class)->findOneBy(array(
            "shipment" => $shipment,
            "name" => $name,
        ));

        if (!$box) {
            throw new NotFoundHttpException();
        }

        $this->getDoctrine()->getManager()->remove($box);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(array("Deleted"));
    }
}
