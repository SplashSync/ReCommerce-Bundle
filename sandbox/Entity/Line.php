<?php


namespace App\Entity;

use Faker\Factory;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


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
    protected $id;

    /**
     * Shipment identifier
     *
     * @var Shipment
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Shipment", inversedBy="lines")
     */
    protected $shipment;

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
    protected $productCodeReference;

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
    protected $quantity;

    /**
     * Optional EAN customisation for this line. If not set, you must use the attached ProductCode EAN
     *
     * @var string|null
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     * @ORM\Column
     */
    protected $ean;

    /**
     * Optional label customisation for this line
     *
     * @var string|null
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     * @ORM\Column
     */
    protected $label;

    /**
     * Optional article code customisation for this line
     *
     * @var string|null
     * @Assert\Type("string")
     *
     * @Groups({"read"})
     * @ORM\Column
     */
    protected $articleCode;

    /**
     * Accessories to prepare in the same quantity for this line (should be put in the same box/transport unit)
     *
     * @var array
     *
     * @Assert\Type("array")
     * @Groups({"read"})
     * @ORM\Column(type="array")
     */
    protected $accessories = array();

    /**
     * Address Faker
     */
    public static function fake(Shipment $shipment): self
    {
        $faker = Factory::create();

        $line = new self();

        $line->setShipment($shipment);
        $line->setProductCodeReference($faker->text(10));
        $line->setQuantity($faker->numberBetween(1, 100));
        $line->setEan($faker->ean13);
        $line->setLabel($faker->sentence(4));
        $line->setArticleCode($faker->streetAddress);

        $accessoires = array();
        for($i=0; $i<3; $i++) {
            $accessoires[] = array("productCodeReference" => $faker->ean13);
        }
        $line->setAccessories($accessoires);

        return $line;
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
     * Gets productCodeReference.
     *
     * @return string
     */
    public function getProductCodeReference()
    {
        return $this->productCodeReference;
    }

    /**
     * Sets productCodeReference.
     *
     * @param string $productCodeReference  The attached-to ProductCode's reference
     *
     * @return $this
     */
    public function setProductCodeReference($productCodeReference)
    {
        $this->productCodeReference = $productCodeReference;

        return $this;
    }

    /**
     * Gets quantity.
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Sets quantity.
     *
     * @param int $quantity  Quantity of the given ProductCode for this Shipment
     *
     * @return $this
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Gets ean.
     *
     * @return string|null
     */
    public function getEan()
    {
        return $this->ean;
    }

    /**
     * Sets ean.
     *
     * @param string|null $ean  Optional EAN customisation for this line. If not set, you must use the attached ProductCode EAN
     *
     * @return $this
     */
    public function setEan($ean = null)
    {
        $this->ean = $ean;

        return $this;
    }

    /**
     * Gets label.
     *
     * @return string|null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Sets label.
     *
     * @param string|null $label  Optional label customisation for this line
     *
     * @return $this
     */
    public function setLabel($label = null)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Gets articleCode.
     *
     * @return string|null
     */
    public function getArticleCode()
    {
        return $this->articleCode;
    }

    /**
     * Sets articleCode.
     *
     * @param string|null $articleCode  Optional article code customisation for this line
     *
     * @return $this
     */
    public function setArticleCode($articleCode = null)
    {
        $this->articleCode = $articleCode;

        return $this;
    }

    /**
     * Gets accessories.
     *
     * @return array
     */
    public function getAccessories(): ?array
    {
        return $this->accessories;
    }

    /**
     * Sets accessories.
     *
     * @param array $accessories  Accessories to prepare in the same quantity for this line (should be put in the same box/transport unit)
     *
     * @return $this
     */
    public function setAccessories(array $accessories)
    {
        $this->accessories = $accessories;

        return $this;
    }
}


