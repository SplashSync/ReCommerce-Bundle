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

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class representing the Shipment Status model.
 *
 * @ApiResource(
 *     collectionOperations={
 *     },
 *     itemOperations={
 *          "status":   {
 *              "path": "/shipment/{id}/status/{status}",
 *              "controller": {"App\Controller\StatusController", "indexAction"},
 *              "method": "patch",
 *              "read": false,
 *              "validate": false
 *          }
 *     }
 * )
 */
class ShipmentStatus
{
    /**
     * Unique identifier representing a Shipment.
     *
     * @var int
     */
    public $id;

    /**
     * Shipment Status Change reason.
     *
     * @var string
     *
     * @Assert\NotNull()
     * @Assert\Type("string")
     */
    public $reasonMessage;
}
