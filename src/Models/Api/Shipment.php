<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2020 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Connectors\ReCommerce\Models\Api;

use DateTime;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\MaxDepth;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\ReadOnly;
use JMS\Serializer\Annotation\Groups;
use Splash\OpenApi\Validator as SPL;

/**
 * Class representing the Shipment model.
 */
class Shipment
{
    /**
     * Unique identifier representing a Shipment.
     *
     * @var string
     * @SerializedName("id")
     * @Assert\NotNull()
     * @Assert\Type("string")
     * @Type("string")
     * @Groups ({"Read", "Write", "List"})
     */
    protected $id;

    /**
     * The order ID this shipment belongs to.
     *
     * @var string
     * @SerializedName("orderId")
     * @Assert\NotNull()
     * @Assert\Type("string")
     * @Type("string")
     * @Groups ({"Read", "Write", "List", "Required"})
     * @SPL\Microdata({"hhtp://www.schema.org", "Order"})
     */
    protected $orderId;

    /**
     * The warehouse this shipment belongs to.
     *
     * @var string
     * @SerializedName("warehouseId")
     * @Assert\NotNull()
     * @Assert\Type("string")
     * @Groups ({"Read", "Write", "List", "Required"})
     * @Type("string")
     */
    protected $warehouseId;

    /**
     * The global order id corresponding to this Shipment. Required for multiSku Shipments.
     *
     * @var null|string
     * @SerializedName("customerOrderId")
     * @Assert\Type("string")
     * @Type("string")
     */
    protected $customerOrderId;

    /**
     * Sales channel label. Behavior of the shipment's workflow vary depending on it.
     *
     * @var string
     * @SerializedName("salesChannelLabel")
     * @Assert\NotNull()
     * @Assert\Choice({
     *     "monoSku": "Mono SKU",
     *     "multiSku": "Multi SKU",
     *     "readyMadeBox": "Ready Made Box",
     *     })
     * @Assert\Type("string")
     *
     * @Groups ({"Read", "Write", "List", "Required"})
     *
     * @Type("string")
     */
    protected $salesChannelLabel;

    /**
     * Carrier label identifying which carrier has to be used.
     *
     * @var string
     * @SerializedName("carrierLabel")
     * @Assert\NotNull()
     * @Assert\Choice({
     *     "chrono18": "Chronopost 18",
     *     "chrono13": "Chronopost 13",
     *     "chronoRelais13": "Chronopost Relais 13",
     *     "chronoSamedi": "Chronopost Samedi",
     *     "chronoClassicInternational": "Chronopost Internationnal",
     *     "chronoExpress": "Chronopost Express",
     *     "dpdRedict": "DPD Direct",
     *     "upsExpressPlus": "UPS Exporess +",
     *     "upsStandard": "UPS Standard",
     *     "upsAccessPoint": "UPS Access Point",
     *     "upsSameDay": "UPS Same Day",
     *     "palletCarrier": "Transporteur Palette",
     *     "other": "Autre",
     * })
     * @Groups ({"Read", "Write", "List", "Required"})
     * @Assert\Type("string")
     * @Type("string")
     *
     */
    protected $carrierLabel;

    /**
     * Transport unit type label. Behavior of the shipment's workflow and validations may vary depending on it.
     *
     * @var string
     * @SerializedName("transportUnitTypeLabel")
     * @Assert\NotNull()
     * @Assert\Choice({
     *     "parcel": "Colis",
     *     "pallet": "Pallette"
     * })
     * @Groups ({"Read", "Write", "List", "Required"})
     * @Assert\Type("string")
     * @Type("string")
     */
    protected $transportUnitTypeLabel;

    /**
     * @var Address
     * @SerializedName("shippingAddress")
     * @Assert\NotNull()
     * @Assert\Type("Splash\Connectors\ReCommerce\Models\Api\Address")
     * @Type("Splash\Connectors\ReCommerce\Models\Api\Address")
     * @Groups ({"Read"})
     */
    protected $shippingAddress;

    /**
     * @var string
     * @SerializedName("status")
     * @Assert\NotNull()
     * @Assert\Type("string")
     * @Assert\Choice({
     *     "failed":        "Failed",
     *     "draft":         "Draft",
     *     "pending":       "Pending",
     *     "processing":    "Processing, Shipment has been taken into account and is being prepared.",
     *     "processed":     "Processed, Shipment has been well prepared.",
     *     "ready-to-ship": "Ready, Shipment is ready to be shipped, but is still not to ship.",
     *     "to-ship":       "To Ship, Shipment has to be shipped.",
     *     "shipping":      "Shipping, Shipment has been taken into account and being prepared for shipping.",
     *     "shipped":       "Shipped, Shipment has been shipped.",
     *     "closed":        "Closed, Shipment is complete, and stock has been updated in other platforms.",
     *     "cancelled":     "Cancelled.",
     * })
     *
     * @Type("string")
     * @Groups ({"Read"})
     */
    protected $status = "draft";

    /**
     * @var DateTime
     * @SerializedName("created")
     * @Assert\NotNull()
     * @Assert\Type("\DateTime")
     * @Type("DateTime")
     * @Groups ({"Read"})
     */
    protected $created;

    /**
     * @var DateTime
     *
     * @SerializedName("modified")
     * @Assert\NotNull()
     * @Assert\Type("\DateTime")
     * @Type("DateTime")
     * @Groups ({"Read"})
     */
    protected $modified;

    /**
     * Shipment's lines. Associations of ProductCode for a quantity.
     *
     * @var Line[]
     * @SerializedName("lines")
     * @Assert\NotNull()
     * @Assert\All({
     *   @Assert\Type("Splash\Connectors\ReCommerce\Models\Api\Line")
     * })
     * @Type("array<Splash\Connectors\ReCommerce\Models\Api\Line>")
     * @Groups ({"Read"})
     */
    protected $lines;
//
//    /**
//     * Assets attached to this shipment.
//     *
//     * @var null|Swagger\Server\Model\AssetResponseInShipment[]
//     * @SerializedName("assets")
//     * @Assert\All({
//     *   @Assert\Type("Swagger\Server\Model\AssetResponseInShipment")
//     * })
//     * @Type("array<Swagger\Server\Model\AssetResponseInShipment>")
//     */
//    protected $assets;
//
    /**
     * Number of Box attached to this shipment. Not available when requesting multiple Shipments
     *
     * @var null|int
     * @SerializedName("countBoxes")
     * @Assert\Type("int")
     * @Type("int")
     * @ReadOnly()
     */
    protected $countBoxes;

    /**
     * Number of TransportUnit attached to this shipment. Not available when requesting multiple Shipments
     *
     * @var null|int
     * @SerializedName("countTransportUnits")
     * @Assert\Type("int")
     * @Type("int")
     * @ReadOnly()
     */
    protected $countTransportUnits;
//
//    /**
//     * The two last status in Shipment's status history, chronological ordered. Not available when requesting multiple Shipments
//     *
//     * @var null|Swagger\Server\Model\StatusHistoryResponse[]
//     * @SerializedName("lastStatusHistories")
//     * @Assert\All({
//     *   @Assert\Type("Swagger\Server\Model\StatusHistoryResponse")
//     * })
//     * @Type("array<Swagger\Server\Model\StatusHistoryResponse>")
//     */
//    protected $lastStatusHistories;
//
//    /**
//     * @var null|Swagger\Server\Model\SelfLink
//     * @SerializedName("_links")
//     * @Assert\Type("Swagger\Server\Model\SelfLink")
//     * @Type("Swagger\Server\Model\SelfLink")
//     */
//    protected $links;



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
     * @param string $id Unique identifier representing a Shipment.
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
     * @param string $orderId The order ID this shipment belongs to.
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
     * @param string $warehouseId The warehouse this shipment belongs to.
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
     * @return null|string
     */
    public function getCustomerOrderId()
    {
        return $this->customerOrderId;
    }

    /**
     * Sets customerOrderId.
     *
     * @param null|string $customerOrderId The global order id corresponding to this Shipment. Required for multiSku Shipments.
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
     * @param string $salesChannelLabel Sales channel label. Behavior of the shipment's workflow vary depending on it.
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
     * @param string $carrierLabel Carrier label identifying which carrier has to be used.
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
     * @param string $transportUnitTypeLabel Transport unit type label. Behavior of the shipment's workflow and validations may vary depending on it.
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


        $this->shippingAddress = $shippingAddress;

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
        $this->status = $status;

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
     * Gets modified.
     *
     * @return DateTime
     */
    public function getModified()
    {
        return $this->modified;
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
     * @param Line[] $lines Shipment's lines. Associations of ProductCode for a quantity.
     *
     * @return $this
     */
    public function setLines(array $lines)
    {
        $this->lines = $lines;

        return $this;
    }
//
//    /**
//     * Gets assets.
//     *
//     * @return null|Swagger\Server\Model\AssetResponseInShipment[]
//     */
//    public function getAssets()
//    {
//        return $this->assets;
//    }
//
//    /**
//     * Sets assets.
//     *
//     * @param null|Swagger\Server\Model\AssetResponseInShipment[] $assets Assets attached to this shipment.
//     *
//     * @return $this
//     */
//    public function setAssets(AssetResponseInShipment $assets = null)
//    {
//        $this->assets = $assets;
//
//        return $this;
//    }
//
    /**
     * Gets countBoxes.
     *
     * @return null|int
     */
    public function getCountBoxes()
    {
        return $this->countBoxes;
    }
//
//    /**
//     * Sets countBoxes.
//     *
//     * @param null|int $countBoxes Number of `Box` attached to this shipment. Not available when requesting multiple Shipments
//     *
//     * @return $this
//     */
//    public function setCountBoxes($countBoxes = null)
//    {
//        $this->countBoxes = $countBoxes;
//
//        return $this;
//    }
//
    /**
     * Gets countTransportUnits.
     *
     * @return null|int
     */
    public function getCountTransportUnits()
    {
        return $this->countTransportUnits;
    }
//
//    /**
//     * Sets countTransportUnits.
//     *
//     * @param null|int $countTransportUnits Number of `TransportUnit` attached to this shipment. Not available when requesting multiple Shipments
//     *
//     * @return $this
//     */
//    public function setCountTransportUnits($countTransportUnits = null)
//    {
//        $this->countTransportUnits = $countTransportUnits;
//
//        return $this;
//    }
//
//    /**
//     * Gets lastStatusHistories.
//     *
//     * @return null|Swagger\Server\Model\StatusHistoryResponse[]
//     */
//    public function getLastStatusHistories()
//    {
//        return $this->lastStatusHistories;
//    }
//
//    /**
//     * Sets lastStatusHistories.
//     *
//     * @param null|Swagger\Server\Model\StatusHistoryResponse[] $lastStatusHistories The two last status in Shipment's status history, chronological ordered. Not available when requesting multiple Shipments
//     *
//     * @return $this
//     */
//    public function setLastStatusHistories(StatusHistoryResponse $lastStatusHistories = null)
//    {
//        $this->lastStatusHistories = $lastStatusHistories;
//
//        return $this;
//    }
//
//    /**
//     * Gets links.
//     *
//     * @return null|Swagger\Server\Model\SelfLink
//     */
//    public function getLinks()
//    {
//        return $this->links;
//    }
//
//    /**
//     * Sets links.
//     *
//     * @param null|Swagger\Server\Model\SelfLink $links
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
