<?php

namespace App\Libraries\AIService;

/**
 * AIServiceInterface
 *
 * Common interface for AI service providers (Claude, OpenAI, etc.)
 * Defines methods for property description generation, translation, and rewriting.
 */
interface AIServiceInterface
{
    /**
     * Generate property description in target language
     *
     * @param array $propertyData Property details (type, location, beds, baths, etc.)
     * @param string $targetLanguage Target language code (en, de, es)
     * @return string Generated description
     * @throws \Exception If generation fails
     */
    public function generateDescription(array $propertyData, string $targetLanguage): string;

    /**
     * Translate text from source language to target language
     *
     * @param string $text Text to translate
     * @param string $sourceLanguage Source language code (en, de, es)
     * @param string $targetLanguage Target language code (en, de, es)
     * @return string Translated text
     * @throws \Exception If translation fails
     */
    public function translateText(string $text, string $sourceLanguage, string $targetLanguage): string;

    /**
     * Rewrite text in the same language to avoid duplicate content
     *
     * @param string $text Text to rewrite
     * @param string $language Language code (en, de, es)
     * @return string Rewritten text
     * @throws \Exception If rewriting fails
     */
    public function rewriteText(string $text, string $language): string;

    /**
     * Generate image alt text descriptions using vision API
     *
     * @param string $imageBase64 Base64 encoded image data
     * @param string $mimeType Image MIME type (image/jpeg, image/png, image/webp)
     * @param string $propertyType Property type (Villa, Apartment, etc.)
     * @param string $location Location/town name
     * @param string $city City name
     * @return array Array of 3 alt text options
     * @throws \Exception If generation fails
     */
    public function generateImageAltText(string $imageBase64, string $mimeType, string $propertyType, string $location, string $city): array;
}
