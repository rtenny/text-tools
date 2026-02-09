<?php

namespace App\Models;

use CodeIgniter\Model;

class PasswordResetModel extends Model
{
    protected $table            = 'password_resets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'token',
        'expires_at',
        'used_at',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = null;
    protected $deletedField  = null;

    // Validation
    protected $validationRules      = [
        'user_id'    => 'required|integer',
        'token'      => 'required|max_length[32]',
        'expires_at' => 'required|valid_date',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setCreatedAt'];

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
     * Get valid token for user
     *
     * @param int $userId
     * @param string $token
     * @return array|null
     */
    public function getValidToken(int $userId, string $token): ?array
    {
        return $this->where('user_id', $userId)
            ->where('token', $token)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->where('used_at', null)
            ->first();
    }

    /**
     * Mark token as used
     *
     * @param int $id
     * @return bool
     */
    public function markAsUsed(int $id): bool
    {
        return $this->update($id, ['used_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Delete expired tokens
     *
     * @return int Number of deleted records
     */
    public function deleteExpiredTokens(): int
    {
        return $this->where('expires_at <', date('Y-m-d H:i:s'))->delete();
    }

    /**
     * Delete all tokens for a user
     *
     * @param int $userId
     * @return bool
     */
    public function deleteUserTokens(int $userId): bool
    {
        return $this->where('user_id', $userId)->delete();
    }
}
