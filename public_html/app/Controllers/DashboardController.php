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

        log_message('debug', '=== DASHBOARD CONTROLLER ===');
        log_message('debug', 'Role from session: ' . ($role ?? 'NULL'));
        log_message('debug', 'Session data: ' . json_encode(session()->get()));

        switch ($role) {
            case 'superadmin':
                log_message('debug', 'Redirecting to /superadmin/dashboard');
                return redirect()->to('/superadmin/dashboard');

            case 'admin':
                log_message('debug', 'Redirecting to /admin/dashboard');
                return redirect()->to('/admin/dashboard');

            case 'user':
                log_message('debug', 'Redirecting to /tools');
                return redirect()->to('/tools');

            default:
                // If no valid role, logout and redirect to login
                log_message('debug', 'No valid role, destroying session and redirecting to login');
                session()->destroy();
                return redirect()->to('/login')->with('error', 'Invalid user role. Please contact support.');
        }
    }
}
