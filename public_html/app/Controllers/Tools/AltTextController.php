<?php

namespace App\Controllers\Tools;

use App\Controllers\BaseController;
use App\Libraries\AIService\AIServiceFactory;

class AltTextController extends BaseController
{
    /**
     * Maximum file size in bytes (5MB)
     */
    private const MAX_FILE_SIZE = 5242880;

    /**
     * Allowed image MIME types
     */
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    /**
     * Allowed image extensions
     */
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * AJAX: Generate alt text from image
     */
    public function generateAltText()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        // Get form data
        $propertyType = $this->request->getPost('property_type');
        $location = $this->request->getPost('location');
        $city = $this->request->getPost('city');
        $imageSource = $this->request->getPost('image_source'); // 'upload' or 'url'

        // Validate required fields
        if (empty($propertyType) || empty($location) || empty($city)) {
            return $this->response->setJSON(['success' => false, 'error' => 'Property type, location, and city are required.']);
        }

        try {
            // Get image data based on source
            if ($imageSource === 'upload') {
                $imageData = $this->handleImageUpload();
            } elseif ($imageSource === 'url') {
                $imageUrl = $this->request->getPost('image_url');
                if (empty($imageUrl)) {
                    return $this->response->setJSON(['success' => false, 'error' => 'Image URL is required.']);
                }
                $imageData = $this->handleImageUrl($imageUrl);
            } else {
                return $this->response->setJSON(['success' => false, 'error' => 'Please upload an image or provide an image URL.']);
            }

            if (!$imageData['success']) {
                return $this->response->setJSON(['success' => false, 'error' => $imageData['error']]);
            }

            // Generate alt text using AI service
            $projectId = session()->get('project_id');
            $aiService = AIServiceFactory::create($projectId);

            $altTextOptions = $aiService->generateImageAltText(
                $imageData['base64'],
                $imageData['mime_type'],
                $propertyType,
                $location,
                $city
            );

            return $this->response->setJSON([
                'success' => true,
                'alt_text_options' => $altTextOptions,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Alt text generation failed: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Failed to generate alt text. Please try again.']);
        }
    }

    /**
     * Handle image file upload
     *
     * @return array ['success' => bool, 'base64' => string, 'mime_type' => string, 'error' => string]
     */
    private function handleImageUpload(): array
    {
        $file = $this->request->getFile('image_file');

        if (!$file || !$file->isValid()) {
            return ['success' => false, 'error' => 'Please upload a valid image file.'];
        }

        // Validate file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            return ['success' => false, 'error' => 'Image file is too large. Maximum size is 5MB.'];
        }

        // Validate MIME type
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
            return ['success' => false, 'error' => 'Invalid image format. Please upload JPG, PNG, or WebP.'];
        }

        // Validate extension
        $extension = strtolower($file->getClientExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            return ['success' => false, 'error' => 'Invalid image format. Please upload JPG, PNG, or WebP.'];
        }

        // Read file and convert to base64
        $imageData = file_get_contents($file->getTempName());
        $base64 = base64_encode($imageData);

        return [
            'success' => true,
            'base64' => $base64,
            'mime_type' => $mimeType,
        ];
    }

    /**
     * Handle image URL
     *
     * @param string $url Image URL
     * @return array ['success' => bool, 'base64' => string, 'mime_type' => string, 'error' => string]
     */
    private function handleImageUrl(string $url): array
    {
        // Validate URL format
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ['success' => false, 'error' => 'Invalid image URL. Please provide a valid URL.'];
        }

        // Validate protocol (only http and https)
        $parsedUrl = parse_url($url);
        if (!isset($parsedUrl['scheme']) || !in_array(strtolower($parsedUrl['scheme']), ['http', 'https'])) {
            return ['success' => false, 'error' => 'Invalid URL protocol. Only HTTP and HTTPS are allowed.'];
        }

        // SSRF protection: Block private/local IP addresses
        $host = $parsedUrl['host'] ?? '';
        if ($this->isPrivateOrLocalIp($host)) {
            return ['success' => false, 'error' => 'Access to private or local IP addresses is not allowed.'];
        }

        // Fetch image from URL
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_MAXFILESIZE, self::MAX_FILE_SIZE);
            curl_setopt($ch, CURLOPT_USERAGENT, 'TextTools/1.0');

            $imageData = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                log_message('error', 'Failed to fetch image from URL: ' . $curlError);
                return ['success' => false, 'error' => 'Failed to load image from URL. Please check the URL and try again.'];
            }

            if ($httpCode !== 200) {
                return ['success' => false, 'error' => 'Failed to load image from URL. HTTP status: ' . $httpCode];
            }

            // Validate content type
            $mimeType = strtolower(explode(';', $contentType)[0]);
            if (!in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
                return ['success' => false, 'error' => 'Invalid image format from URL. Only JPG, PNG, and WebP are supported.'];
            }

            // Convert to base64
            $base64 = base64_encode($imageData);

            return [
                'success' => true,
                'base64' => $base64,
                'mime_type' => $mimeType,
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error fetching image from URL: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to load image from URL. Please try again.'];
        }
    }

    /**
     * Check if host is a private or local IP address
     *
     * @param string $host Hostname or IP address
     * @return bool True if private/local, false otherwise
     */
    private function isPrivateOrLocalIp(string $host): bool
    {
        // Resolve hostname to IP
        $ip = gethostbyname($host);

        // If resolution failed, block it
        if ($ip === $host && !filter_var($host, FILTER_VALIDATE_IP)) {
            return true;
        }

        // Check if IP is private or reserved
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return true;
        }

        return false;
    }
}
