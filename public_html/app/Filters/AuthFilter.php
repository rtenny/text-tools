<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AuthFilter
 *
 * Ensures user is logged in before accessing protected routes.
 */
class AuthFilter implements FilterInterface
{
    /**
     * Check if user is authenticated before request
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
                        'error' => 'Your session has expired. Please log in again.',
                        'session_expired' => true
                    ]);
            }

            $session->set('intended_url', current_url());
            return redirect()->to('/login')->with('error', 'Please log in to access this page.');
        }

        // Check if user account is still active
        if (!$session->get('is_active')) {
            if ($isAjax) {
                $session->destroy();
                return service('response')
                    ->setStatusCode(401)
                    ->setJSON([
                        'success' => false,
                        'error' => 'Your account has been deactivated. Please contact an administrator.',
                        'session_expired' => true
                    ]);
            }

            $session->destroy();
            return redirect()->to('/login')->with('error', 'Your account has been deactivated. Please contact an administrator.');
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
