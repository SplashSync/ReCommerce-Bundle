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

namespace Splash\Connectors\ReCommerce\Objects\Order;

use Exception;

/**
 * Manage Translation from Boxes to Lines for ReadyMadeBox Orders
 */
trait BoxesLinesTrait
{
    /**
     * In Case of ReadyMadeBox, we complete Order Lines with Boxes Details
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     *
     * @throws Exception
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getLinesFields($key, $fieldName): void
    {
        //====================================================================//
        // Check if List field & Init List Array
        $fieldId = self::lists()->initOutput($this->out, "lines", $fieldName);
        if (!$fieldId || !$this->object->isBoxesToLinesOrder()) {
            return;
        }
        //====================================================================//
        // Fill Line List with Data
        foreach ($this->loadBoxes() as $index => $box) {
            switch ($fieldId) {
                case 'id':
                case 'ean':
                case 'articleEan':
                    $value = $box->name;

                    break;
                case 'quantity':
                    $value = 1;

                    break;
                default:
                    $value = null;

                    break;
            }
            //====================================================================//
            // Insert Data in List
            self::lists()->Insert($this->out, "lines", $fieldName, "box".$index, $value);
        }
    }
}
