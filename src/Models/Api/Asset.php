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

namespace Splash\Connectors\ReCommerce\Models\Api;

use JMS\Serializer\Annotation as JMS;
use Psr\SimpleCache\InvalidArgumentException;
use Splash\Connectors\ReCommerce\DataTransformer\AssetTransformer;
use Splash\OpenApi\Validator as SPL;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class representing the Asset model.
 */
class Asset
{
    /**
     * Unique identifier for the Asset − machine-readable
     *
     * @var int
     *
     * @Assert\Type("integer")
     *
     * @JMS\SerializedName("id")
     * @JMS\Type("integer")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Microdata({"http://schema.org/DigitalDocument", "identifier"})
     */
    public $id;

    /**
     * Standard formatted URL to the asset. When this field is null, the file attached to the asset is not available.
     * Using POST ../asset/:id/upload will set this value.
     *
     * @var string
     *
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("url")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Type("url")
     * @SPL\Microdata({"http://schema.org/DigitalDocument", "url"})
     */
    public $url;

    /**
     * A unique name among all shipments assets − human-readable. Contains the file extension (ex: myfile.pdf).
     *
     * @var string
     *
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @JMS\SerializedName("name")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Microdata({"http://schema.org/DigitalDocument", "alternateName"})
     */
    public $name;

    /**
     * Categorize the asset
     *
     * @var string
     *
     * @Assert\NotNull()
     * @Assert\Type("string")
     * @Assert\Choice({
     *     "orderSummary": "Order Summary",
     *     "transportDocument": "Transport Document",
     *     "transportProof": "Transport Proof",
     *     "other": "Other Document"
     * })
     *
     * @JMS\SerializedName("tag")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Microdata({"http://schema.org/DigitalDocument", "name"})
     */
    protected $tag;

    /**
     * Asset Splash Definition Array
     *
     * @var null|array
     * @JMS\SerializedName("file")
     * @JMS\Type("array")
     * @JMS\Groups ({"Read"})
     *
     * @SPL\Type("stream")
     * @SPL\Microdata({"http://schema.org/DigitalDocument", "description"})
     */
    protected $file;

    //====================================================================//
    // SPECIAL GETTERS
    //====================================================================//

    /**
     * @return string
     */
    public function getTag(): string
    {
        switch ($this->tag) {
            case "orderSummary":
                return "BL";
            default:
                return $this->tag;
        }
    }

    /**
     * @return null|array
     */
    public function getFile(): ?array
    {
        try {
            return AssetTransformer::getInfos($this);
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }
}
