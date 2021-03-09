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

use App\Entity\Asset;
use App\Entity\Shipment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Asset Controller: Custom operations to work with Shipment's Assets
 */
class AssetController extends AbstractController
{
    /**
     * Download Asset by Id for a Shipment
     *
     * @param int $id
     * @param int $asset
     *
     * @return BinaryFileResponse
     */
    public function downloadAction(int $id, int $asset): BinaryFileResponse
    {
        /** @var null|Asset $asset */
        $assetObject = $this->getDoctrine()->getManager()->getRepository(Asset::class)->findOneBy(array(
            "shipment" => $id,
            "id" => $asset,
        ));

        if (!$assetObject) {
            throw new NotFoundHttpException();
        }

        return new BinaryFileResponse(
            $this->getParameter('kernel.project_dir').'/public/files/'.$assetObject->name
        );
    }
}
