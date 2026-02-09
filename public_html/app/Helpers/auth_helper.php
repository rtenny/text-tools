<?php

/**
 * Authentication Helper Functions
 *
 * Provides convenient functions for checking user authentication
 * and authorization throughout the application.
 */

if (!function_exists('current_user')) {
    /**
     * Get current logged-in user data from session
     *
     * @return array|null User data array or null if not logged in
     */
    function current_user(): ?array
    {
        $session = session();

        if (!$session->has('user_id')) {
            return null;
        }

        return [
            'id'         => $session->get('user_id'),
            'email'      => $session->get('email'),
            'first_name' => $session->get('first_name'),
            'last_name'  => $session->get('last_name'),
            'role'       => $session->get('role'),
            'project_id' => $session->get('project_id'),
            'is_active'  => $session->get('is_active'),
        ];
    }
}

if (!function_exists('user_id')) {
    /**
     * Get current user's ID
     *
     * @return int|null User ID or null if not logged in
     */
    function user_id(): ?int
    {
        return session()->get('user_id');
    }
}

if (!function_exists('user_email')) {
    /**
     * Get current user's email
     *
     * @return string|null User email or null if not logged in
     */
    function user_email(): ?string
    {
        return session()->get('email');
    }
}

if (!function_exists('user_full_name')) {
    /**
     * Get current user's full name
     *
     * @return string|null Full name or null if not logged in
     */
    function user_full_name(): ?string
    {
        $session = session();

        if (!$session->has('first_name')) {
            return null;
        }

        return trim($session->get('first_name') . ' ' . $session->get('last_name'));
    }
}

if (!function_exists('user_role')) {
    /**
     * Get current user's role
     *
     * @return string|null Role (superadmin/admin/user) or null if not logged in
     */
    function user_role(): ?string
    {
        return session()->get('role');
    }
}

if (!function_exists('user_project_id')) {
    /**
     * Get current user's project ID
     *
     * @return int|null Project ID or null if not logged in or superadmin
     */
    function user_project_id(): ?int
    {
        return session()->get('project_id');
    }
}

if (!function_exists('is_logged_in')) {
    /**
     * Check if user is logged in
     *
     * @return bool True if logged in
     */
    function is_logged_in(): bool
    {
        return session()->has('user_id');
    }
}

if (!function_exists('is_superadmin')) {
    /**
     * Check if current user is a superadmin
     *
     * @return bool True if superadmin
     */
    function is_superadmin(): bool
    {
        return session()->get('role') === 'superadmin';
    }
}

if (!function_exists('is_admin')) {
    /**
     * Check if current user is an admin (not including superadmin)
     *
     * @return bool True if admin
     */
    function is_admin(): bool
    {
        return session()->get('role') === 'admin';
    }
}

if (!function_exists('is_admin_or_superadmin')) {
    /**
     * Check if current user is an admin or superadmin
     *
     * @return bool True if admin or superadmin
     */
    function is_admin_or_superadmin(): bool
    {
        $role = session()->get('role');
        return in_array($role, ['admin', 'superadmin'], true);
    }
}

if (!function_exists('is_user')) {
    /**
     * Check if current user is a regular user
     *
     * @return bool True if user
     */
    function is_user(): bool
    {
        return session()->get('role') === 'user';
    }
}

if (!function_exists('has_role')) {
    /**
     * Check if current user has specific role
     *
     * @param string $role Role to check (superadmin/admin/user)
     * @return bool True if user has role
     */
    function has_role(string $role): bool
    {
        return session()->get('role') === $role;
    }
}

if (!function_exists('belongs_to_project')) {
    /**
     * Check if current user belongs to a specific project
     *
     * @param int $projectId Project ID to check
     * @return bool True if user belongs to project
     */
    function belongs_to_project(int $projectId): bool
    {
        $userProjectId = session()->get('project_id');

        // Superadmins don't belong to a project but can access all
        if (is_superadmin()) {
            return true;
        }

        return $userProjectId === $projectId;
    }
}

if (!function_exists('can_access_project')) {
    /**
     * Check if current user can access a specific project
     * Superadmins can access all projects
     *
     * @param int $projectId Project ID to check
     * @return bool True if user can access project
     */
    function can_access_project(int $projectId): bool
    {
        // Superadmins can access all projects
        if (is_superadmin()) {
            return true;
        }

        // Admins and users can only access their own project
        return session()->get('project_id') === $projectId;
    }
}

if (!function_exists('redirect_by_role')) {
    /**
     * Redirect user to appropriate dashboard based on role
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    function redirect_by_role()
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
                return redirect()->to('/login');
        }
    }
}

if (!function_exists('login_user')) {
    /**
     * Log in a user by storing their data in session
     *
     * @param array $user User data from database
     * @return void
     */
    function login_user(array $user): void
    {
        $session = session();

        $session->set([
            'user_id'    => $user['id'],
            'email'      => $user['email'],
            'first_name' => $user['first_name'],
            'last_name'  => $user['last_name'],
            'role'       => $user['role'],
            'project_id' => $user['project_id'] ?? null,
            'is_active'  => $user['is_active'],
            'logged_in'  => true,
        ]);

        // Regenerate session ID for security
        $session->regenerate();
    }
}

if (!function_exists('logout_user')) {
    /**
     * Log out current user by destroying session
     *
     * @return void
     */
    function logout_user(): void
    {
        session()->destroy();
    }
}
