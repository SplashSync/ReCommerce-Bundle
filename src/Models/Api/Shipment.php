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

namespace Splash\Connectors\ReCommerce\Models\Api;

use DateTime;
use JMS\Serializer\Annotation as JMS;
use Splash\Connectors\ReCommerce\DataTransformer\LinesTransformer;
use Splash\OpenApi\Validator as SPL;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class representing the Shipment model.
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Shipment
{
    /**
     * Unique identifier representing a Shipment.
     *
     * @var string
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("id")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read", "Write", "List"})
     */
    protected $id;

    /**
     * The order ID this shipment belongs to.
     *
     * @var string
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("orderId")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read", "Write", "List", "Required"})
     *
     * @SPL\Microdata({"http://schema.org/Order", "orderNumber"})
     */
    protected $orderId;

    /**
     * The warehouse this shipment belongs to.
     *
     * @var string
     *
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("warehouseId")
     * @JMS\Groups ({"Read", "Write", "List", "Required"})
     * @JMS\Type("string")
     */
    protected $warehouseId;

    /**
     * The global order id corresponding to this Shipment. Required for multiSku Shipments.
     *
     * @var null|string
     *
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("customerOrderId")
     * @JMS\Type("string")
     *
     * @SPL\Microdata({"http://schema.org/Order", "alternateName"})
     */
    protected $customerOrderId;

    /**
     * Sales channel label. Behavior of the shipment's workflow vary depending on it.
     *
     * @var string
     *
     * @Assert\NotNull()
     * @Assert\Choice({
     *     "monoSku": "Mono SKU",
     *     "multiSku": "Multi SKU",
     *     "readyMadeBox": "Ready Made Box",
     *     })
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("salesChannelLabel")
     * @JMS\Groups ({"Read", "Write", "List", "Required"})
     * @JMS\Type("string")
     */
    protected $salesChannelLabel;

    /**
     * Carrier label identifying which carrier has to be used.
     *
     * @var string
     *
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
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("carrierLabel")
     * @JMS\Groups ({"Read", "Write", "List", "Required"})
     * @JMS\Type("string")
     *
     * @SPL\Microdata({"http://schema.org/ParcelDelivery", "identifier"})
     */
    protected $carrierLabel;

    /**
     * Transport unit type label. Behavior of the shipment's workflow and validations may vary depending on it.
     *
     * @var string
     *
     * @Assert\NotNull()
     * @Assert\Choice({
     *     "parcel": "Colis",
     *     "pallet": "Pallette"
     * })
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("transportUnitTypeLabel")
     * @JMS\Groups ({"Read", "Write", "List", "Required"})
     * @JMS\Type("string")
     *
     * @SPL\Microdata({"http://schema.org/ParcelDelivery", "alternateName"})
     */
    protected $transportUnitTypeLabel;

    /**
     * @var Address
     *
     * @Assert\NotNull()
     * @Assert\Type("Splash\Connectors\ReCommerce\Models\Api\Address")
     *
     * @JMS\SerializedName("shippingAddress")
     * @JMS\Type("Splash\Connectors\ReCommerce\Models\Api\Address")
     * @JMS\Groups ({"Read"})
     */
    protected $shippingAddress;

    /**
     * @var string
     *
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
     * @JMS\SerializedName("status")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read", "List"})
     *
     * @SPL\Microdata({"http://schema.org/Order", "orderStatus"})
     */
    protected $status = "draft";

    /**
     * @var DateTime
     *
     * @Assert\NotNull()
     * @Assert\Type("\DateTime")
     *
     * @JMS\SerializedName("created")
     * @JMS\Type("DateTime")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Microdata({"http://schema.org/DataFeedItem", "dateCreated"})
     */
    protected $created;

    /**
     * @var DateTime
     *
     * @Assert\NotNull()
     * @Assert\Type("\DateTime")
     *
     * @JMS\SerializedName("modified")
     * @JMS\Type("DateTime")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Microdata({"http://schema.org/DataFeedItem", "dateModified"})
     */
    protected $modified;

    /**
     * Shipment's lines. Associations of ProductCode for a quantity.
     *
     * @var Line[]
     *
     * @Assert\NotNull()
     * @Assert\All({
     *   @Assert\Type("Splash\Connectors\ReCommerce\Models\Api\Line")
     * })
     *
     * @JMS\SerializedName("lines")
     * @JMS\Type("array<Splash\Connectors\ReCommerce\Models\Api\Line>")
     * @JMS\Groups ({"Read"})
     */
    protected $lines;

    /**
     * Assets attached to this shipment.
     *
     * @var null|Asset[]
     *
     * @Assert\All({
     *   @Assert\Type("Splash\Connectors\ReCommerce\Models\Api\Asset")
     * })
     *
     * @JMS\SerializedName("assets")
     * @JMS\Type("array<Splash\Connectors\ReCommerce\Models\Api\Asset>")
     */
    protected $assets;

    /**
     * Number of Box attached to this shipment. Not available when requesting multiple Shipments
     *
     * @var null|int
     *
     * @Assert\Type("int")
     *
     * @JMS\SerializedName("countBoxes")
     * @JMS\Type("int")
     * @JMS\Groups ({"Read"})
     */
    protected $countBoxes;

    /**
     * Number of TransportUnit attached to this shipment. Not available when requesting multiple Shipments
     *
     * @var null|int
     *
     * @Assert\Type("int")
     *
     * @JMS\SerializedName("countTransportUnits")
     * @JMS\Type("int")
     * @JMS\Groups ({"Read"})
     */
    protected $countTransportUnits;

    //====================================================================//
    // Computed Fields
    //====================================================================//

    /**
     * Sales channel code.
     * Send to Carrier in Order to Adjust Shipment Workflow.
     *
     * @var null|string
     *
     * @JMS\SerializedName("salesChannelCode")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Microdata({"http://schema.org/Order", "disambiguatingDescription"})
     */
    protected $salesChannelCode;

    /**
     * Expended Shipment's lines. Including Accessories Lines.
     *
     * @var Line[]
     *
     * @JMS\Exclude()
     */
    private $expendedLines;

    /**
     * Shipment Parcel's Simulation.
     * - 2 Lines per parcel
     * - 2 Parcels per Transport Unit
     *
     * @var Line[]
     *
     * @JMS\Exclude()
     */
    private $parcelsSimulation;

    //====================================================================//
    // SPECIAL GETTERS
    //====================================================================//

    /**
     * Check if this Order push Boxes to Order Lines
     *
     * @return bool
     */
    public function isBoxesToLinesOrder(): bool
    {
        return ('readyMadeBox' == $this->salesChannelLabel);
    }

    /**
     * @return null|string
     */
    public function getSalesChannelCode(): ?string
    {
        $channelsCodes = array(
            "monoSku" => "REC00021",
            "multiSku" => "REC00121",
            "readyMadeBox" => "REC00221",
        );
        if (!isset($channelsCodes[$this->salesChannelLabel])) {
            return null;
        }

        return $channelsCodes[$this->salesChannelLabel];
    }

    /**
     * Gets expended lines.
     *
     * @return Line[]
     */
    public function getLines(): array
    {
        if (!isset($this->expendedLines)) {
            $this->expendedLines = LinesTransformer::expend((array) $this->lines);
        }

        return $this->expendedLines;
    }

    /**
     * Gets a Simulated list of Parcels based on lines.
     *
     * @return array
     */
    public function getParcelSimulation(): array
    {
        if (!isset($this->parcelsSimulation)) {
            $this->parcelsSimulation = LinesTransformer::simParcels((array) $this->lines);
        }

        return $this->parcelsSimulation;
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
     * Sets customerOrderId. Required for multiSku Shipments.
     *
     * @param null|string $customerOrderId The global order id corresponding to this Shipment.
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
     * Behavior of the shipment's workflow vary depending on it.
     *
     * @param string $salesChannelLabel Sales channel label.
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
     * Behavior of the shipment's workflow and validations may vary depending on it.
     *
     * @param string $transportUnitTypeLabel Transport unit type label.
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
    public function getShippingAddress(): Address
    {
        return $this->shippingAddress;
    }

    /**
     * Gets status.
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Gets created.
     *
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * Gets modified.
     *
     * @return DateTime
     */
    public function getModified(): DateTime
    {
        return $this->modified;
    }

    /**
     * Gets assets.
     *
     * @return null|Asset[]
     */
    public function getAssets(): ?array
    {
        return $this->assets;
    }

    /**
     * Gets countBoxes.
     *
     * @return int
     */
    public function getCountBoxes(): int
    {
        return (int) $this->countBoxes;
    }

    /**
     * Gets countTransportUnits.
     *
     * @return int
     */
    public function getCountTransportUnits(): int
    {
        return (int) $this->countTransportUnits;
    }
}
