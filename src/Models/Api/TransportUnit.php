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

/**
 * API Shipment Stage
 *
 * (For Stage environment) This API manage `Shipment` (of order) and its preparation in warehouse.
 *
 * OpenAPI spec version: 2
 *
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace Splash\Connectors\ReCommerce\Models\Api;

use DateTime;
use JMS\Serializer\Annotation as JMS;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class representing the TransportUnit model.
 */
class TransportUnit
{
    /**
     * Unique identifier for the TransportUnit among all the API
     *
     * @var string
     *
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("id")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     */
    public $id;

    /**
     * Type of the TransportUnit. Attributes and validations are different depending on this type
     *
     * @var string
     *
     * @Assert\NotNull()
     * @Assert\Choice({ "parcel", "pallet" })
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("type")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read", "Write", "Required"})
     */
    public $type = "parcel";

    /**
     * Only for type 'parcel'. Carrier's tracking number - unique among other Shipment's transport units
     *
     * @var string
     *
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("trackingNumber")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read", "Write", "Required"})
     */
    public $trackingNumber;

    /**
     * Only for type 'pallet'. Identifier of the pallet given by the carrier
     * Unique among other Shipment''s transport units
     *
     * @var string
     *
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("name")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read", "Write", "Required"})
     */
    public $name;

    /**
     * @var array
     *
     * @Assert\Type("array")
     *
     * @JMS\SerializedName("boxes")
     * @JMS\Groups ({"Write", "Required"})
     * @JMS\Type("array")
     */
    public $boxes = array();

    /**
     * Number of Boxes Included in Transport Unit
     *
     * @var int
     *
     * @Assert\Type("int")
     *
     * @JMS\SerializedName("countBoxes")
     * @JMS\Groups ({"Read"})
     * @JMS\Type("int")
     */
    public $countBoxes;

    /**
     * Only for type 'pallet'. Pallet height in cm
     *
     * @var null|int
     *
     * @Assert\Type("int")
     *
     * @JMS\SerializedName("height")
     * @JMS\Type("int")
     * @JMS\Groups ({"Read", "Write"})
     */
    public $height = 0;

    /**
     * Only for type 'pallet'. Pallet width in cm
     *
     * @var null|int
     *
     * @Assert\Type("int")
     *
     * @JMS\SerializedName("width")
     * @JMS\Type("int")
     * @JMS\Groups ({"Read", "Write"})
     */
    public $width = 0;

    /**
     * Only for type 'pallet'. Pallet depth in cm
     *
     * @var null|int
     *
     * @Assert\Type("int")
     *
     * @JMS\SerializedName("depth")
     * @JMS\Type("int")
     * @JMS\Groups({"Read", "Write"})
     */
    public $depth = 0;

    /**
     * Only for type 'pallet'. Transport unit weight in kg
     *
     * @var null|float
     *
     * @Assert\Type("float")
     *
     * @JMS\SerializedName("weight")
     * @JMS\Type("float")
     * @JMS\Groups({"Read", "Write", "Required"})
     */
    public $weight = 0.0;

    /**
     * @var DateTime
     *
     * @Assert\Type("DateTime")
     *
     * @JMS\SerializedName("created")
     * @JMS\Groups({"Read"})
     */
    public $created;

    //====================================================================//
    // MAIN METHODS
    //====================================================================//

    /**
     * TransportUnit constructor.
     *
     * @param string $name
     * @param string $type
     */
    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        if (in_array($type, array("parcel", "pallet"), true)) {
            $this->type = $type;
        }
    }

    /**
     * Add a Parcel to a Transport Unit form a Parcel Definition
     *
     * @param array $parcel
     *
     * @return self
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function addParcel(array $parcel): self
    {
        if (isset($parcel['trackingNumber']) && is_string($parcel['trackingNumber'])) {
            $this->trackingNumber = $parcel['trackingNumber'];
        }
        if (isset($parcel['id']) && is_string($parcel['id'])) {
            $this->boxes[] = array("boxName" => $parcel['id']);
        }
        if (!empty($parcel['weight'])) {
            $this->weight += (float) $parcel['weight'];
        }
        if (isset($parcel['height'])) {
            $this->height = (int) (100 * $parcel['height']) ?: 0;
        }
        if (isset($parcel['width'])) {
            $this->width = (int) (100 * $parcel['width']) ?: 0;
        }
        if (isset($parcel['depth'])) {
            $this->depth = (int) (100 * $parcel['depth']) ?: 0;
        }
        $this->countBoxes = count($this->boxes);

        return $this;
    }

    /**
     * Get Transport Unit Comparaison Md5 Checksum
     *
     * @return string
     */
    public function getCheckSum(): string
    {
        return md5(serialize(array(
            $this->type,
            $this->trackingNumber,
            $this->countBoxes,
        )));
    }
}
