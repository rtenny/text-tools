<?php

namespace App\Controllers\Tools;

use App\Controllers\BaseController;
use App\Libraries\AIService\AIServiceFactory;

class GeneratorController extends BaseController
{
    /**
     * AJAX: Generate property description
     */
    public function generate()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        $propertyData = [
            'property_type' => $this->request->getPost('property_type'),
            'location'      => $this->request->getPost('location'),
            'bedrooms'      => $this->request->getPost('bedrooms'),
            'bathrooms'     => $this->request->getPost('bathrooms'),
            'living_area'   => $this->request->getPost('living_area'),
            'plot_size'     => $this->request->getPost('plot_size'),
            'features'      => $this->request->getPost('features'),
        ];

        // Validate required fields
        if (empty($propertyData['property_type']) || empty($propertyData['location']) ||
            empty($propertyData['bedrooms']) || empty($propertyData['bathrooms']) ||
            empty($propertyData['living_area'])) {
            return $this->response->setJSON(['success' => false, 'error' => 'Please fill in all required fields.']);
        }

        $projectId = session()->get('project_id');

        // Validate that the selected town is assigned to the user's project
        $townService = new \App\Services\TownService();
        if (!$townService->validateTownForProject($projectId, $propertyData['location'])) {
            return $this->response->setJSON(['success' => false, 'error' => 'The selected location is not available for your project.']);
        }

        try {
            $aiService = AIServiceFactory::create($projectId);

            // Generate in English
            $description = $aiService->generateDescription($propertyData, 'en');

            return $this->response->setJSON([
                'success' => true,
                'description' => $description,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Generation failed: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Generation failed: ' . $e->getMessage()]);
        }
    }
}
