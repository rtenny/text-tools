<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use App\Models\ProjectModel;
use App\Models\UserModel;
use App\Models\ActivityLogModel;

class DashboardController extends BaseController
{
    protected $projectModel;
    protected $userModel;
    protected $activityLogModel;

    public function __construct()
    {
        $this->projectModel = new ProjectModel();
        $this->userModel = new UserModel();
        $this->activityLogModel = new ActivityLogModel();
    }

    public function index()
    {
        log_message('debug', '=== SUPERADMIN DASHBOARD INDEX CALLED ===');
        log_message('debug', 'Session data: ' . json_encode(session()->get()));

        // Get statistics
        $totalProjects = $this->projectModel->countAllResults();
        $activeProjects = $this->projectModel->where('is_active', 1)->countAllResults();

        $totalAdmins = $this->userModel->where('role', 'admin')->countAllResults();
        $totalUsers = $this->userModel->where('role', 'user')->countAllResults();

        // Get recent projects
        $recentProjects = $this->projectModel
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->find();

        // Get recent admins
        $recentAdmins = $this->userModel
            ->where('role', 'admin')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->find();

        // Get recent activity (if activity logs exist)
        $recentActivity = [];
        if ($this->activityLogModel->countAllResults() > 0) {
            $recentActivity = $this->activityLogModel
                ->select('activity_logs.*, users.email, projects.name as project_name')
                ->join('users', 'users.id = activity_logs.user_id', 'left')
                ->join('projects', 'projects.id = activity_logs.project_id', 'left')
                ->orderBy('activity_logs.created_at', 'DESC')
                ->limit(10)
                ->find();
        }

        $data = [
            'title' => 'Dashboard',
            'totalProjects' => $totalProjects,
            'activeProjects' => $activeProjects,
            'totalAdmins' => $totalAdmins,
            'totalUsers' => $totalUsers,
            'recentProjects' => $recentProjects,
            'recentAdmins' => $recentAdmins,
            'recentActivity' => $recentActivity,
        ];

        return view('superadmin/dashboard', $data);
    }
}
