<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\PasswordResetModel;
use App\Libraries\PasswordResetService;

class PasswordResetController extends BaseController
{
    protected $userModel;
    protected $passwordResetModel;
    protected $passwordResetService;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->passwordResetModel = new PasswordResetModel();
        $this->passwordResetService = new PasswordResetService();
    }

    /**
     * Verify password reset token and display form
     */
    public function verify($userId, $token)
    {
        // Find user
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Invalid password reset link.');
        }

        // Validate token
        if (!$this->passwordResetService->validateToken($userId, $token)) {
            return redirect()->to('/login')->with('error', 'This password reset link has expired or is invalid. Please contact your administrator for a new link.');
        }

        // Check if token has already been used
        $existingReset = $this->passwordResetModel
            ->where('user_id', $userId)
            ->where('token', $token)
            ->where('used_at IS NOT NULL')
            ->first();

        if ($existingReset) {
            return redirect()->to('/login')->with('error', 'This password reset link has already been used. Please contact your administrator for a new link.');
        }

        $data = [
            'title' => 'Reset Password',
            'user' => $user,
            'userId' => $userId,
            'token' => $token,
        ];

        return view('auth/password_reset_form', $data);
    }

    /**
     * Process password reset
     */
    public function reset($userId, $token)
    {
        // Find user
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Invalid password reset link.');
        }

        // Validate token again
        if (!$this->passwordResetService->validateToken($userId, $token)) {
            return redirect()->to('/login')->with('error', 'This password reset link has expired or is invalid.');
        }

        // Validate password input
        $rules = [
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $password = $this->request->getPost('password');

        // Update user password
        $this->userModel->update($userId, [
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        // Mark token as used (if exists in database)
        $existingReset = $this->passwordResetModel
            ->where('user_id', $userId)
            ->where('token', $token)
            ->first();

        if ($existingReset) {
            $this->passwordResetModel->update($existingReset['id'], [
                'used_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            // Create a record for this token usage
            $this->passwordResetModel->insert([
                'user_id' => $userId,
                'token' => $token,
                'expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour')),
                'used_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // Redirect to login with success message
        return redirect()->to('/login')->with('success', 'Your password has been reset successfully. You can now login with your new password.');
    }
}
