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
        $isAjax = $request->isAJAX();

        // Check if user is logged in
        if (!$session->has('user_id')) {
            if ($isAjax) {
                return service('response')
                    ->setStatusCode(401)
                    ->setJSON([
                        'success' => false,
                        'error' => 'Please log in to access this page.',
                        'session_expired' => true
                    ]);
            }

            return redirect()->to('/login')->with('error', 'Please log in to access this page.');
        }

        // Check if user is superadmin
        if ($session->get('role') !== 'superadmin') {
            if ($isAjax) {
                return service('response')
                    ->setStatusCode(403)
                    ->setJSON([
                        'success' => false,
                        'error' => 'Access denied. Superadmin privileges required.'
                    ]);
            }

            $role = $session->get('role');

            if ($role === 'admin') {
                return redirect()->to('/admin/dashboard')->with('error', 'Access denied. Superadmin privileges required.');
            } elseif ($role === 'user') {
                return redirect()->to('/tools')->with('error', 'Access denied. Superadmin privileges required.');
            }

            return redirect()->to('/login')->with('error', 'Access denied.');
        }
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
