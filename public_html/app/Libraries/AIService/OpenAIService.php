<?php

namespace App\Libraries\AIService;

/**
 * OpenAIService
 *
 * Implementation of AI service using OpenAI's GPT API.
 * Provides same functionality as ClaudeService but using OpenAI.
 */
class OpenAIService implements AIServiceInterface
{
    /**
     * @var string API key for OpenAI
     */
    private string $apiKey;

    /**
     * @var string OpenAI model to use
     */
    private string $model = 'gpt-5.2';

    /**
     * @var string API endpoint
     */
    private string $endpoint = 'https://api.openai.com/v1/chat/completions';

    /**
     * Constructor
     *
     * @param string $apiKey OpenAI API key
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Generate property description in target language
     *
     * @param array $propertyData Property details
     * @param string $targetLanguage Target language code (en, de, es)
     * @return string Generated description
     * @throws \Exception If generation fails
     */
    public function generateDescription(array $propertyData, string $targetLanguage): string
    {
        // Generate in English first
        $prompt = $this->buildGenerationPrompt($propertyData);
        $englishDescription = $this->callOpenAIAPI($prompt, 2048);

        // If target language is not English, translate
        if ($targetLanguage !== 'en') {
            return $this->translateText($englishDescription, 'en', $targetLanguage);
        }

        return $englishDescription;
    }

    /**
     * Translate text from source language to target language
     *
     * @param string $text Text to translate
     * @param string $sourceLanguage Source language code
     * @param string $targetLanguage Target language code
     * @return string Translated text
     * @throws \Exception If translation fails
     */
    public function translateText(string $text, string $sourceLanguage, string $targetLanguage): string
    {
        $languageMap = [
            'en' => 'English',
            'de' => 'German',
            'es' => 'Spanish (European)',
        ];

        $sourceLang = $languageMap[$sourceLanguage] ?? $sourceLanguage;
        $targetLang = $languageMap[$targetLanguage] ?? $targetLanguage;

        $systemPrompt = "You are a professional property description translator.";

        $userPrompt = "Translate the following property description from {$sourceLang} to {$targetLang}.

Important requirements:
- For Spanish: Use European Spanish (Spain), not Latin American Spanish
- Maintain the professional tone and marketing appeal of the original text
- Keep property-specific terms accurate (square meters, room counts, features, etc.)
- Preserve any numerical values exactly as they appear
- Do not add or remove information, only translate

{$sourceLang} text to translate:
{$text}

Provide ONLY the translated text, without any explanations or additional commentary.";

        return $this->callOpenAIAPI($userPrompt, 1024, $systemPrompt);
    }

    /**
     * Rewrite text in the same language to avoid duplicate content
     *
     * @param string $text Text to rewrite
     * @param string $language Language code
     * @return string Rewritten text
     * @throws \Exception If rewriting fails
     */
    public function rewriteText(string $text, string $language): string
    {
        $languageMap = [
            'en' => 'English',
            'de' => 'German',
            'es' => 'Spanish',
        ];

        $lang = $languageMap[$language] ?? $language;

        $systemPrompt = "You are a professional property description copywriter.";

        $userPrompt = "Rewrite the following {$lang} property description to avoid duplicate content issues while maintaining all the original information.

Important requirements:
- Keep ALL information from the original text (square meters, room counts, features, location details, prices, etc.)
- Use different wording and sentence structures to avoid duplicate content detection
- Maintain the professional and marketing tone
- Keep it in {$lang}
- Do not add new information that wasn't in the original
- Preserve numerical values exactly as they appear
- Make it natural and engaging, not robotic

Original {$lang} text:
{$text}

Provide ONLY the rewritten {$lang} text, without any explanations or additional commentary.";

        return $this->callOpenAIAPI($userPrompt, 1024, $systemPrompt);
    }

    /**
     * Build prompt for property description generation (English)
     *
     * @param array $propertyData Property details
     * @return string Generated prompt
     */
    private function buildGenerationPrompt(array $propertyData): string
    {
        return "Generate an engaging, professional property description in English based on the following details:

Property Details:
- Property Type: {$propertyData['type']}
- Location: {$propertyData['town']}
- Region: {$propertyData['region']}
- Bedrooms: {$propertyData['bedrooms']}
- Bathrooms: {$propertyData['bathrooms']}
- Living Area: {$propertyData['living_area']} m²
- Plot Size: {$propertyData['plot_size']} m²
- Additional Features: {$propertyData['features']}

Requirements for the description:
1. Write 3-4 compelling paragraphs
2. Structure the description as follows:
   - Opening paragraph: Overview of the property and its unique features
   - Interior and amenities: Details about rooms, equipment, and special characteristics
   - Location and surroundings: Information about {$propertyData['town']} and the {$propertyData['region']} region (infrastructure, beaches, attractions, lifestyle)
   - Closing paragraph: Summary and call to action

3. Use professional yet inviting language
4. Emphasize the advantages of the {$propertyData['town']} location in the {$propertyData['region']} region
5. Integrate the additional features naturally into the text
6. Use concrete numbers (square meters, room counts)
7. Create a vivid image of the property and lifestyle

Write ONLY the property description without any additional explanations or comments.";
    }

    /**
     * Make API call to OpenAI
     *
     * @param string $userPrompt The user prompt to send
     * @param int $maxTokens Maximum tokens for response
     * @param string|null $systemPrompt Optional system prompt
     * @return string API response text
     * @throws \Exception If API call fails
     */
    private function callOpenAIAPI(string $userPrompt, int $maxTokens = 1024, ?string $systemPrompt = null): string
    {
        $messages = [];

        if ($systemPrompt) {
            $messages[] = [
                'role' => 'system',
                'content' => $systemPrompt,
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => $userPrompt,
        ];

        $data = [
            'model' => $this->model,
            'messages' => $messages,
            'max_tokens' => $maxTokens,
            'temperature' => 0.7,
        ];

        $ch = curl_init($this->endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            log_message('error', 'OpenAI API connection error: ' . $curlError);
            throw new \Exception('Connection error: ' . $curlError);
        }

        if ($httpCode !== 200) {
            $errorData = json_decode($response, true);
            $errorMsg = $errorData['error']['message'] ?? 'Unknown error';
            log_message('error', "OpenAI API error (HTTP {$httpCode}): {$errorMsg}");
            throw new \Exception("API error (HTTP {$httpCode}): " . $errorMsg);
        }

        $result = json_decode($response, true);

        if (!isset($result['choices'][0]['message']['content'])) {
            log_message('error', 'Unexpected OpenAI API response format');
            throw new \Exception('Unexpected API response format');
        }

        return $result['choices'][0]['message']['content'];
    }
}
