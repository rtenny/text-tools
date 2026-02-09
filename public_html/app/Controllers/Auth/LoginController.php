<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

class LoginController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Display login form
     */
    public function index()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('user_id')) {
            return redirect()->to('/dashboard');
        }

        $data = [
            'title' => 'Login',
        ];

        return view('auth/login', $data);
    }

    /**
     * Authenticate user
     */
    public function authenticate()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Find user by email
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid email or password.');
        }

        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid email or password.');
        }

        // Check if user is active
        if (!$user['is_active']) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Your account has been deactivated. Please contact support.');
        }

        // If user is not superadmin, check if their project is active
        if ($user['role'] !== 'superadmin') {
            $projectModel = new \App\Models\ProjectModel();
            $project = $projectModel->find($user['project_id']);

            if (!$project || !$project['is_active']) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Your project has been deactivated. Please contact your administrator.');
            }
        }

        // Update last login timestamp
        $this->userModel->update($user['id'], [
            'last_login_at' => date('Y-m-d H:i:s'),
        ]);

        // Set session data
        session()->set([
            'user_id' => $user['id'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'role' => $user['role'],
            'project_id' => $user['project_id'],
            'is_active' => $user['is_active'],
            'is_logged_in' => true,
        ]);

        // Determine redirect URL based on role
        $redirectUrl = match ($user['role']) {
            'superadmin' => '/superadmin/dashboard',
            'admin' => '/admin/dashboard',
            'user' => '/tools',
            default => '/dashboard',
        };

        return redirect()->to($redirectUrl)->with('success', 'Welcome back, ' . $user['first_name'] . '!');
    }
}
