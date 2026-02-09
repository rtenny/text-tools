<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectModel extends Model
{
    protected $table            = 'projects';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'slug',
        'languages',
        'default_ai_provider',
        'api_key',
        'is_active',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'name'                => 'required|min_length[3]|max_length[255]',
        'slug'                => 'required|alpha_dash|max_length[255]|is_unique[projects.slug,id,{id}]',
        'languages'           => 'required',
        'default_ai_provider' => 'required|in_list[claude,openai]',
        'api_key'             => 'required',
    ];
    protected $validationMessages   = [
        'name' => [
            'required'   => 'Project name is required',
            'min_length' => 'Project name must be at least 3 characters',
        ],
        'slug' => [
            'required'   => 'Project slug is required',
            'alpha_dash' => 'Slug can only contain letters, numbers, dashes and underscores',
            'is_unique'  => 'This slug is already taken',
        ],
        'default_ai_provider' => [
            'required' => 'AI provider is required',
            'in_list'  => 'AI provider must be either claude or openai',
        ],
        'api_key' => [
            'required' => 'API key is required',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateSlug', 'encodeLanguages'];
    protected $beforeUpdate   = ['generateSlug', 'encodeLanguages'];
    protected $afterFind      = ['decodeLanguages'];

    /**
     * Generate slug from name if not provided
     *
     * @param array $data
     * @return array
     */
    protected function generateSlug(array $data): array
    {
        if (isset($data['data']['name']) && empty($data['data']['slug'])) {
            $data['data']['slug'] = url_title($data['data']['name'], '-', true);
        }

        return $data;
    }

    /**
     * Encode languages array to JSON before saving
     *
     * @param array $data
     * @return array
     */
    protected function encodeLanguages(array $data): array
    {
        if (isset($data['data']['languages']) && is_array($data['data']['languages'])) {
            $data['data']['languages'] = json_encode($data['data']['languages']);
        }

        return $data;
    }

    /**
     * Decode languages JSON to array after fetching
     *
     * @param array $data
     * @return array
     */
    protected function decodeLanguages(array $data): array
    {
        if (isset($data['data'])) {
            // Handle single record
            if (isset($data['data']['languages'])) {
                $data['data']['languages'] = json_decode($data['data']['languages'], true);
            }
        } elseif (isset($data['result'])) {
            // Handle multiple records
            foreach ($data['result'] as &$record) {
                if (isset($record['languages'])) {
                    $record['languages'] = json_decode($record['languages'], true);
                }
            }
        }

        return $data;
    }

    /**
     * Get active projects
     *
     * @return array
     */
    public function getActiveProjects(): array
    {
        return $this->where('is_active', 1)->findAll();
    }

    /**
     * Get project by slug
     *
     * @param string $slug
     * @return array|null
     */
    public function getBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }
}
