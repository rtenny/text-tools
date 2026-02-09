<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Libraries\PasswordResetService;

class UsersController extends BaseController
{
    protected $userModel;
    protected $passwordResetService;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->passwordResetService = new PasswordResetService();
    }

    /**
     * List all users for admin's project
     */
    public function index()
    {
        $projectId = session()->get('project_id');

        if (!$projectId) {
            return redirect()->to('/login')->with('error', 'Your account is not properly configured.');
        }

        // Get users for this project only
        $users = $this->userModel
            ->where('project_id', $projectId)
            ->where('role', 'user')
            ->orderBy('created_at', 'DESC')
            ->find();

        $data = [
            'title' => 'Manage Users',
            'users' => $users,
        ];

        return view('admin/users/index', $data);
    }

    /**
     * Show create user form
     */
    public function create()
    {
        $data = [
            'title' => 'Create User',
        ];

        return view('admin/users/create', $data);
    }

    /**
     * Store new user
     */
    public function store()
    {
        $projectId = session()->get('project_id');

        if (!$projectId) {
            return redirect()->to('/login')->with('error', 'Your account is not properly configured.');
        }

        // Validation rules
        $rules = [
            'email' => 'required|valid_email|is_unique[users.email]',
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name' => 'required|min_length[2]|max_length[50]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Create user with temporary password (will use password reset link)
        $temporaryPassword = bin2hex(random_bytes(16)); // Generate random password
        $passwordHash = password_hash($temporaryPassword, PASSWORD_BCRYPT);

        $userData = [
            'project_id' => $projectId, // Auto-assign to admin's project
            'email' => $this->request->getPost('email'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'role' => 'user', // Fixed role
            'password_hash' => $passwordHash,
            'is_active' => 1,
        ];

        $userId = $this->userModel->insert($userData);

        if ($userId) {
            return redirect()->to('/admin/users')
                ->with('success', 'User created successfully. Generate a password reset link to send to them.');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to create user. Please try again.');
    }

    /**
     * Generate password reset link for user
     */
    public function generatePasswordResetLink($userId)
    {
        $projectId = session()->get('project_id');

        if (!$projectId) {
            return redirect()->to('/login')->with('error', 'Your account is not properly configured.');
        }

        // Verify user belongs to admin's project
        $user = $this->userModel->find($userId);

        if (!$user || $user['project_id'] != $projectId) {
            return redirect()->to('/admin/users')
                ->with('error', 'User not found or access denied.');
        }

        // Generate reset link
        $resetLink = $this->passwordResetService->createResetLink($userId);

        $data = [
            'title' => 'Password Reset Link',
            'user' => $user,
            'resetLink' => $resetLink,
        ];

        return view('admin/users/password_reset_link', $data);
    }
}
