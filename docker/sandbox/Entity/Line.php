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

use Doctrine\ORM\Mapping as ORM;
use Faker\Factory;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class representing the Line model.
 *
 * @ORM\Entity
 */
class Line
{
    /**
     * A unique identifier among Shipment's lines
     *
     * @var string
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     */
    public $id;

    /**
     * Shipment identifier
     *
     * @var Shipment
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Shipment", inversedBy="lines")
     */
    public $shipment;

    /**
     * The attached-to ProductCode's reference
     *
     * @var string
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     * @ORM\Column
     */
    public $productCodeReference;

    /**
     * Quantity of the given ProductCode for this Shipment
     *
     * @var int
     * @Assert\NotNull()
     * @Assert\Type("int")
     *
     * @Groups({"read"})
     * @ORM\Column
     */
    public $quantity;

    /**
     * Optional EAN customisation for this line. If not set, you must use the attached ProductCode EAN
     *
     * @var null|string
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     * @ORM\Column
     */
    public $ean;

    /**
     * Optional label customisation for this line
     *
     * @var null|string
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     * @ORM\Column
     */
    public $label;

    /**
     * Optional article code customisation for this line
     *
     * @var null|string
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     * @ORM\Column
     */
    public $articleCode;

    /**
     * Accessories to prepare in the same quantity for this line (should be put in the same box/transport unit)
     *
     * @var array
     *
     * @Assert\Type("array")
     * @Groups({"read"})
     * @ORM\Column(type="array")
     */
    public $accessories = array();

    /**
     * Address Faker
     */
    public static function fake(Shipment $shipment): self
    {
        $faker = Factory::create();

        $line = new self();

        $line->setShipment($shipment);
        $line->productCodeReference = $faker->text(10);
        $line->quantity = $faker->numberBetween(1, 100);
        $line->ean = $faker->ean13;
        $line->label = $faker->sentence(4);
        $line->articleCode = $faker->streetAddress;

        $accessoires = array();
        for ($i = 0; $i < 1; $i++) {
            $accessoires[] = array(
                "productCodeReference" => $faker->ean13,
                "ean" => $faker->randomElement(array(
                    "0000000001145", "0000000001173", "0000000001174", "3663705900067",
                )),
            );
        }
        $line->accessories = $accessoires;

        return $line;
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
