<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table            = 'activity_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'project_id',
        'action',
        'ai_provider',
        'input_language',
        'output_languages',
        'tokens_used',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = null;
    protected $deletedField  = null;

    // Validation
    protected $validationRules      = [
        'user_id'        => 'required|integer',
        'action'         => 'required|max_length[100]',
        'ai_provider'    => 'required|in_list[claude,openai]',
        'input_language' => 'required|max_length[5]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setCreatedAt', 'encodeOutputLanguages'];
    protected $afterFind      = ['decodeOutputLanguages'];

    /**
     * Set created_at timestamp
     *
     * @param array $data
     * @return array
     */
    protected function setCreatedAt(array $data): array
    {
        $data['data']['created_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    /**
     * Encode output_languages array to JSON before saving
     *
     * @param array $data
     * @return array
     */
    protected function encodeOutputLanguages(array $data): array
    {
        if (isset($data['data']['output_languages']) && is_array($data['data']['output_languages'])) {
            $data['data']['output_languages'] = json_encode($data['data']['output_languages']);
        }

        return $data;
    }

    /**
     * Decode output_languages JSON to array after fetching
     *
     * @param array $data
     * @return array
     */
    protected function decodeOutputLanguages(array $data): array
    {
        if (isset($data['data'])) {
            // Handle single record
            if (isset($data['data']['output_languages'])) {
                $data['data']['output_languages'] = json_decode($data['data']['output_languages'], true);
            }
        } elseif (isset($data['result'])) {
            // Handle multiple records
            foreach ($data['result'] as &$record) {
                if (isset($record['output_languages'])) {
                    $record['output_languages'] = json_decode($record['output_languages'], true);
                }
            }
        }

        return $data;
    }

    /**
     * Get logs by user
     *
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getByUser(int $userId, int $limit = 50): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get logs by project
     *
     * @param int $projectId
     * @param int $limit
     * @return array
     */
    public function getByProject(int $projectId, int $limit = 50): array
    {
        return $this->where('project_id', $projectId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get recent activity
     *
     * @param int $limit
     * @return array
     */
    public function getRecentActivity(int $limit = 20): array
    {
        return $this->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get total tokens used by project
     *
     * @param int $projectId
     * @return int
     */
    public function getTotalTokensByProject(int $projectId): int
    {
        $result = $this->selectSum('tokens_used')
            ->where('project_id', $projectId)
            ->first();

        return $result['tokens_used'] ?? 0;
    }
}
