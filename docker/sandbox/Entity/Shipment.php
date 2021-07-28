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

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class representing the ShipmentResponse model.
 *
 * @ApiResource(
 *     collectionOperations={
 *          "get":      { "path": "/shipment" },
 *          "post":     { "path": "/shipment" }
 *     },
 *     itemOperations={
 *          "get":      { "path": "/shipment/{id}" },
 *          "patch":    { "path": "/shipment/{id}" },
 *          "delete":   { "path": "/shipment/{id}" },
 *          "download":      {
 *              "method": "GET",
 *              "path": "/shipment/{id}/asset/{asset}/download",
 *              "controller": {"App\Controller\AssetController", "downloadAction"},
 *          },
 *     },
 *     attributes={
 *          "filters"={"shipment.orderId"},
 *          "normalization_context"={"groups"={"read"}},
 *          "denormalizationContext"={"groups"={"write"}}
 *     }
 * )
 * @ApiFilter(
 *     filterClass="ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter",
 *     properties={"status"}
 * )
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Shipment
{
    /**
     * Unique identifier representing a Shipment.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Assert\Type("integer")
     *
     * @Groups({"read"})
     */
    public $id;

    /**
     * The order ID this shipment belongs to.
     *
     * @var string
     *
     * @ORM\Column
     * @Assert\NotNull()
     * @Groups({"read", "write"})
     */
    public $orderId;

    /**
     * The warehouse this shipment belongs to.
     *
     * @var string
     *
     * @ORM\Column
     * @Assert\NotNull()
     * @Groups({"read", "write"})
     */
    public $warehouseId;

    /**
     * The global order id corresponding to this Shipment. Required for multiSku Shipments.
     *
     * @var null|string
     *
     * @ORM\Column(nullable=true)
     * @Assert\Type("string")
     * @Groups({"read", "write"})
     */
    public $customerOrderId;

    /**
     * Sales channel label. Behavior of the shipment's workflow vary depending on it.
     *
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotNull()
     * @Assert\Choice({ "monoSku", "multiSku", "readyMadeBox" })
     * @Assert\Type("string")
     * @Groups({"read", "write"})
     */
    public $salesChannelLabel;

    /**
     * Carrier label identifying which carrier has to be used.
     *
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotNull()
     * @Assert\Choice({
     *     "palletCarrier", "other",
     *     "chrono18", "chrono13", "chronoRelais13", "chronoSamedi", "chronoClassicInternational", "chronoExpress",
     *     "dpdRedict",
     *     "upsExpressPlus", "upsStandard", "upsAccessPoint", "upsSameDay",
     *     "tntInternational", "tntEconomy"
     * })
     * @Assert\Type("string")
     * @Groups({"read", "write"})
     */
    public $carrierLabel;

    /**
     * Transport unit type label. Behavior of the shipment&#39;s workflow and validations may vary depending on it.
     *
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotNull()
     * @Assert\Choice({ "parcel", "pallet" })
     * @Assert\Type("string")
     * @Groups({"read"})
     */
    public $transportUnitTypeLabel;

    /**
     * @var Address
     *
     * @Assert\Type("App\Entity\Address")
     * @Groups({"read", "write"})
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Address", cascade="all")
     * @ORM\JoinColumn(referencedColumnName="id", unique=true, nullable=true)
     */
    public $shippingAddress;

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\Type("string")
     * @Assert\Choice({
     *     "failed",
     *     "draft",
     *     "pending",
     *     "processing",
     *     "processed",
     *     "ready-to-ship",
     *     "to-ship",
     *     "shipping",
     *     "shipped",
     *     "closed",
     *     "cancelled",
     * })
     *
     * @Groups({"read"})
     */
    public $status = "draft";

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     *
     * @Assert\Type("DateTime")
     * @Groups({"read"})
     */
    public $created;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     * @Assert\Type("DateTime")
     * @Groups({"read"})
     */
    public $modified;

    /**
     * Shipment's lines. Associations of ProductCode for a quantity.
     *
     * @var Line[]
     *
     * @Assert\All({
     *   @Assert\Type("App\Entity\Line")
     * })
     * @Groups({"read"})
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Line", mappedBy="shipment", cascade={"all"})
     */
    public $lines;

    /**
     * Assets attached to this shipment.
     *
     * @var null|Asset[]
     *
     * @Assert\All({
     *   @Assert\Type("App\Entity\Asset")
     * })
     *
     * @Groups({"read"})
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Asset", mappedBy="shipment", cascade={"all"})
     */
    public $assets;

    /**
     * Shipment's boxes.
     *
     * @var Box[]
     *
     * @Assert\All({
     *   @Assert\Type("App\Entity\Box")
     * })
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Box", mappedBy="shipment", cascade={"all"})
     */
    public $boxes;

    /**
     * Shipment's transport units.
     *
     * @var TransportUnit[]
     *
     * @Assert\All({
     *   @Assert\Type("App\Entity\TransportUnit")
     * })
     *
     * @ORM\OneToMany(targetEntity="App\Entity\TransportUnit", mappedBy="shipment", cascade={"all"})
     */
    public $transportUnits;

    /**
     * Extra Informations
     *
     * @var array
     *
     * @ORM\Column(type="array", nullable=true)
     *
     * @Assert\Type("array")
     * @Groups({"read"})
     */
    public $_embedded = array(
        "productCodes" => array(),
    );

    /**
     * Preparation Rules Informations
     *
     * @var array
     *
     * @ORM\Column(type="array", nullable=true)
     *
     * @Assert\Type("array")
     * @Groups({"read"})
     */
    public $preparationRule = array(
        "maximumQuantityPerBox" => 10,
        "mixedEanInBox" => null,
    );

    /**
     * Number of Box attached to this shipment. Not available when requesting multiple Shipments
     *
     * @var null|int
     *
     * @Assert\Type("int")
     * @Groups({"read"})
     */
    protected $countBoxes;

    /**
     * Number of TransportUnit attached to this shipment. Not available when requesting multiple Shipments
     *
     * @var null|int
     *
     * @Assert\Type("int")
     * @Groups({"read"})
     */
    protected $countTransportUnits;

    //====================================================================//
    // Specific Getters
    //====================================================================//

    /**
     * @return null|int
     */
    public function getCountBoxes(): ?int
    {
        return count($this->boxes->toArray());
    }

    /**
     * @return null|int
     */
    public function getCountTransportUnits(): ?int
    {
        return count($this->transportUnits->toArray());
    }

    //====================================================================//
    // Specific Setters
    //====================================================================//

    /**
     * Add an Accessory Ean Code to Embedded
     *
     * @param string $productCode
     * @param string $ean
     *
     * @return self
     */
    public function addAccEan(string $productCode, string $ean): self
    {
        $this->_embedded["productCodes"][] = array(
            "reference" => $productCode,
            "ean" => $ean,
        );

        return $this;
    }

    //====================================================================//
    // ORM EVENTS
    //====================================================================//

    /**
     * @ORM\PrePersist()
     */
    public function onPrePersist(): void
    {
        $this->status = "draft";
        $this->shippingAddress = Address::fake($this);
        $this->lines = new ArrayCollection(array(
            Line::fake($this),
            Line::fake($this),
            Line::fake($this),
            Line::fake($this),
            Line::fake($this),
        ));

        $this->assets = new ArrayCollection(array(
            Asset::fake($this),
            Asset::fake($this),
        ));

        $this->created = new DateTime();
        $this->modified = new DateTime();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function onPreUpdate(): void
    {
        $this->modified = new DateTime();
    }
}
