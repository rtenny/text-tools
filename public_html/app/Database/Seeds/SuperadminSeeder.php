<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SuperadminSeeder extends Seeder
{
    public function run()
    {
        // Check if superadmin already exists
        $userModel = new \App\Models\UserModel();
        $existingSuperadmin = $userModel->where('email', 'admin@texttools.local')->first();

        if ($existingSuperadmin) {
            echo "Superadmin already exists. Skipping seeder.\n";
            return;
        }

        // Create superadmin user
        $data = [
            'project_id'    => null, // Superadmins don't belong to a project
            'email'         => 'admin@texttools.local',
            'password'      => 'admin123', // Will be hashed by UserModel beforeInsert callback
            'first_name'    => 'Super',
            'last_name'     => 'Admin',
            'role'          => 'superadmin',
            'is_active'     => 1,
            'last_login_at' => null,
        ];

        $userModel->insert($data);

        echo "Superadmin created successfully!\n";
        echo "Email: admin@texttools.local\n";
        echo "Password: admin123\n";
        echo "Please change this password after first login.\n";
    }
}
