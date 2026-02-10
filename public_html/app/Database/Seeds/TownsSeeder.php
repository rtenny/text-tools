<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TownsSeeder extends Seeder
{
    public function run()
    {
        $townModel = new \App\Models\TownModel();

        // List of towns to seed
        $towns = [
            'Alicante',
            'Benidorm',
            'Calpe',
            'Denia',
            'Torrevieja',
        ];

        $insertedCount = 0;
        $skippedCount = 0;

        foreach ($towns as $townName) {
            // Check if town already exists
            $existingTown = $townModel->where('name', $townName)->first();

            if ($existingTown) {
                echo "Town '{$townName}' already exists. Skipping.\n";
                $skippedCount++;
                continue;
            }

            // Insert town
            $townModel->insert(['name' => $townName]);
            echo "Town '{$townName}' created successfully.\n";
            $insertedCount++;
        }

        echo "\n========================================\n";
        echo "Towns Seeder Summary:\n";
        echo "- Inserted: {$insertedCount}\n";
        echo "- Skipped: {$skippedCount}\n";
        echo "- Total: " . count($towns) . "\n";
        echo "========================================\n";
    }
}
