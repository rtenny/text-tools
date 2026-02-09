<?php

namespace App\Libraries\AIService;

use App\Models\ProjectModel;
use App\Libraries\EncryptionService;

/**
 * AIServiceFactory
 *
 * Factory class to create the appropriate AI service instance
 * based on a project's configuration (Claude or OpenAI).
 */
class AIServiceFactory
{
    /**
     * Create AI service instance for a project
     *
     * @param int $projectId Project ID
     * @return AIServiceInterface AI service instance (Claude or OpenAI)
     * @throws \RuntimeException If project not found or invalid provider
     */
    public static function create(int $projectId): AIServiceInterface
    {
        // Load project from database
        $projectModel = new ProjectModel();
        $project = $projectModel->find($projectId);

        if (!$project) {
            log_message('error', "AIServiceFactory: Project {$projectId} not found");
            throw new \RuntimeException('Project not found');
        }

        // Check if project is active
        if (!$project['is_active']) {
            log_message('error', "AIServiceFactory: Project {$projectId} is not active");
            throw new \RuntimeException('Project is not active');
        }

        // Decrypt API key
        try {
            $encryptionService = new EncryptionService();
            $apiKey = $encryptionService->decrypt($project['api_key']);
        } catch (\Exception $e) {
            log_message('error', "AIServiceFactory: Failed to decrypt API key for project {$projectId}: " . $e->getMessage());
            throw new \RuntimeException('Failed to decrypt API key');
        }

        // Create appropriate service based on provider
        switch ($project['default_ai_provider']) {
            case 'claude':
                log_message('debug', "AIServiceFactory: Creating ClaudeService for project {$projectId}");
                return new ClaudeService($apiKey);

            case 'openai':
                log_message('debug', "AIServiceFactory: Creating OpenAIService for project {$projectId}");
                return new OpenAIService($apiKey);

            default:
                log_message('error', "AIServiceFactory: Invalid AI provider '{$project['default_ai_provider']}' for project {$projectId}");
                throw new \RuntimeException('Invalid AI provider: ' . $project['default_ai_provider']);
        }
    }

    /**
     * Create AI service instance directly with provider and API key
     * (useful for testing or when project context is not available)
     *
     * @param string $provider Provider name (claude or openai)
     * @param string $apiKey API key
     * @return AIServiceInterface AI service instance
     * @throws \RuntimeException If invalid provider
     */
    public static function createWithCredentials(string $provider, string $apiKey): AIServiceInterface
    {
        switch (strtolower($provider)) {
            case 'claude':
                return new ClaudeService($apiKey);

            case 'openai':
                return new OpenAIService($apiKey);

            default:
                throw new \RuntimeException('Invalid AI provider: ' . $provider);
        }
    }

    /**
     * Get list of supported AI providers
     *
     * @return array Array of provider names
     */
    public static function getSupportedProviders(): array
    {
        return ['claude', 'openai'];
    }

    /**
     * Check if a provider is supported
     *
     * @param string $provider Provider name
     * @return bool True if supported
     */
    public static function isProviderSupported(string $provider): bool
    {
        return in_array(strtolower($provider), self::getSupportedProviders(), true);
    }
}
