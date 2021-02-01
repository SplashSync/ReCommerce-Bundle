<?php


namespace App\Entity;

use Faker\Factory;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class representing the Address model.
 *
 * @ApiResource(
 *     collectionOperations={},
 *     itemOperations={"get"},
 * )
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
     */
    protected $id;

    /**
     * Shipment identifier
     *
     * @var Shipment
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Shipment", inversedBy="shippingAddress")
     */
    protected $shipment;

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
    protected $firstname;

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
    protected $lastname;

    /**
     * Client's company.
     *
     * @var string|null
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     *
     * @ORM\Column
     */
    protected $company;

    /**
     * Client's email.
     *
     * @var string|null
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     *
     * @ORM\Column
     */
    protected $email;

    /**
     * Client's phone number.
     *
     * @var string|null
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     *
     * @ORM\Column
     */
    protected $phoneNumber;

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
    protected $address1;

    /**
     * Optional second line of the address street.
     *
     * @var string|null
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     *
     * @ORM\Column(nullable=true)
     */
    protected $address2;

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
    protected $postalCode;

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
    protected $city;

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
    protected $countryId;

    /**
     * Optional relay unique code where to send the shipment in case of pickup delivery mode.
     *
     * @var string|null
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     *
     * @ORM\Column
     */
    protected $relayCode;

    //====================================================================//
    // DATA FAKER
    //====================================================================//

    /**
     * Address Faker
     * @param Shipment $shipment
     *
     * @return Address
     */
    public static function fake(Shipment $shipment): self
    {
        $faker = Factory::create();

        $address = new self();

        $address->setShipment($shipment);
        $address->setFirstname($faker->firstName);
        $address->setLastname($faker->lastName);
        $address->setCompany($faker->company);
        $address->setEmail($faker->companyEmail);
        $address->setPhoneNumber($faker->phoneNumber);
        $address->setAddress1($faker->streetAddress);
        $address->setAddress2($faker->streetSuffix);
        $address->setPostalCode($faker->postcode);
        $address->setCity($faker->city);
        $address->setCountryId($faker->countryISOAlpha3);
        $address->setRelayCode($faker->randomNumber());

        return $address;
    }

    //====================================================================//
    // GENERIC GETTERS & SETTERS
    //====================================================================//

    /**
     * Gets id.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

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

    /**
     * Gets firstname.
     *
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * Sets firstname.
     *
     * @param string $firstname Client's firstname.
     *
     * @return $this
     */
    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Gets lastname.
     *
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * Sets lastname.
     *
     * @param string $lastname Client's lastname.
     *
     * @return $this
     */
    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Gets company.
     *
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * Sets company.
     *
     * @param string|null $company  Client's company.
     *
     * @return $this
     */
    public function setCompany(?string $company = null): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Gets email.
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Sets email.
     *
     * @param string|null $email  Client's email.
     *
     * @return $this
     */
    public function setEmail(?string $email = null): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Gets phoneNumber.
     *
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * Sets phoneNumber.
     *
     * @param string|null $phoneNumber  Client's phone number.
     *
     * @return $this
     */
    public function setPhoneNumber(?string $phoneNumber = null): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Gets address1.
     *
     * @return string
     */
    public function getAddress1(): string
    {
        return $this->address1;
    }

    /**
     * Sets address1.
     *
     * @param string $address1 First line of the address street.
     *
     * @return $this
     */
    public function setAddress1(string $address1): self
    {
        $this->address1 = $address1;

        return $this;
    }

    /**
     * Gets address2.
     *
     * @return string|null
     */
    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    /**
     * Sets address2.
     *
     * @param string|null $address2  Optional second line of the address street.
     *
     * @return $this
     */
    public function setAddress2(?string $address2 = null): self
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * Gets postalCode.
     *
     * @return string
     */
    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    /**
     * Sets postalCode.
     *
     * @param string $postalCode Address zip code.
     *
     * @return $this
     */
    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Gets city.
     *
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * Sets city.
     *
     * @param string $city Address city.
     *
     * @return $this
     */
    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Gets countryId.
     *
     * @return string
     */
    public function getCountryId(): string
    {
        return $this->countryId;
    }

    /**
     * Sets countryId.
     *
     * @param string $countryId  Address country as ISO_3166-1 alpha-3.
     *
     * @return $this
     */
    public function setCountryId(string $countryId): self
    {
        $this->countryId = $countryId;

        return $this;
    }

    /**
     * Gets relayCode.
     *
     * @return string|null
     */
    public function getRelayCode(): ?string
    {
        return $this->relayCode;
    }

    /**
     * Sets relayCode.
     *
     * @param string|null $relayCode  Optional relay unique code where to send the shipment in case of pickup delivery mode.
     *
     * @return $this
     */
    public function setRelayCode(?string $relayCode = null): self
    {
        $this->relayCode = $relayCode;

        return $this;
    }
}


