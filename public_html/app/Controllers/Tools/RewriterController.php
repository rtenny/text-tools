<?php

namespace App\Controllers\Tools;

use App\Controllers\BaseController;
use App\Libraries\AIService\AIServiceFactory;

class RewriterController extends BaseController
{
    /**
     * AJAX: Rewrite text and optionally translate
     */
    public function rewrite()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        $originalText = $this->request->getPost('original_text');

        if (empty($originalText)) {
            return $this->response->setJSON(['success' => false, 'error' => 'Original text is required.']);
        }

        $projectId = session()->get('project_id');

        try {
            $aiService = AIServiceFactory::create($projectId);

            // Rewrite in English
            $rewritten = $aiService->rewriteText($originalText, 'en');

            return $this->response->setJSON([
                'success' => true,
                'rewritten' => $rewritten,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Rewrite failed: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Rewrite failed: ' . $e->getMessage()]);
        }
    }
}
