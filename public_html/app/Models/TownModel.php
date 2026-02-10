<?php

namespace App\Models;

use CodeIgniter\Model;

class TownModel extends Model
{
    protected $table            = 'towns';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[255]|is_unique[towns.name,id,{id}]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Town name is required',
            'min_length' => 'Town name must be at least 2 characters',
            'max_length' => 'Town name must not exceed 255 characters',
            'is_unique'  => 'This town already exists',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;

    /**
     * Get all towns ordered alphabetically
     *
     * @return array
     */
    public function getAllTowns(): array
    {
        return $this->orderBy('name', 'ASC')->findAll();
    }

    /**
     * Search towns by name
     *
     * @param string $search
     * @return array
     */
    public function searchTowns(string $search): array
    {
        return $this->like('name', $search)->orderBy('name', 'ASC')->findAll();
    }

    /**
     * Get town by name
     *
     * @param string $name
     * @return array|null
     */
    public function getByName(string $name): ?array
    {
        return $this->where('name', $name)->first();
    }
}
