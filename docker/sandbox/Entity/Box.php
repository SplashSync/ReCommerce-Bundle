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

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class representing the Box model.
 *
 * @ApiResource(
 *      compositeIdentifier=false,
 *      collectionOperations={
 *          "get":      {
 *              "path": "/shipment/{shipment}/box",
 *              "controller": {"App\Controller\BoxController", "listAction"},
 *          },
 *          "post":     {
 *              "path": "/shipment/{shipment}/box",
 *              "controller": {"App\Controller\BoxController", "postAction"},
 *          }
 *     },
 *     itemOperations={
 *          "get":      {
 *              "path": "/shipment/{shipment}/box/{name}",
 *              "controller": {"App\Controller\BoxController", "getAction"},
 *          },
 *          "delete":   {
 *              "path": "/shipment/{shipment}/box/{name}",
 *              "controller": {"App\Controller\BoxController", "deleteAction"},
 *          }
 *     },
 *     attributes={
 *          "normalization_context"={"groups"={"read"}},
 *          "denormalizationContext"={"groups"={"write"}}
 *     }
 * )
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Box
{
    /**
     * Box name, unique for the API
     *
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string")
     *
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @ApiProperty(identifier=true)
     *
     * @Groups({"read"})
     */
    public $name;

    /**
     * Shipment identifier
     *
     * @var Shipment
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Shipment", inversedBy="boxes")
     *
     * @ApiProperty(identifier=true)
     */
    public $shipment;

    /**
     * Transport Unit identifier
     *
     * @var TransportUnit
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\TransportUnit", inversedBy="box")
     *
     * @Groups({"none"})
     */
    public $transportUnit;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     * @Assert\Type("DateTime")
     * @Groups({"read"})
     */
    public $created;

    //====================================================================//
    // ORM EVENTS
    //====================================================================//

    /**
     * @ORM\PrePersist()
     */
    public function onPrePersist(): void
    {
        $this->created = new DateTime();
    }
}
