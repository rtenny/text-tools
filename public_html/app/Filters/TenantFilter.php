<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * TenantFilter
 *
 * Injects project (tenant) context into request for admins and users.
 * Superadmins are not associated with a specific project.
 */
class TenantFilter implements FilterInterface
{
    /**
     * Inject project context before request
     *
     * @param RequestInterface $request
     * @param mixed|null $arguments
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $isAjax = $request->isAJAX();

        // Only inject project context for admins and users (not superadmins)
        $role = $session->get('role');

        if (in_array($role, ['admin', 'user'], true)) {
            $projectId = $session->get('project_id');

            // Ensure project_id is set
            if (empty($projectId)) {
                log_message('error', 'User ' . $session->get('user_id') . ' has no project_id assigned');
                $session->destroy();

                if ($isAjax) {
                    return service('response')
                        ->setStatusCode(403)
                        ->setJSON([
                            'success' => false,
                            'error' => 'Your account is not properly configured. Please contact an administrator.',
                            'session_expired' => true
                        ]);
                }

                return redirect()->to('/login')->with('error', 'Your account is not properly configured. Please contact an administrator.');
            }

            // Store project_id in request for easy access in controllers
            // Note: This is stored as a custom property on the request object
            $request->projectId = $projectId;
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
