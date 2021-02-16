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
use Splash\Client\Splash;
use Splash\Connectors\ReCommerce\Models\Api;
use Splash\OpenApi\Action\JsonHal;
use Splash\OpenApi\Fields as ApiFields;
use Splash\OpenApi\Visitor\JsonHalVisitor;

/**
 * Manage Translation from Parcels to Boxes & Remote Updates
 */
trait BoxesTrait
{
    /**
     * @var null|Api\Box[]
     */
    private $boxes;

    /**
     * @var JsonHalVisitor
     */
    private $boxVisitor;

    /**
     * Build Fields
     *
     * @return void
     */
    protected function buildBoxesFields(): void
    {
        //====================================================================//
        // BOX - Name
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("name")
            ->name("Box name")
            ->inList("boxes")
            ->isReadOnly()
        ;

        //====================================================================//
        // BOX - Date Created
        $this->fieldsFactory()->create(SPL_T_DATETIME)
            ->identifier("created")
            ->name("Date Created")
            ->inList("boxes")
            ->isReadOnly()
        ;
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     *
     * @throws Exception
     */
    protected function getBoxesFields($key, $fieldName): void
    {
        //====================================================================//
        // Check if List field & Init List Array
        $fieldId = self::lists()->initOutput($this->out, "boxes", $fieldName);
        if (!$fieldId) {
            return;
        }
        //====================================================================//
        // Load Boxes List
        $boxes = $this->loadBoxes();
        //====================================================================//
        // Fill Boxes List with Data
        foreach ($boxes as $index => $box) {
            //====================================================================//
            // Read Raw value
            $value = ApiFields\Getter::get(Api\Box::class, $box, $fieldId);
            //====================================================================//
            // Insert Data in List
            self::lists()->Insert($this->out, "boxes", $fieldName, $index, $value);
        }
        unset($this->in[$key]);
    }

    /**
     * Update List of Boxes for a Shipment
     *
     * @param API\Box[] $newBoxes
     *
     * @throws Exception
     *
     * @return void
     */
    protected function updateBoxes(array $newBoxes): void
    {
        $errors = 0;
        //====================================================================//
        // Create Boxes Visitor
        $visitor = $this->getBoxesVisitor((string) $this->getObjectIdentifier());
        //====================================================================//
        // Load Boxes List
        $currentBoxes = $this->loadBoxes();
        //====================================================================//
        // Walk on NEW Boxes List
        foreach ($newBoxes as $box) {
            //====================================================================//
            // Search for Box by Name
            $currentBox = $this->findBox($currentBoxes, $box->name);
            if (!$currentBox) {
                //====================================================================//
                // Create a new Box from API
                if (!$visitor->create($box, false)->isSuccess()) {
                    Splash::log()->errTrace("Unable to create box ".$box->name);
                    $errors++;
                };
            }
        }
        //====================================================================//
        // Delete Remaining Boxes
        foreach ($currentBoxes as $box) {
            if (!$visitor->delete($box->name)->isSuccess()) {
                Splash::log()->errTrace("Unable to delete box ".$box->name);
                $errors++;
            };
        }

        unset($this->in["boxes"]);
    }

    /**
     * Get Shipment Boxes API Visitor
     *
     * @param string $shipmentId
     *
     * @throws Exception
     *
     * @return JsonHalVisitor
     */
    private function getBoxesVisitor(string $shipmentId): JsonHalVisitor
    {
        if (!isset($this->boxVisitor)) {
            $this->boxVisitor = new JsonHalVisitor(
                $this->getVisitor()->getConnexion(),
                $this->getVisitor()->getHydrator(),
                Api\Box::class
            ) ;

            $this->boxVisitor
                ->setListAction(JsonHal\ListAction::class, array("raw" => true))
            ;
        }

        $this->boxVisitor->setModel(
            Api\Box::class,
            "/shipment/".$shipmentId."/box",
            "/shipment/".$shipmentId."/box/{id}"
        );

        return $this->boxVisitor;
    }

    /**
     * Load Shipment Boxes from API
     *
     * @throws Exception
     *
     * @return API\Box[]
     */
    private function loadBoxes(): array
    {
        //====================================================================//
        // Already Loaded
        if (isset($this->boxes) && is_array($this->boxes)) {
            return $this->boxes;
        }
        //====================================================================//
        // Check Shipment Boxes Count
        if (empty($this->object->getCountBoxes())) {
            return $this->boxes = array();
        }
        //====================================================================//
        // Load Boxes List from API
        $listResponse = $this->getBoxesVisitor((string) $this->getObjectIdentifier())->list();
        $this->boxes = $listResponse->isSuccess() ? $listResponse->getResults() : array();

        return $this->boxes;
    }

    /**
     * Load Shipment Boxes from API
     *
     * @param API\Box[] $boxes
     * @param string    $boxName
     *
     * @return null|API\Box
     */
    private function findBox(array &$boxes, string $boxName): ?API\Box
    {
        foreach ($boxes as $index => $box) {
            if ($box->name == $boxName) {
                unset($boxes[$index]);

                return $box;
            }
        }

        return null;
    }
}
