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
 * Class representing the Address model.
 *
 * @ORM\Entity
 */
class Address
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
     * Client's firstname.
     *
     * @var string
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     *
     * @ORM\Column
     */
    public $firstname;

    /**
     * Client's lastname.
     *
     * @var string
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     *
     * @ORM\Column
     */
    public $lastname;

    /**
     * Client's company.
     *
     * @var null|string
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     *
     * @ORM\Column
     */
    public $company;

    /**
     * Client's email.
     *
     * @var null|string
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     *
     * @ORM\Column
     */
    public $email;

    /**
     * Client's phone number.
     *
     * @var null|string
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     *
     * @ORM\Column
     */
    public $phoneNumber;

    /**
     * First line of the address street.
     *
     * @var string
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     *
     * @ORM\Column
     */
    public $address1;

    /**
     * Optional second line of the address street.
     *
     * @var null|string
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     *
     * @ORM\Column(nullable=true)
     */
    public $address2;

    /**
     * Address zip code.
     *
     * @var string
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     *
     * @ORM\Column
     */
    public $postalCode;

    /**
     * Address city.
     *
     * @var string
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     *
     * @ORM\Column
     */
    public $city;

    /**
     * Address country as ISO_3166-1 alpha-3.
     *
     * @var string
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     *
     * @ORM\Column
     */
    public $countryId;

    /**
     * Optional relay unique code where to send the shipment in case of pickup delivery mode.
     *
     * @var null|string
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     *
     * @ORM\Column
     */
    public $relayCode;

    /**
     * Shipment identifier
     *
     * @var Shipment
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Shipment", inversedBy="shippingAddress")
     */
    protected $shipment;

    //====================================================================//
    // DATA FAKER
    //====================================================================//

    /**
     * Address Faker
     *
     * @param Shipment $shipment
     *
     * @return Address
     */
    public static function fake(Shipment $shipment): self
    {
        $faker = Factory::create("fr_FR");

        $address = new self();

        $address->setShipment($shipment);
        $address->firstname = $faker->firstName;
        $address->lastname = $faker->lastName;
        $address->company = $faker->company;
        $address->email = $faker->companyEmail;
        $address->phoneNumber = $faker->e164PhoneNumber;
        $address->address1 = $faker->streetAddress;
        $address->address2 = $faker->streetSuffix;
        $address->postalCode = $faker->postcode;
        $address->city = $faker->city;
        $address->countryId = $faker->countryISOAlpha3;
        $address->relayCode = $faker->randomNumber();

        return $address;
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
