<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AdminFilter
 *
 * Ensures user has admin or superadmin role before accessing admin routes.
 */
class AdminFilter implements FilterInterface
{
    /**
     * Check if user is admin or superadmin before request
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

        $role = $session->get('role');

        // Check if user is admin or superadmin
        if (!in_array($role, ['admin', 'superadmin'], true)) {
            if ($isAjax) {
                return service('response')
                    ->setStatusCode(403)
                    ->setJSON([
                        'success' => false,
                        'error' => 'Access denied. Admin privileges required.'
                    ]);
            }

            // Regular users cannot access admin routes
            return redirect()->to('/tools')->with('error', 'Access denied. Admin privileges required.');
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
