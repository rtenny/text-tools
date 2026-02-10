<?php

namespace App\Validation;

use App\Services\TownService;

class CustomRules
{
    /**
     * Validate that a town belongs to the user's project
     *
     * @param string $value The town name
     * @param string $params Comma-separated parameters (project_id)
     * @param array $data All form data
     * @return bool
     */
    public function valid_town_for_project(string $value, string $params, array $data): bool
    {
        if (empty($value)) {
            return false;
        }

        // Get project ID from session if not provided in params
        $projectId = !empty($params) ? (int)$params : session()->get('project_id');

        if (empty($projectId)) {
            return false;
        }

        $townService = new TownService();
        return $townService->validateTownForProject($projectId, $value);
    }

    /**
     * Error message for valid_town_for_project rule
     *
     * @return string
     */
    public function valid_town_for_project_errors(): string
    {
        return 'The selected town is not available for your project.';
    }
}
