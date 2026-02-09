<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    /**
     * Role-based dashboard redirect
     *
     * Redirects users to their appropriate dashboard based on their role:
     * - superadmin -> /superadmin/dashboard
     * - admin -> /admin/dashboard
     * - user -> /tools
     */
    public function index()
    {
        $role = session()->get('role');

        switch ($role) {
            case 'superadmin':
                return redirect()->to('/superadmin/dashboard');

            case 'admin':
                return redirect()->to('/admin/dashboard');

            case 'user':
                return redirect()->to('/tools');

            default:
                // If no valid role, logout and redirect to login
                session()->destroy();
                return redirect()->to('/login')->with('error', 'Invalid user role. Please contact support.');
        }
    }
}
