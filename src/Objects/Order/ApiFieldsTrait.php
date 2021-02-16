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
use Splash\OpenApi\Fields as ApiFields;

trait ApiFieldsTrait
{
    /**
     * Build Objects Fields from OpenApi Model.
     *
     * @throws Exception
     */
    protected function buildApiFields()
    {
        ApiFields\Builder::buildModelFields($this->fieldsFactory(), $this->model);
    }

    /**
     * Read API Simple Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     *
     * @throws Exception
     */
    protected function getApiSimpleFields(string $key, string $fieldName): void
    {
        //====================================================================//
        // Check if Field Exists for Reading
        if (!ApiFields\Getter::has($this, $fieldName)) {
            return;
        }
        //====================================================================//
        // Read Data
        $this->out[$fieldName] = ApiFields\Getter::get($this, $this->object, $fieldName);
        unset($this->in[$key]);
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     *
     * @throws Exception
     */
    protected function getApiListFields(string $key, string $fieldName): void
    {
        //====================================================================//
        // Check if Field is a List field
        $listInfos = $this->lists()->isListField($fieldName);
        if (!$listInfos) {
            return;
        }
        //====================================================================//
        // Check if List field & Init List Array
        $listName = (string) $this->lists()->listName($fieldName);
        $fieldId = self::lists()->initOutput($this->out, (string) $this->lists()->listName($fieldName), $fieldName);
        if (!$fieldId) {
            return;
        }
        //====================================================================//
        // Fill List with Data
        foreach (ApiFields\Getter::getListData($this->object, $listName, $fieldId) as $index => $data) {
            //====================================================================//
            // Insert Data in List
            self::lists()->Insert($this->out, $listName, $fieldName, $index, $data);
        }
        unset($this->in[$key]);
    }

    /**
     * Write Given Fields
     *
     * @param string $fieldName Field Identifier / Name
     * @param mixed  $fieldData Field Data
     *
     * @throws Exception
     *
     * @return void
     */
    protected function setApiSimpleFields(string $fieldName, $fieldData): void
    {
        //====================================================================//
        // Check if Field Exists for Writing
        if (!ApiFields\Setter::has($this, $fieldName)) {
            return;
        }
        //====================================================================//
        // Write Data
        $result = ApiFields\Setter::set($this, $this->object, $fieldName, $fieldData);
        //====================================================================//
        // Write Fail
        if (is_null($result)) {
            return;
        }
        unset($this->in[$fieldName]);
        //====================================================================//
        // Data was Updated
        if ($result) {
            $this->needUpdate();
        }
    }
}
