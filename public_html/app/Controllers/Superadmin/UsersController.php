<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\ProjectModel;
use App\Libraries\PasswordResetService;

class UsersController extends BaseController
{
    protected $userModel;
    protected $projectModel;
    protected $passwordResetService;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->projectModel = new ProjectModel();
        $this->passwordResetService = new PasswordResetService();
    }

    public function index()
    {
        // Get all admins with their project information
        $admins = $this->userModel
            ->select('users.*, projects.name as project_name')
            ->join('projects', 'projects.id = users.project_id', 'left')
            ->where('users.role', 'admin')
            ->orderBy('users.created_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Project Admins',
            'admins' => $admins,
        ];

        return view('superadmin/users/index', $data);
    }

    public function create()
    {
        // Get all projects for the dropdown
        $projects = $this->projectModel
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Create Admin',
            'projects' => $projects,
        ];

        return view('superadmin/users/create', $data);
    }

    public function store()
    {
        $rules = [
            'email' => 'required|valid_email|is_unique[users.email]',
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name' => 'required|min_length[2]|max_length[100]',
            'project_id' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Verify project exists
        $project = $this->projectModel->find($this->request->getPost('project_id'));
        if (!$project) {
            return redirect()->back()->withInput()->with('error', 'Selected project not found.');
        }

        // Create admin with temporary password (they'll use password reset link)
        $tempPassword = bin2hex(random_bytes(16));

        $data = [
            'project_id' => $this->request->getPost('project_id'),
            'email' => $this->request->getPost('email'),
            'password_hash' => password_hash($tempPassword, PASSWORD_DEFAULT),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'role' => 'admin',
            'is_active' => 1,
        ];

        $userId = $this->userModel->insert($data);

        if ($userId) {
            // Generate password reset link
            $resetLink = $this->passwordResetService->createResetLink($userId);

            return redirect()->to('superadmin/users')
                ->with('success', 'Admin created successfully.')
                ->with('reset_link', $resetLink)
                ->with('admin_email', $this->request->getPost('email'));
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create admin. Please try again.');
        }
    }

    public function generatePasswordResetLink($userId)
    {
        $user = $this->userModel->find($userId);

        if (!$user || $user['role'] !== 'admin') {
            return redirect()->to('superadmin/users')->with('error', 'Admin not found.');
        }

        $resetLink = $this->passwordResetService->createResetLink($userId);

        $data = [
            'title' => 'Password Reset Link',
            'user' => $user,
            'resetLink' => $resetLink,
        ];

        return view('superadmin/users/password_reset_modal', $data);
    }
}
