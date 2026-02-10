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

        // Validate token (checks existence, expiry, and used status)
        if (!$this->passwordResetService->validateToken($userId, $token)) {
            return redirect()->to('/login')->with('error', 'This password reset link has expired, already been used, or is invalid. Please contact your administrator for a new link.');
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

        // Update user password (skip validation since we're only updating the password)
        $this->userModel->skipValidation(true)->update($userId, [
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        // Mark token as used
        $resetRecord = $this->passwordResetModel
            ->where('user_id', $userId)
            ->where('token', $token)
            ->first();

        if ($resetRecord) {
            $this->passwordResetModel->markAsUsed($resetRecord['id']);
        }

        // Redirect to login with success message
        return redirect()->to('/login')->with('success', 'Your password has been reset successfully. You can now login with your new password.');
    }
}
