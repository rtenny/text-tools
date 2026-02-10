<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectTownModel extends Model
{
    protected $table            = 'project_towns';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'project_id',
        'town_id',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';

    // Validation
    protected $validationRules = [
        'project_id' => 'required|integer',
        'town_id'    => 'required|integer',
    ];

    protected $validationMessages = [
        'project_id' => [
            'required' => 'Project ID is required',
            'integer'  => 'Project ID must be an integer',
        ],
        'town_id' => [
            'required' => 'Town ID is required',
            'integer'  => 'Town ID must be an integer',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;

    /**
     * Get all towns for a specific project
     *
     * @param int $projectId
     * @return array
     */
    public function getTownsForProject(int $projectId): array
    {
        return $this->db->table('project_towns')
            ->select('towns.id, towns.name')
            ->join('towns', 'towns.id = project_towns.town_id')
            ->where('project_towns.project_id', $projectId)
            ->orderBy('towns.name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get all town IDs for a specific project
     *
     * @param int $projectId
     * @return array
     */
    public function getTownIdsForProject(int $projectId): array
    {
        $result = $this->select('town_id')
            ->where('project_id', $projectId)
            ->findAll();

        return array_column($result, 'town_id');
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
        // Start transaction
        $this->db->transStart();

        // Remove existing assignments
        $this->where('project_id', $projectId)->delete();

        // Add new assignments
        if (!empty($townIds)) {
            $data = [];
            foreach ($townIds as $townId) {
                $data[] = [
                    'project_id' => $projectId,
                    'town_id'    => $townId,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }
            $this->insertBatch($data);
        }

        // Complete transaction
        $this->db->transComplete();

        return $this->db->transStatus();
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
        if (empty($townIds)) {
            return true;
        }

        return $this->where('project_id', $projectId)
            ->whereIn('town_id', $townIds)
            ->delete();
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
        $result = $this->where([
            'project_id' => $projectId,
            'town_id'    => $townId,
        ])->first();

        return !empty($result);
    }

    /**
     * Get count of projects using a specific town
     *
     * @param int $townId
     * @return int
     */
    public function getProjectCountForTown(int $townId): int
    {
        return $this->where('town_id', $townId)->countAllResults();
    }
}
