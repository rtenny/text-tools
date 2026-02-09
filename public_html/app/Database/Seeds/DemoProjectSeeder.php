<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DemoProjectSeeder extends Seeder
{
    public function run()
    {
        // Check if demo project already exists
        $projectModel = new \App\Models\ProjectModel();
        $existingProject = $projectModel->where('slug', 'demo-project')->first();

        if ($existingProject) {
            echo "Demo project already exists. Skipping seeder.\n";
            return;
        }

        // Create encryption service for API key
        $encryptionService = new \App\Libraries\EncryptionService();

        // Create demo project
        $projectData = [
            'name'                => 'Demo Project',
            'slug'                => 'demo-project',
            'languages'           => ['en', 'de', 'es'],
            'default_ai_provider' => 'claude',
            'api_key'             => $encryptionService->encrypt('your-actual-api-key-here'), // Replace with your actual API key
            'is_active'           => 1,
        ];

        $projectModel->insert($projectData);
        $projectId = $projectModel->getInsertID();

        echo "Demo project created successfully!\n";
        echo "Project ID: {$projectId}\n";

        // Create demo admin for this project
        $userModel = new \App\Models\UserModel();
        $adminData = [
            'project_id'    => $projectId,
            'email'         => 'admin@demo.local',
            'password'      => 'admin123', // Will be hashed by UserModel beforeInsert callback
            'first_name'    => 'Demo',
            'last_name'     => 'Admin',
            'role'          => 'admin',
            'is_active'     => 1,
            'last_login_at' => null,
        ];

        $userModel->insert($adminData);
        $adminId = $userModel->getInsertID();

        echo "Demo admin created successfully!\n";
        echo "Admin ID: {$adminId}\n";
        echo "Email: admin@demo.local\n";
        echo "Password: admin123\n";

        // Create demo user for this project
        $userData = [
            'project_id'    => $projectId,
            'email'         => 'user@demo.local',
            'password'      => 'user123', // Will be hashed by UserModel beforeInsert callback
            'first_name'    => 'Demo',
            'last_name'     => 'User',
            'role'          => 'user',
            'is_active'     => 1,
            'last_login_at' => null,
        ];

        $userModel->insert($userData);
        $userId = $userModel->getInsertID();

        echo "Demo user created successfully!\n";
        echo "User ID: {$userId}\n";
        echo "Email: user@demo.local\n";
        echo "Password: user123\n";

        echo "\n=== Demo Project Setup Complete ===\n";
        echo "Remember to replace 'your-demo-api-key-here' with your actual API key!\n";
    }
}
