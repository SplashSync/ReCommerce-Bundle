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

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class representing the ProductCodeType model.
 *
 * @ApiResource(
 *     collectionOperations={
 *          "get":      { "path": "/product-code-type" },
 *     },
 *     itemOperations={},
 * )
 */
class ProductCodeType
{
    /**
     * Unique identifier representing a ProductCodeType.
     *
     * @var string
     * @Assert\NotNull()
     * @Assert\Type("string")
     *
     * @ApiProperty(identifier=true)
     */
    public $label;

    /**
     * Human-readable name of ProductCodeType.
     *
     * @var string
     * @Assert\NotNull()
     * @Assert\Type("string")
     */
    public $name;

    /**
     * Whether ProductCode attached to this ProductCodeType require a serial number while processing
     *
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type("bool")
     */
    public $expectingSerial;
}
