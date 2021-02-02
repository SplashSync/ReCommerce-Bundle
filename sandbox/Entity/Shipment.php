<?php


namespace App\Entity;

use _HumbugBoxcb6a53192cfd\Nette\Neon\Exception;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

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
 *          "delete":   { "path": "/shipment/{id}" }
 *     },
 *     subresourceOperations={
 *          "api_shipment_address_get_subresource": { "method": "GET", "path": "/address/{id}" },
 *     },
 *     attributes={
 *          "filters"={"shipment.orderId"},
 *          "normalization_context"={"groups"={"read"}},
 *          "denormalizationContext"={"groups"={"write"}}
 *     }
 * )
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
    protected $id;

    /**
     * The order ID this shipment belongs to.
     *
     * @var string
     *
     * @ORM\Column
     * @Assert\NotNull()
     * @Groups({"read", "write"})
     */
    protected $orderId;

    /**
     * The warehouse this shipment belongs to.
     *
     * @var string
     *
     * @ORM\Column
     * @Assert\NotNull()
     * @Groups({"read", "write"})
     */
    protected $warehouseId;

    /**
     * The global order id corresponding to this Shipment. Required for multiSku Shipments.
     *
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     * @Assert\Type("string")
     * @Groups({"read", "write"})
     */
    protected $customerOrderId;

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
    protected $salesChannelLabel;

    /**
     * Carrier label identifying which carrier has to be used.
     *
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotNull()
     * @Assert\Choice({ "palletCarrier", "other", "chrono18", "chrono13", "chronoRelais13", "chronoSamedi", "chronoClassicInternational", "chronoExpress", "dpdRedict", "upsExpressPlus", "upsStandard", "upsAccessPoint", "upsSameDay" })
     * @Assert\Type("string")
     * @Groups({"read", "write"})
     */
    protected $carrierLabel;

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
    protected $transportUnitTypeLabel;

    /**
     * @var Address
     *
     * @Assert\Type("App\Entity\Address")
     * @Groups({"read", "write"})
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Address", cascade="all")
     * @ORM\JoinColumn(referencedColumnName="id", unique=true, nullable=true)
     */
    protected $shippingAddress;

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\Type("string")
     * @Assert\Choice({
     *     "failed", "draft", "pending", "processing", "processed", "ready-to-ship", "to-ship", "shipping", "shipped", "closed", "cancelled",
     * })
     *
     * @Groups({"read"})
     */
    protected $status = "draft";

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     *
     * @Assert\Type("DateTime")
     * @Groups({"read"})
     */
    protected $created;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     * @Assert\Type("DateTime")
     * @Groups({"read"})
     */
    protected $modified;

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
     * @ORM\OneToMany(targetEntity="App\Entity\Line", mappedBy="shipment", cascade="all")
     */
    protected $lines;
//
//    /**
//     * Assets attached to this shipment.
//     *
//     * @var AssetResponseInShipment[]|null
//     *
//     * @Assert\All({
//     *   @Assert\Type("Swagger\Server\Model\AssetResponseInShipment")
//     * })
//     */
//    protected $assets;
//
    /**
     * Number of Box attached to this shipment. Not available when requesting multiple Shipments
     *
     * @var int|null
     *
     * @Assert\Type("int")
     * @Groups({"read"})
     */
    protected $countBoxes;

    /**
     * Number of TransportUnit attached to this shipment. Not available when requesting multiple Shipments
     *
     * @var int|null
     *
     * @Assert\Type("int")
     * @Groups({"read"})
     */
    protected $countTransportUnits;
//
//    /**
//     * The two last status in Shipment's status history, chronological ordered. Not available when requesting multiple Shipments
//     *
//     * @var StatusHistoryResponse[]|null
//     *
//     * @Assert\All({
//     *   @Assert\Type("Swagger\Server\Model\StatusHistoryResponse")
//     * })
//     */
//    protected $lastStatusHistories;
//
//    /**
//     * @var SelfLink|null
//     *
//     * @Assert\Type("Swagger\Server\Model\SelfLink")
//     */
//    protected $links;

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
        ));

        $this->setCreated(new DateTime());
        $this->setModified(new DateTime());
    }

    /**
     * @ORM\PreUpdate()
     */
    public function onPreUpdate(): void
    {
        $this->setModified(new DateTime());
    }

    //====================================================================//
    // GENERIC GETTERS & SETTERS
    //====================================================================//

    /**
     * Gets id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets id.
     *
     * @param string $id  Unique identifier representing a Shipment.
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets orderId.
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Sets orderId.
     *
     * @param string $orderId  The order ID this shipment belongs to.
     *
     * @return $this
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Gets warehouseId.
     *
     * @return string
     */
    public function getWarehouseId()
    {
        return $this->warehouseId;
    }

    /**
     * Sets warehouseId.
     *
     * @param string $warehouseId  The warehouse this shipment belongs to.
     *
     * @return $this
     */
    public function setWarehouseId($warehouseId)
    {
        $this->warehouseId = $warehouseId;

        return $this;
    }

    /**
     * Gets customerOrderId.
     *
     * @return string|null
     */
    public function getCustomerOrderId()
    {
        return $this->customerOrderId;
    }

    /**
     * Sets customerOrderId.
     *
     * @param string|null $customerOrderId  The global order id corresponding to this Shipment. Required for multiSku Shipments.
     *
     * @return $this
     */
    public function setCustomerOrderId($customerOrderId = null)
    {
        $this->customerOrderId = $customerOrderId;

        return $this;
    }

    /**
     * Gets salesChannelLabel.
     *
     * @return string
     */
    public function getSalesChannelLabel()
    {
        return $this->salesChannelLabel;
    }

    /**
     * Sets salesChannelLabel.
     *
     * @param string $salesChannelLabel  Sales channel label. Behavior of the shipment's workflow vary depending on it.
     *
     * @return $this
     */
    public function setSalesChannelLabel($salesChannelLabel)
    {
        $this->salesChannelLabel = $salesChannelLabel;

        return $this;
    }

    /**
     * Gets carrierLabel.
     *
     * @return string
     */
    public function getCarrierLabel()
    {
        return $this->carrierLabel;
    }

    /**
     * Sets carrierLabel.
     *
     * @param string $carrierLabel  Carrier label identifying which carrier has to be used.
     *
     * @return $this
     */
    public function setCarrierLabel($carrierLabel)
    {
        $this->carrierLabel = $carrierLabel;

        return $this;
    }

    /**
     * Gets transportUnitTypeLabel.
     *
     * @return string
     */
    public function getTransportUnitTypeLabel()
    {
        return $this->transportUnitTypeLabel;
    }

    /**
     * Sets transportUnitTypeLabel.
     *
     * @param string $transportUnitTypeLabel  Transport unit type label. Behavior of the shipment's workflow and validations may vary depending on it.
     *
     * @return $this
     */
    public function setTransportUnitTypeLabel($transportUnitTypeLabel)
    {
        $this->transportUnitTypeLabel = $transportUnitTypeLabel;

        return $this;
    }

    /**
     * Gets shippingAddress.
     *
     * @return Address
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * Sets shippingAddress.
     *
     * @param Address $shippingAddress
     *
     * @return $this
     */
    public function setShippingAddress(Address $shippingAddress)
    {
        return $this;
    }

    /**
     * Gets status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets status.
     *
     * @param string $status
     *
     * @return $this
     */
    public function setStatus(string $status)
    {
        if (!empty($status)) {
            $this->status = $status;
        }

        return $this;
    }

    /**
     * Gets created.
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Sets created.
     *
     * @param DateTime $created
     *
     * @return $this
     */
    public function setCreated(DateTime $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Gets modified.
     *
     * @return DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Sets modified.
     *
     * @param DateTime $modified
     *
     * @return $this
     */
    public function setModified(DateTime $modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Gets lines.
     *
     * @return Line[]
     */
    public function getLines()
    {

        return $this->lines;
    }

    /**
     * Sets lines.
     *
     * @param Line[] $lines  Shipment's lines. Associations of ProductCode for a quantity.
     *
     * @return $this
     */
    public function setLines(array $lines): self
    {
//        $this->lines = $lines;

        return $this;
    }

//    /**
//     * Gets assets.
//     *
//     * @return Swagger\Server\Model\AssetResponseInShipment[]|null
//     */
//    public function getAssets()
//    {
//        return $this->assets;
//    }

//    /**
//     * Sets assets.
//     *
//     * @param Swagger\Server\Model\AssetResponseInShipment[]|null $assets  Assets attached to this shipment.
//     *
//     * @return $this
//     */
//    public function setAssets(AssetResponseInShipment $assets = null)
//    {
//        $this->assets = $assets;
//
//        return $this;
//    }

    /**
     * Gets countBoxes.
     *
     * @return int|null
     */
    public function getCountBoxes()
    {
        return $this->countBoxes;
    }

    /**
     * Sets countBoxes.
     *
     * @param int|null $countBoxes  Number of `Box` attached to this shipment. Not available when requesting multiple Shipments
     *
     * @return $this
     */
    public function setCountBoxes($countBoxes = null)
    {
        $this->countBoxes = $countBoxes;

        return $this;
    }

    /**
     * Gets countTransportUnits.
     *
     * @return int|null
     */
    public function getCountTransportUnits()
    {
        return $this->countTransportUnits;
    }

    /**
     * Sets countTransportUnits.
     *
     * @param int|null $countTransportUnits  Number of `TransportUnit` attached to this shipment. Not available when requesting multiple Shipments
     *
     * @return $this
     */
    public function setCountTransportUnits($countTransportUnits = null)
    {
        $this->countTransportUnits = $countTransportUnits;

        return $this;
    }
//
//    /**
//     * Gets lastStatusHistories.
//     *
//     * @return Swagger\Server\Model\StatusHistoryResponse[]|null
//     */
//    public function getLastStatusHistories()
//    {
//        return $this->lastStatusHistories;
//    }

//    /**
//     * Sets lastStatusHistories.
//     *
//     * @param Swagger\Server\Model\StatusHistoryResponse[]|null $lastStatusHistories  The two last status in Shipment's status history, chronological ordered. Not available when requesting multiple Shipments
//     *
//     * @return $this
//     */
//    public function setLastStatusHistories(StatusHistoryResponse $lastStatusHistories = null)
//    {
//        $this->lastStatusHistories = $lastStatusHistories;
//
//        return $this;
//    }

//    /**
//     * Gets links.
//     *
//     * @return Swagger\Server\Model\SelfLink|null
//     */
//    public function getLinks()
//    {
//        return $this->links;
//    }

//    /**
//     * Sets links.
//     *
//     * @param Swagger\Server\Model\SelfLink|null $links
//     *
//     * @return $this
//     */
//    public function setLinks(SelfLink $links = null)
//    {
//        $this->links = $links;
//
//        return $this;
//    }
}


