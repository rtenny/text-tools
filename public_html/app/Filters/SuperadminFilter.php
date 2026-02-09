<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * SuperadminFilter
 *
 * Ensures user has superadmin role before accessing superadmin routes.
 */
class SuperadminFilter implements FilterInterface
{
    /**
     * Check if user is superadmin before request
     *
     * @param RequestInterface $request
     * @param mixed|null $arguments
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        log_message('debug', '=== SUPERADMIN FILTER CHECK ===');
        log_message('debug', 'Session ID: ' . session_id());
        log_message('debug', 'Has user_id: ' . ($session->has('user_id') ? 'YES' : 'NO'));
        log_message('debug', 'Role: ' . ($session->get('role') ?? 'NULL'));

        // Check if user is logged in
        if (!$session->has('user_id')) {
            log_message('debug', 'SUPERADMIN FILTER: No user_id, redirecting to login');
            return redirect()->to('/login')->with('error', 'Please log in to access this page.');
        }

        // Check if user is superadmin
        if ($session->get('role') !== 'superadmin') {
            log_message('debug', 'SUPERADMIN FILTER: Role is not superadmin, denying access');
            // Redirect to appropriate dashboard based on role
            $role = $session->get('role');

            if ($role === 'admin') {
                return redirect()->to('/admin/dashboard')->with('error', 'Access denied. Superadmin privileges required.');
            } elseif ($role === 'user') {
                return redirect()->to('/tools')->with('error', 'Access denied. Superadmin privileges required.');
            }

            // Fallback to login if role is not recognized
            return redirect()->to('/login')->with('error', 'Access denied.');
        }

        log_message('debug', 'SUPERADMIN FILTER: User is superadmin, allowing access');
    }

    /**
     * Allows After filters to inspect and modify the response.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param mixed|null $arguments
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after request
    }
}
