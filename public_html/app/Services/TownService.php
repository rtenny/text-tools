<?php

namespace App\Services;

use App\Models\TownModel;
use App\Models\ProjectTownModel;

class TownService
{
    protected $townModel;
    protected $projectTownModel;

    public function __construct()
    {
        $this->townModel = new TownModel();
        $this->projectTownModel = new ProjectTownModel();
    }

    /**
     * Get all available towns (for selection)
     *
     * @return array
     */
    public function getAllAvailableTowns(): array
    {
        return $this->townModel->getAllTowns();
    }

    /**
     * Get towns assigned to a specific project
     *
     * @param int $projectId
     * @return array
     */
    public function getTownsForProject(int $projectId): array
    {
        return $this->projectTownModel->getTownsForProject($projectId);
    }

    /**
     * Get town IDs assigned to a specific project
     *
     * @param int $projectId
     * @return array
     */
    public function getTownIdsForProject(int $projectId): array
    {
        return $this->projectTownModel->getTownIdsForProject($projectId);
    }

    /**
     * Assign towns to a project (replaces existing assignments)
     *
     * @param int $projectId
     * @param array $townIds
     * @return bool
     */
    public function assignTownsToProject(int $projectId, array $townIds): bool
    {
        // Remove empty values and ensure integers
        $townIds = array_filter($townIds, function($id) {
            return !empty($id) && is_numeric($id);
        });
        $townIds = array_map('intval', $townIds);

        // Validate that all town IDs exist
        if (!empty($townIds)) {
            $existingTowns = $this->townModel->whereIn('id', $townIds)->findAll();
            if (count($existingTowns) !== count($townIds)) {
                return false; // Some town IDs don't exist
            }
        }

        return $this->projectTownModel->assignTownsToProject($projectId, $townIds);
    }

    /**
     * Remove specific towns from a project
     *
     * @param int $projectId
     * @param array $townIds
     * @return bool
     */
    public function removeTownsFromProject(int $projectId, array $townIds): bool
    {
        return $this->projectTownModel->removeTownsFromProject($projectId, $townIds);
    }

    /**
     * Check if a town is assigned to a project
     *
     * @param int $projectId
     * @param int $townId
     * @return bool
     */
    public function isTownAssignedToProject(int $projectId, int $townId): bool
    {
        return $this->projectTownModel->isTownAssignedToProject($projectId, $townId);
    }

    /**
     * Validate that a town belongs to a project (for form validation)
     *
     * @param int $projectId
     * @param string $townName
     * @return bool
     */
    public function validateTownForProject(int $projectId, string $townName): bool
    {
        // Get the town by name
        $town = $this->townModel->getByName($townName);
        if (!$town) {
            return false;
        }

        // Check if it's assigned to the project
        return $this->isTownAssignedToProject($projectId, $town['id']);
    }

    /**
     * Create a new town
     *
     * @param string $name
     * @return int|false Town ID on success, false on failure
     */
    public function createTown(string $name)
    {
        $data = ['name' => trim($name)];

        if ($this->townModel->insert($data)) {
            return $this->townModel->getInsertID();
        }

        return false;
    }

    /**
     * Update a town
     *
     * @param int $townId
     * @param string $name
     * @return bool
     */
    public function updateTown(int $townId, string $name): bool
    {
        return $this->townModel->update($townId, ['name' => trim($name)]);
    }

    /**
     * Delete a town (will cascade delete project associations)
     *
     * @param int $townId
     * @return bool
     */
    public function deleteTown(int $townId): bool
    {
        return $this->townModel->delete($townId);
    }

    /**
     * Get count of projects using a specific town
     *
     * @param int $townId
     * @return int
     */
    public function getProjectCountForTown(int $townId): int
    {
        return $this->projectTownModel->getProjectCountForTown($townId);
    }

    /**
     * Search towns by name
     *
     * @param string $search
     * @return array
     */
    public function searchTowns(string $search): array
    {
        return $this->townModel->searchTowns($search);
    }
}
