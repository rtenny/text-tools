<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;

class LogoutController extends BaseController
{
    /**
     * Logout user and destroy session
     */
    public function index()
    {
        // Destroy all session data
        session()->destroy();

        // Redirect to login with success message
        return redirect()->to('/login')->with('success', 'You have been logged out successfully.');
    }
}
