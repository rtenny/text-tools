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
        // Debug logging
        log_message('debug', '=== LOGIN ATTEMPT START ===');
        log_message('debug', 'POST data: ' . json_encode($this->request->getPost()));
        log_message('debug', 'Request method: ' . $this->request->getMethod());

        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            log_message('debug', 'Validation failed: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        log_message('debug', 'Email from POST: ' . ($email ?? 'NULL'));
        log_message('debug', 'Password from POST: ' . (empty($password) ? 'EMPTY' : 'SET'));

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

        log_message('debug', 'User authenticated successfully: ' . $user['email']);

        // Set session data
        $sessionData = [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'role' => $user['role'],
            'project_id' => $user['project_id'],
            'is_logged_in' => true,
        ];

        log_message('debug', 'Setting session data: ' . json_encode($sessionData));
        session()->set($sessionData);

        // Verify session was set
        $verifyUserId = session()->get('user_id');
        $verifyLoggedIn = session()->get('is_logged_in');
        log_message('debug', 'Session verification - user_id: ' . ($verifyUserId ?? 'NULL') . ', is_logged_in: ' . ($verifyLoggedIn ? 'true' : 'false'));

        // Determine redirect URL based on role
        $redirectUrl = match ($user['role']) {
            'superadmin' => '/superadmin/dashboard',
            'admin' => '/admin/dashboard',
            'user' => '/tools',
            default => '/dashboard', // Fallback to generic dashboard
        };

        log_message('debug', '=== LOGIN ATTEMPT END - REDIRECTING TO: ' . $redirectUrl . ' ===');

        // Redirect directly to role-specific dashboard
        // CI4 automatically uses 303 for POST requests (forces GET on redirect)
        return redirect()->to($redirectUrl)->with('success', 'Welcome back, ' . $user['first_name'] . '!');
    }
}
