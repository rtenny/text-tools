<?php

namespace App\Controllers\Tools;

use App\Controllers\BaseController;
use App\Libraries\AIService\AIServiceFactory;
use App\Models\ProjectModel;

class TranslatorController extends BaseController
{
    /**
     * Display the three-tab tools interface
     */
    public function index()
    {
        $projectId = session()->get('project_id');
        $projectModel = new ProjectModel();
        $project = $projectModel->find($projectId);

        $languages = is_string($project['languages']) ? json_decode($project['languages'], true) : $project['languages'];

        $data = [
            'title' => 'Text Tools',
            'projectName' => $project['name'] ?? 'Project',
            'languages' => $languages,
        ];

        return view('tools/index', $data);
    }

    /**
     * AJAX: Translate text to target language
     */
    public function translate()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        $sourceText = $this->request->getPost('source_text');
        $targetLanguage = $this->request->getPost('target_language');

        if (empty($sourceText) || empty($targetLanguage)) {
            return $this->response->setJSON(['success' => false, 'error' => 'Source text and target language are required.']);
        }

        $projectId = session()->get('project_id');

        try {
            $aiService = AIServiceFactory::create($projectId);
            $translation = $aiService->translateText($sourceText, 'en', $targetLanguage);

            return $this->response->setJSON([
                'success' => true,
                'translation' => $translation,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Translation failed: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Translation failed: ' . $e->getMessage()]);
        }
    }
}
