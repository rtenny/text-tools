<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\ActivityLogModel;

class DashboardController extends BaseController
{
    protected $userModel;
    protected $activityLogModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->activityLogModel = new ActivityLogModel();
    }

    public function index()
    {
        log_message('debug', '=== ADMIN DASHBOARD INDEX CALLED ===');

        // Get project_id from session (set by TenantFilter)
        $projectId = session()->get('project_id');
        log_message('debug', 'Admin project_id: ' . ($projectId ?? 'NULL'));

        if (!$projectId) {
            log_message('error', 'Admin user has no project_id in session');
            return redirect()->to('/login')->with('error', 'Your account is not properly configured.');
        }

        // Get statistics for this project only
        $totalUsers = $this->userModel
            ->where('project_id', $projectId)
            ->where('role', 'user')
            ->countAllResults();

        // Get recent users
        $recentUsers = $this->userModel
            ->where('project_id', $projectId)
            ->where('role', 'user')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->find();

        // Get recent activity (if activity logs exist)
        $recentActivity = [];
        if ($this->activityLogModel->countAllResults() > 0) {
            $recentActivity = $this->activityLogModel
                ->select('activity_logs.*, users.email')
                ->join('users', 'users.id = activity_logs.user_id', 'left')
                ->where('activity_logs.project_id', $projectId)
                ->orderBy('activity_logs.created_at', 'DESC')
                ->limit(10)
                ->find();
        }

        $data = [
            'title' => 'Admin Dashboard',
            'totalUsers' => $totalUsers,
            'recentUsers' => $recentUsers,
            'recentActivity' => $recentActivity,
        ];

        return view('admin/dashboard', $data);
    }
}
