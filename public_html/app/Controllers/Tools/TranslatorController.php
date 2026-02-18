<?php

namespace App\Controllers\Tools;

use App\Controllers\BaseController;
use App\Libraries\AIService\AIServiceFactory;
use App\Models\ActivityLogModel;
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

        // Get towns assigned to this project
        $townService = new \App\Services\TownService();
        $towns = $townService->getTownsForProject($projectId);

        // Usage counter for subscription projects
        $dailyLimit = $project['daily_translation_limit'];
        $isUnlimited = ($dailyLimit === null);
        $todayUsed = 0;
        if (!$isUnlimited) {
            $activityLogModel = new ActivityLogModel();
            $todayUsed = $activityLogModel->getTodayTranslationCount($projectId);
        }

        $data = [
            'title'       => 'Text Tools',
            'projectName' => $project['name'] ?? 'Project',
            'languages'   => $languages,
            'towns'       => $towns,
            'dailyLimit'  => $dailyLimit,
            'todayUsed'   => $todayUsed,
            'isUnlimited' => $isUnlimited,
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

        // Daily limit enforcement
        $projectModel = new ProjectModel();
        $project = $projectModel->find($projectId);
        $dailyLimit = $project['daily_translation_limit'];

        if ($dailyLimit !== null) {
            $activityLogModel = new ActivityLogModel();
            $usedToday = $activityLogModel->getTodayTranslationCount($projectId);

            if ($usedToday >= $dailyLimit) {
                return $this->response->setStatusCode(429)->setJSON([
                    'success'       => false,
                    'error'         => 'Daily translation limit reached (' . $usedToday . '/' . $dailyLimit . '). Translations will reset tomorrow.',
                    'limit_reached' => true,
                    'used'          => $usedToday,
                    'limit'         => $dailyLimit,
                ]);
            }
        }

        try {
            $aiService = AIServiceFactory::create($projectId);
            $translation = $aiService->translateText($sourceText, 'en', $targetLanguage);

            return $this->response->setJSON([
                'success'     => true,
                'translation' => $translation,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Translation failed: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Translation failed: ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX: Return today's translation usage for the current project.
     */
    public function usage()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false]);
        }

        $projectId = session()->get('project_id');
        $projectModel = new ProjectModel();
        $project = $projectModel->find($projectId);
        $dailyLimit = $project['daily_translation_limit'];

        $used = 0;
        if ($dailyLimit !== null) {
            $activityLogModel = new ActivityLogModel();
            $used = $activityLogModel->getTodayTranslationCount($projectId);
        }

        return $this->response->setJSON([
            'success'       => true,
            'used'          => $used,
            'limit'         => $dailyLimit,
            'is_unlimited'  => ($dailyLimit === null),
            'limit_reached' => ($dailyLimit !== null && $used >= $dailyLimit),
        ]);
    }
}
