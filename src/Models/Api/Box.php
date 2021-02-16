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

use DateTime;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class representing the Box model.
 */
class Box
{
    /**
     * Box name, unique for the API
     *
     * @var string
     *
     * @JMS\SerializedName("name")
     * @JMS\Type("string")
     * @JMS\Groups ({"Read", "Write"})
     */
    public $name;

    /**
     * @var DateTime
     *
     * @Assert\Type("DateTime")
     *
     * @JMS\SerializedName("created")
     * @JMS\Groups ({"Read"})
     */
    public $created;

    /**
     * @var array
     *
     * @Assert\Type("array")
     *
     * @JMS\SerializedName("lineItems")
     * @JMS\Groups ({"Write"})
     */
    public $lineItems = array();

    /**
     * Create a Box form a Parcel Definition
     *
     * @param array $parcel
     *
     * @return null|Box
     */
    public static function createFromParcel(array $parcel): ?Box
    {
        $box = new self();

        if (isset($parcel['id']) && is_string($parcel['id'])) {
            $box->name = $parcel['id'];
        }
        if (isset($parcel['contents']) && is_string($parcel['contents'])) {
            $contents = json_decode($parcel['contents'], true);
            $box->lineItems = array();
            foreach ($contents as $lineId) {
                $box->lineItems[] = array("lineId" => $lineId);
            }
        }

        return $box->isValid() ? $box : null;
    }

    /**
     * Check Box is Valid
     *
     * @return bool
     */
    protected function isValid(): bool
    {
        if (empty($this->name)) {
            return false;
        }

        return true;
    }
}
