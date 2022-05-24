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

namespace Splash\Connectors\ReCommerce\Objects\Order;

use Exception;
use Splash\Client\Splash;
use Splash\Connectors\ReCommerce\Models\Api;
use Splash\OpenApi\Action\JsonHal;
use Splash\OpenApi\Fields as ApiFields;
use Splash\OpenApi\Visitor\JsonHalVisitor;

/**
 * Manage Translation from Parcels to Transport Units & Remote Updates
 */
trait TransportUnitsTrait
{
    /**
     * @var null|Api\TransportUnit[]
     */
    private ?array $units;

    /**
     * @var null|JsonHalVisitor
     */
    private ?JsonHalVisitor $unitsVisitor;

    /**
     * Build Fields
     *
     * @return void
     */
    protected function buildTransportUnitsFields(): void
    {
        //====================================================================//
        // TRANSPORT UNIT - ID
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("id")
            ->name("Unit Id")
            ->inList("transportUnits")
            ->isReadOnly()
        ;
        //====================================================================//
        // TRANSPORT UNIT - Name
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("name")
            ->name("Unit name")
            ->inList("transportUnits")
            ->isReadOnly()
        ;
        //====================================================================//
        // TRANSPORT UNIT - Type
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("type")
            ->name("Type")
            ->inList("transportUnits")
            ->isReadOnly()
        ;
        //====================================================================//
        // TRANSPORT UNIT - Tracking Number
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("trackingNumber")
            ->name("Tracking")
            ->inList("transportUnits")
            ->isReadOnly()
        ;
        //====================================================================//
        // TRANSPORT UNIT - Number of Attached Boxes
        $this->fieldsFactory()->create(SPL_T_INT)
            ->identifier("countBoxes")
            ->name("Nb Boxes")
            ->inList("transportUnits")
            ->isReadOnly()
        ;
        //====================================================================//
        // TRANSPORT UNIT - Weight
        $this->fieldsFactory()->create(SPL_T_DOUBLE)
            ->identifier("weight")
            ->name("Weight (kg)")
            ->inList("transportUnits")
            ->isReadOnly()
        ;
        //====================================================================//
        // TRANSPORT UNIT - Height
        $this->fieldsFactory()->create(SPL_T_DOUBLE)
            ->identifier("height")
            ->name("Height (cm)")
            ->inList("transportUnits")
            ->isReadOnly()
        ;
        //====================================================================//
        // TRANSPORT UNIT - Width
        $this->fieldsFactory()->create(SPL_T_DOUBLE)
            ->identifier("width")
            ->name("Width (cm)")
            ->inList("transportUnits")
            ->isReadOnly()
        ;
        //====================================================================//
        // TRANSPORT UNIT - Depth
        $this->fieldsFactory()->create(SPL_T_DOUBLE)
            ->identifier("depth")
            ->name("Depth (cm)")
            ->inList("transportUnits")
            ->isReadOnly()
        ;
        //====================================================================//
        // TRANSPORT UNIT - Date Created
        $this->fieldsFactory()->create(SPL_T_DATETIME)
            ->identifier("created")
            ->name("Date Created")
            ->inList("transportUnits")
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
    protected function getTransportUnitsFields(string $key, string $fieldName): void
    {
        //====================================================================//
        // Check if List field & Init List Array
        $fieldId = self::lists()->initOutput($this->out, "transportUnits", $fieldName);
        if (!$fieldId) {
            return;
        }
        //====================================================================//
        // Load Transport Units List
        $units = $this->loadTransportUnits();
        //====================================================================//
        // Fill Units List with Data
        foreach ($units as $index => $unit) {
            //====================================================================//
            // Read Raw value
            $value = ApiFields\Getter::get(Api\TransportUnit::class, $unit, $fieldId);
            //====================================================================//
            // Insert Data in List
            self::lists()->Insert($this->out, "transportUnits", $fieldName, $index, $value);
        }
        unset($this->in[$key]);
    }

    /**
     * Update List of Transport Units for a Shipment
     *
     * @param API\TransportUnit[] $newUnits
     *
     * @throws Exception
     *
     * @return void
     */
    protected function updateTransportUnits(array $newUnits): void
    {
        $errors = 0;
        $toCreateUnits = array();
        //====================================================================//
        // Create Transport Units Visitor
        $visitor = $this->getUnitsVisitor((string) $this->getObjectIdentifier());
        //====================================================================//
        // Load Units List
        $currentUnits = $this->loadTransportUnits();
        //====================================================================//
        // Walk on NEW Boxes List
        foreach ($newUnits as $unit) {
            //====================================================================//
            // Search for Unit by Name
            $currentUnit = $this->findTransportUnit($currentUnits, $unit);
            if (!$currentUnit) {
                //====================================================================//
                // Mark Transport Unit for Creation
                $toCreateUnits[] = $unit;
            }
        }
        //====================================================================//
        // Delete Remaining/Updated Transport Units
        foreach ($currentUnits as $unit) {
            if (!empty($unit->id) && !$visitor->delete($unit->id)->isSuccess()) {
                Splash::log()->errTrace("Unable to delete transport unit ".$unit->id);
                $errors++;
            }
        }
        //====================================================================//
        // Create New Transport Units
        foreach ($toCreateUnits as $unit) {
            //====================================================================//
            // Create a new Box from API
            if (!$visitor->create($unit, false)->isSuccess()) {
                Splash::log()->errTrace("Unable to create transport unit ".$unit->name);
                $errors++;
            }
        }

        unset($this->in["transportUnits"]);
    }

    /**
     * Get Shipment Transport Units API Visitor
     *
     * @param string $shipmentId
     *
     * @throws Exception
     *
     * @return JsonHalVisitor
     */
    private function getUnitsVisitor(string $shipmentId): JsonHalVisitor
    {
        if (!isset($this->unitsVisitor)) {
            $this->unitsVisitor = new JsonHalVisitor(
                $this->getVisitor()->getConnexion(),
                $this->getVisitor()->getHydrator(),
                Api\TransportUnit::class
            ) ;

            $this->unitsVisitor
                ->setListAction(JsonHal\ListAction::class, array("raw" => true))
            ;
        }

        $this->unitsVisitor->setModel(
            Api\TransportUnit::class,
            "/shipment/".$shipmentId."/transport-unit",
            "/shipment/".$shipmentId."/transport-unit/{id}"
        );

        return $this->unitsVisitor;
    }

    /**
     * Load Shipment Transport Units from API
     *
     * @throws Exception
     *
     * @return API\TransportUnit[]
     */
    private function loadTransportUnits(): array
    {
        //====================================================================//
        // Already Loaded
        if (isset($this->units) && is_array($this->units)) {
            return $this->units;
        }
        //====================================================================//
        // Check Shipment Units Count
        if (empty($this->object->getCountTransportUnits())) {
            return $this->units = array();
        }
        //====================================================================//
        // Load Units Paginated List from API
        $listResponse = $this
            ->getUnitsVisitor((string) $this->getObjectIdentifier())
            ->listWithPagination(null, 250, 2000)
        ;
        /** @var API\TransportUnit[] $units */
        $units = $listResponse->isSuccess() ? $listResponse->getResults() : array();

        return $this->units = $units;
    }

    /**
     * Find Transport Units in Loaded List
     *
     * @param API\TransportUnit[] $currentUnits
     * @param API\TransportUnit   $expectedUnit
     *
     * @return null|API\TransportUnit
     */
    private function findTransportUnit(array &$currentUnits, API\TransportUnit $expectedUnit): ?API\TransportUnit
    {
        foreach ($currentUnits as $index => $unit) {
            if ($unit->getCheckSum() == $expectedUnit->getCheckSum()) {
                unset($currentUnits[$index]);

                return $unit;
            }
        }

        return null;
    }
}
