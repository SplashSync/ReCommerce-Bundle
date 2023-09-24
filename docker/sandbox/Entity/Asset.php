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

use Doctrine\ORM\Mapping as ORM;
use Faker\Factory;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class representing the Asset model.
 *
 * @ORM\Entity
 */
class Asset
{
    const FILE_HELPER = "App\\Helpers\\Files";

    /**
     * Unique identifier for the Asset − machine-readable
     *
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\GeneratedValue
     *
     * @ORM\Column(type="integer")
     *
     * @Assert\Type("integer")
     *
     * @Groups({"read"})
     */
    public $id;

    /**
     * Shipment identifier
     *
     * @var Shipment
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Shipment", inversedBy="assets")
     */
    public $shipment;

    /**
     * Standard formatted URL to the asset. When this field is null, the file attached to the asset is not available.
     * Using POST ../asset/:id/upload will set this value.
     *
     * @var null|string
     *
     * @Assert\Type("string")
     *
     * @ORM\Column
     *
     * @Groups({"read"})
     */
    public $url;

    /**
     * A unique name among all shipments assets − human-readable. Contains the file extension (ex: myfile.pdf).
     *
     * @var string
     *
     * @Assert\NotNull()
     *
     * @Assert\Type("string")
     *
     * @ORM\Column
     *
     * @Groups({"read"})
     */
    public $name;

    /**
     * Categorize the asset
     *
     * @var string
     *
     * @Assert\NotNull()
     *
     * @Assert\Type("string")
     *
     * @Assert\Choice({
     *     "orderSummary": "Order Summary",
     *     "transportDocument": "Transport Document",
     *     "transportProof": "Transport Proof",
     *     "other": "Other Document"
     * })
     *
     * @ORM\Column
     *
     * @Groups({"read"})
     */
    public $tag;

    //====================================================================//
    // DATA FAKER
    //====================================================================//

    /**
     * Asset Faker
     *
     * @param Shipment $shipment
     *
     * @return Asset
     */
    public static function fake(Shipment $shipment): self
    {
        $faker = Factory::create();

        $asset = new self();

        $asset->setShipment($shipment);
        $asset->url = $faker->imageUrl();
        $asset->name = $faker->text(10);
        $asset->tag = $faker->randomElement(array(
            "orderSummary", "transportDocument", "transportProof", "other"
        ));
        //====================================================================//
        // Generate Infos for a Fake File
        $helper = self::FILE_HELPER;
        if (class_exists($helper)) {
            $fileArray = $helper::fake();
            $asset->url = $fileArray['url'];
            $asset->name = $fileArray['name'];
        }

        return $asset;
    }

    //====================================================================//
    // GENERIC GETTERS & SETTERS
    //====================================================================//

    /**
     * @param Shipment $shipment
     *
     * @return $this
     */
    public function setShipment(Shipment $shipment): self
    {
        $this->shipment = $shipment;

        return $this;
    }
}
