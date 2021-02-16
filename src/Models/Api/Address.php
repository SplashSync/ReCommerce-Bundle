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

use JMS\Serializer\Annotation as JMS;
use Splash\OpenApi\Validator as SPL;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class representing the Address model.
 */
class Address
{
    /**
     * Client's firstname.
     *
     * @var string
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("firstname")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     */
    public $firstname;

    /**
     * Client's lastname.
     *
     * @var string
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("lastname")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     */
    public $lastname;

    /**
     * Client's company.
     *
     * @var null|string
     *
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("company")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Microdata({"http://schema.org/Organization", "legalName"})
     */
    public $company;

    /**
     * Client's email.
     *
     * @var null|string
     *
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("email")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Type("email")
     * @SPL\Microdata({"http://schema.org/ContactPoint", "email"})
     */
    public $email;

    /**
     * Client's phone number.
     *
     * @var null|string
     *
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("phoneNumber")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Microdata({"http://schema.org/PostalAddress", "telephone"})
     */
    public $phoneNumber;

    /**
     * First line of the address street.
     *
     * @var string
     *
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("address1")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Microdata({"http://schema.org/PostalAddress", "streetAddress"})
     */
    public $address1;

    /**
     * Optional second line of the address street.
     *
     * @var null|string
     *
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("address2")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Microdata({"http://schema.org/PostalAddress", "postOfficeBoxNumber"})
     */
    public $address2;

    /**
     * Address zip code.
     *
     * @var string
     *
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("postalCode")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Microdata({"http://schema.org/PostalAddress", "postalCode"})
     */
    public $postalCode;

    /**
     * Address city.
     *
     * @var string
     *
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("city")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Microdata({"http://schema.org/PostalAddress", "addressLocality"})
     */
    public $city;

    /**
     * Optional relay unique code where to send the shipment in case of pickup delivery mode.
     *
     * @var null|string
     *
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("relayCode")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Microdata({"http://schema.org/PostalAddress", "description"})
     */
    public $relayCode;

    /**
     * Client's full name.
     *
     * @var string
     *
     * @JMS\SerializedName("name")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Microdata({"http://schema.org/PostalAddress", "alternateName"})
     */
    protected $name;

    /**
     * Address country as ISO_3166-1 alpha-3.
     *
     * @var string
     *
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("countryId")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Type("country")
     * @SPL\Microdata({"http://schema.org/PostalAddress", "addressCountry"})
     */
    protected $countryId;

    //====================================================================//
    // VIRTUAL GETTERS
    //====================================================================//

    /**
     * Gets Client's Full Name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->firstname." ".$this->lastname;
    }

    /**
     * Gets countryId.
     *
     * @return string
     */
    public function getCountryId(): string
    {
        //====================================================================//
        // Convert ISO Country Code from Alpha-3 to Alpha-2
        $alpha2Code = Countries::getAlpha2Code($this->countryId);

        return $alpha2Code ? $alpha2Code : $this->countryId;
    }
}
