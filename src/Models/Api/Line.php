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

use JMS\Serializer\Annotation as JMS;
use Splash\Connectors\ReCommerce\DataTransformer\LinesTransformer;
use Splash\OpenApi\Validator as SPL;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class representing the Shipment Line API Model.
 */
class Line
{
    /**
     * A unique identifier among Shipment's lines
     *
     * @var string
     *
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("id")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Microdata({"http://schema.org/partOfInvoice", "identifier"})
     */
    protected $id;

    /**
     * The attached-to ProductCode's reference
     *
     * @var string
     *
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("productCodeReference")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Microdata({"http://schema.org/Product", "ref"})
     */
    protected $productCodeReference;

    /**
     * Quantity of the given ProductCode for this Shipment
     *
     * @var int
     *
     * @Assert\NotNull()
     * @Assert\Type("int")
     *
     * @JMS\SerializedName("quantity")
     * @JMS\Type("int")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Microdata({"http://schema.org/QuantitativeValue", "value"})
     */
    protected $quantity;

    /**
     * Optional Customer EAN customisation for this line. If not set, you must use the attached ProductCode EAN
     *
     * @var null|string
     *
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("ean")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Microdata({"http://schema.org/Product", "gint13"})
     */
    protected $ean;

    /**
     * Optional label customisation for this line
     *
     * @var null|string
     *
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("label")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Microdata({"http://schema.org/partOfInvoice", "description"})
     */
    protected $label;

    /**
     * Optional article code customisation for this line
     *
     * @var null|string
     *
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("articleCode")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Microdata({"http://schema.org/Product", "additionalProperty"})
     */
    protected $articleCode;

    /**
     * Logistical EAN for this line. Taken from attached ProductCodes EAN
     *
     * @var string
     *
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Microdata({"http://schema.org/Product", "sku"})
     */
    protected $articleEan;

    /**
     * Accessories to prepare in the same quantity for this line (should be put in the same box/transport unit)
     *
     * @var array
     *
     * @Assert\NotNull()
     *
     * @JMS\SerializedName("accessories")
     * @JMS\Type("array<array>")
     */
    protected $accessories;

    /**
     * Line is Accessory Copy of an Original Line
     *
     * @var null|bool
     *
     * @JMS\Type("bool")
     * @JMS\Groups ({"Read"})
     */
    protected $accessoryLine;

    /**
     * Accessories to prepare in the same quantity for this line (should be put in the same box/transport unit)
     *
     * @var null|string
     *
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     * @SPL\Type("inline")
     */
    protected ?string $accessoriesSkus;

    //====================================================================//
    // VIRTUAL GETTERS
    //====================================================================//

    /**
     * Gets accessories.
     *
     * @return string
     */
    public function getAccessoriesSkus(): string
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

        return $this->accessoriesSkus = (string) json_encode($skus);
    }

    /**
     * Create Order Line for Accessory.
     *
     * @param string      $productCode Accessory Product Code References
     * @param null|string $productEan  Accessory Article Ean found on _embedded
     *
     * @return Line
     */
    public function getAccessoryCopy(string $productCode, ?string $productEan)
    {
        $accessoryLine = clone $this;

        $accessoryLine->productCodeReference = $productCode;
        $accessoryLine->articleEan = $productEan ?: $productCode;
        $accessoryLine->accessories = array();
        $accessoryLine->accessoryLine = true;

        return $accessoryLine;
    }

    /**
     * Gets Customer Ean Code.
     *
     * @return null|string
     */
    public function getEan()
    {
        //====================================================================//
        // Ensure Customer Ean Setup
        if (empty($this->ean)) {
            //====================================================================//
            // If Empty, use Product Reference as Ean
            $this->ean = $this->getProductCodeReference();
        }

        return $this->ean;
    }

    /**
     * Gets Logistical EAN.
     *
     * @return null|string
     */
    public function getArticleEan()
    {
        //====================================================================//
        // Ensure Ean Setup
        if (empty($this->articleEan)) {
            //====================================================================//
            // Detect Ean From _embedded
            $ean = LinesTransformer::getEan($this->getProductCodeReference());
            //====================================================================//
            // If Empty, use Reference as Ean
            $this->articleEan = $ean ?: $this->getProductCodeReference();
        }

        return $this->articleEan;
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
     * Gets productCodeReference.
     *
     * @return string
     */
    public function getProductCodeReference()
    {
        return $this->productCodeReference;
    }

    /**
     * Gets quantity.
     *
     * @return int
     */
    public function getQuantity(): int
    {
        return (int) $this->quantity;
    }

    /**
     * Gets label.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return (string) $this->label;
    }

    /**
     * Gets articleCode.
     *
     * @return string
     */
    public function getArticleCode(): string
    {
        return (string) $this->articleCode;
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
     * Gets Is Accessory Flag.
     *
     * @return bool
     */
    public function isAccessoryLine(): bool
    {
        return (bool) $this->accessoryLine;
    }
}
