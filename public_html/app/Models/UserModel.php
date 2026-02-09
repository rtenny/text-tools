<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'project_id',
        'email',
        'password_hash',
        'first_name',
        'last_name',
        'role',
        'is_active',
        'last_login_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'email'      => 'required|valid_email|max_length[255]|is_unique[users.email,id,{id}]',
        'first_name' => 'required|min_length[2]|max_length[100]',
        'last_name'  => 'required|min_length[2]|max_length[100]',
        'role'       => 'required|in_list[superadmin,admin,user]',
    ];
    protected $validationMessages   = [
        'email' => [
            'required'    => 'Email address is required',
            'valid_email' => 'Please provide a valid email address',
            'is_unique'   => 'This email is already registered',
        ],
        'first_name' => [
            'required'   => 'First name is required',
            'min_length' => 'First name must be at least 2 characters',
        ],
        'last_name' => [
            'required'   => 'Last name is required',
            'min_length' => 'Last name must be at least 2 characters',
        ],
        'role' => [
            'required' => 'User role is required',
            'in_list'  => 'Role must be superadmin, admin, or user',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    /**
     * Hash password before saving
     *
     * @param array $data
     * @return array
     */
    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
            unset($data['data']['password']);
        }

        return $data;
    }

    /**
     * Verify user credentials
     *
     * @param string $email
     * @param string $password
     * @return array|null
     */
    public function verifyCredentials(string $email, string $password): ?array
    {
        $user = $this->where('email', $email)->where('is_active', 1)->first();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Update last login time
            $this->update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
            return $user;
        }

        return null;
    }

    /**
     * Get users by project ID
     *
     * @param int $projectId
     * @return array
     */
    public function getUsersByProject(int $projectId): array
    {
        return $this->where('project_id', $projectId)->findAll();
    }

    /**
     * Get users by role
     *
     * @param string $role
     * @return array
     */
    public function getUsersByRole(string $role): array
    {
        return $this->where('role', $role)->findAll();
    }

    /**
     * Get project admins
     *
     * @return array
     */
    public function getAdmins(): array
    {
        return $this->where('role', 'admin')->findAll();
    }

    /**
     * Get superadmins
     *
     * @return array
     */
    public function getSuperadmins(): array
    {
        return $this->where('role', 'superadmin')->findAll();
    }

    /**
     * Check if email exists
     *
     * @param string $email
     * @param int|null $excludeId
     * @return bool
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $builder = $this->where('email', $email);

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }
}
