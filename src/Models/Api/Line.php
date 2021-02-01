<?php


namespace Splash\Connectors\ReCommerce\Models\Api;

use JMS\Serializer\Annotation\AccessType;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
use Splash\OpenApi\Validator as SPL;

/**
 * Class representing the LineResponse model.
 *
 * @package Swagger\Server\Model
 * @author  Swagger Codegen team
 */
class Line
{
    /**
     * A unique identifier among Shipment's lines
     *
     * @var string
     * @SerializedName("id")
     * @Assert\NotNull()
     * @Assert\Type("string")
     * @Type("string")
     * @Groups ({"Read"})
     */
    protected $id;

    /**
     * The attached-to ProductCode's reference
     *
     * @var string
     * @SerializedName("productCodeReference")
     * @Assert\NotNull()
     * @Assert\Type("string")
     * @Type("string")
     * @Groups ({"Read"})
     */
    protected $productCodeReference;

    /**
     * Quantity of the given ProductCode for this Shipment
     *
     * @var int
     * @SerializedName("quantity")
     * @Assert\NotNull()
     * @Assert\Type("int")
     * @Type("int")
     * @Groups ({"Read"})
     * @SPL\Microdata({"http://schema.org/QuantitativeValue", "value"})
     */
    protected $quantity;

    /**
     * Optional EAN customisation for this line. If not set, you must use the attached ProductCode EAN
     *
     * @var string|null
     * @SerializedName("ean")
     * @Assert\Type("string")
     * @Type("string")
     * @Groups ({"Read"})
     * @SPL\Microdata({"http://schema.org/Product", "ean13"})
     */
    protected $ean;

    /**
     * Optional label customisation for this line
     *
     * @var string|null
     * @SerializedName("label")
     * @Assert\Type("string")
     * @Type("string")
     * @Groups ({"Read"})
     * @SPL\Microdata({"http://schema.org/partOfInvoice", "description"})
     */
    protected $label;

    /**
     * Optional article code customisation for this line
     *
     * @var string|null
     * @SerializedName("articleCode")
     * @Assert\Type("string")
     * @Type("string")
     * @Groups ({"Read"})
     */
    protected $articleCode;

    /**
     * Accessories to prepare in the same quantity for this line (should be put in the same box/transport unit)
     *
     * @var array
     * @SerializedName("accessories")
     * @Assert\NotNull()
     * @Type("array<array>")
     * @AccessType("public_method")
     */
    protected $accessories;

    /**
     * Accessories to prepare in the same quantity for this line (should be put in the same box/transport unit)
     *
     * @var string
     * @Type("string")
     * @Groups ({"Read"})
     * @SPL\Type("inline")
     */
    protected $accessoriesSkus;

    //====================================================================//
    // VIRTUAL GETTERS
    //====================================================================//

    /**
     * Gets accessories.
     *
     * @return string
     */
    public function getAccessoriesSkus()
    {
        if (isset($this->accessoriesSkus)) {
            return $this->accessoriesSkus;
        }

        $skus = array();
        if (is_iterable($this->accessories)) {
            foreach ($this->accessories as $accessory) {
                $skus[] = $accessory["productCodeReference"];
            }
        }

        return $this->accessoriesSkus = json_encode($skus);
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
     * @param string $id  A unique identifier among Shipment's lines
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

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
        return (float) $this->quantity;
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
     * @return null|array
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


