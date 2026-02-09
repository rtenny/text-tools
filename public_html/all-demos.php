<?php
/**
 * Combined Property Tools Demo
 * Combines Property Description Generation, Translation, and Rewriting in one interface
 * Uses Anthropic Claude API
 */

// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
        }
    }
    return true;
}

// Load .env file
loadEnv(__DIR__ . '/.env');

// Function to generate property description
function generateDescription($propertyData, $apiKey) {
    $prompt = "Du bist ein professioneller Immobilienmakler auf Mallorca und schreibst ansprechende, verkaufsfördernde Immobilienbeschreibungen auf Deutsch.

Erstelle eine professionelle Immobilienbeschreibung basierend auf folgenden Angaben:

Immobiliendetails:
- Immobilientyp: {$propertyData['type']}
- Ort: {$propertyData['town']}
- Region: {$propertyData['region']} Mallorca
- Schlafzimmer: {$propertyData['bedrooms']}
- Badezimmer: {$propertyData['bathrooms']}
- Wohnfläche: {$propertyData['living_area']} m²
- Grundstücksgröße: {$propertyData['plot_size']} m²
- Zusätzliche Ausstattung: {$propertyData['features']}

Anforderungen an die Beschreibung:
1. Schreibe 3-4 aussagekräftige Absätze
2. Gliedere die Beschreibung wie folgt:
   - Einleitender Absatz: Überblick über die Immobilie und ihre Besonderheiten
   - Ausstattung und Räumlichkeiten: Details zu Zimmern, Ausstattung und besonderen Merkmalen
   - Lage und Umgebung: Informationen über {$propertyData['town']} und die {$propertyData['region']}e Region Mallorcas (Infrastruktur, Strände, Sehenswürdigkeiten, Lebensstil)
   - Abschließender Absatz: Zusammenfassung und Kaufanreiz

3. Verwende eine professionelle, aber einladende Sprache
4. Betone die Vorzüge der Lage {$propertyData['town']} in der {$propertyData['region']}en Region
5. Integriere die zusätzlichen Ausstattungsmerkmale natürlich in den Text
6. Verwende konkrete Zahlen (Quadratmeter, Anzahl Zimmer)
7. Erzeuge ein lebendiges Bild der Immobilie und des Lebensgefühls

Schreibe NUR die Immobilienbeschreibung ohne zusätzliche Erklärungen oder Kommentare.";

    $data = [
        'model' => 'claude-sonnet-4-5-20250929',
        'max_tokens' => 2048,
        'messages' => [
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ]
    ];

    $ch = curl_init('https://api.anthropic.com/v1/messages');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'x-api-key: ' . $apiKey,
        'anthropic-version: 2023-06-01'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        throw new Exception("Connection error: " . $curlError);
    }

    if ($httpCode !== 200) {
        $errorData = json_decode($response, true);
        $errorMsg = isset($errorData['error']['message']) ? $errorData['error']['message'] : 'Unknown error';
        throw new Exception("API error (HTTP {$httpCode}): " . $errorMsg);
    }

    $result = json_decode($response, true);

    if (isset($result['content'][0]['text'])) {
        return $result['content'][0]['text'];
    }

    throw new Exception("Unexpected API response format");
}

// Function to rewrite German text
function rewriteGermanText($text, $apiKey) {
    $prompt = "You are a professional property description copywriter. Rewrite the following German property description to avoid duplicate content issues while maintaining all the original information.

Important requirements:
- Keep ALL information from the original text (square meters, room counts, features, location details, prices, etc.)
- Use different wording and sentence structures to avoid duplicate content detection
- Maintain the professional and marketing tone
- Keep it in German
- Do not add new information that wasn't in the original
- Preserve numerical values exactly as they appear
- Make it natural and engaging, not robotic

Original German text:
{$text}

Provide ONLY the rewritten German text, without any explanations or additional commentary.";

    $data = [
        'model' => 'claude-sonnet-4-5-20250929',
        'max_tokens' => 1024,
        'messages' => [
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ]
    ];

    $ch = curl_init('https://api.anthropic.com/v1/messages');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'x-api-key: ' . $apiKey,
        'anthropic-version: 2023-06-01'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        throw new Exception("Connection error: " . $curlError);
    }

    if ($httpCode !== 200) {
        $errorData = json_decode($response, true);
        $errorMsg = isset($errorData['error']['message']) ? $errorData['error']['message'] : 'Unknown error';
        throw new Exception("API error (HTTP {$httpCode}): " . $errorMsg);
    }

    $result = json_decode($response, true);

    if (isset($result['content'][0]['text'])) {
        return $result['content'][0]['text'];
    }

    throw new Exception("Unexpected API response format");
}

// Function to translate text
function translateText($text, $targetLanguage, $apiKey) {
    $languageInstructions = [
        'English' => 'English',
        'Russian' => 'Russian (Русский)',
        'Czech' => 'Czech (Čeština)',
        'Spanish' => 'European Spanish (Spain) - NOT Latin American Spanish'
    ];

    $prompt = "You are a professional property description translator. Translate the following German property description into {$languageInstructions[$targetLanguage]}.

Important requirements:
- For Spanish: Use European Spanish (Spain), not Latin American Spanish
- Maintain the professional tone and marketing appeal of the original text
- Keep property-specific terms accurate (square meters, room counts, features, etc.)
- Preserve any numerical values exactly as they appear
- Do not add or remove information, only translate

German text to translate:
{$text}

Provide ONLY the translated text, without any explanations or additional commentary.";

    $data = [
        'model' => 'claude-sonnet-4-5-20250929',
        'max_tokens' => 1024,
        'messages' => [
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ]
    ];

    $ch = curl_init('https://api.anthropic.com/v1/messages');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'x-api-key: ' . $apiKey,
        'anthropic-version: 2023-06-01'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        throw new Exception("Connection error: " . $curlError);
    }

    if ($httpCode !== 200) {
        $errorData = json_decode($response, true);
        $errorMsg = isset($errorData['error']['message']) ? $errorData['error']['message'] : 'Unknown error';
        throw new Exception("API error (HTTP {$httpCode}): " . $errorMsg);
    }

    $result = json_decode($response, true);

    if (isset($result['content'][0]['text'])) {
        return $result['content'][0]['text'];
    }

    throw new Exception("Unexpected API response format");
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] === '1') {
    header('Content-Type: application/json');

    $action = $_POST['action'] ?? '';
    
    if ($action === 'generate') {
        $propertyData = [
            'type' => $_POST['property_type'] ?? '',
            'town' => $_POST['town'] ?? '',
            'region' => $_POST['region'] ?? '',
            'bedrooms' => $_POST['bedrooms'] ?? '',
            'bathrooms' => $_POST['bathrooms'] ?? '',
            'living_area' => $_POST['living_area'] ?? '',
            'plot_size' => $_POST['plot_size'] ?? '',
            'features' => trim($_POST['features'] ?? '')
        ];

        // Validation
        if (empty($propertyData['type']) || empty($propertyData['town']) || empty($propertyData['region'])) {
            echo json_encode(['success' => false, 'error' => 'Please fill in all required fields.']);
            exit;
        }

        if (!isset($_ENV['ANTHROPIC_API_KEY']) || $_ENV['ANTHROPIC_API_KEY'] === 'your_api_key_here') {
            echo json_encode(['success' => false, 'error' => 'API key not configured. Please add your Anthropic API key to the .env file.']);
            exit;
        }

        try {
            $description = generateDescription($propertyData, $_ENV['ANTHROPIC_API_KEY']);
            echo json_encode(['success' => true, 'description' => $description]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    } elseif ($action === 'translate') {
        $germanText = trim($_POST['german_text'] ?? '');
        $language = $_POST['language'] ?? '';

        if (empty($germanText)) {
            echo json_encode(['success' => false, 'error' => 'Please enter a German property description to translate.']);
            exit;
        }

        if (!isset($_ENV['ANTHROPIC_API_KEY']) || $_ENV['ANTHROPIC_API_KEY'] === 'your_api_key_here') {
            echo json_encode(['success' => false, 'error' => 'API key not configured. Please add your Anthropic API key to the .env file.']);
            exit;
        }

        try {
            $translation = translateText($germanText, $language, $_ENV['ANTHROPIC_API_KEY']);
            echo json_encode(['success' => true, 'translation' => $translation]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    } elseif ($action === 'rewrite') {
        $germanText = trim($_POST['german_text'] ?? '');

        if (empty($germanText)) {
            echo json_encode(['success' => false, 'error' => 'Please enter a German property description.']);
            exit;
        }

        if (!isset($_ENV['ANTHROPIC_API_KEY']) || $_ENV['ANTHROPIC_API_KEY'] === 'your_api_key_here') {
            echo json_encode(['success' => false, 'error' => 'API key not configured. Please add your Anthropic API key to the .env file.']);
            exit;
        }

        try {
            $rewritten = rewriteGermanText($germanText, $_ENV['ANTHROPIC_API_KEY']);
            echo json_encode(['success' => true, 'rewritten' => $rewritten]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid action.']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Combined Property Tools</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 2em;
            text-align: center;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 1.1em;
            text-align: center;
        }

        /* Tab Styles */
        .tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }

        .tab-button {
            padding: 15px 30px;
            background: #f0f0f0;
            border: none;
            border-radius: 6px 6px 0 0;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            color: #666;
            margin-right: 5px;
            transition: all 0.3s;
        }

        .tab-button.active {
            background: #667eea;
            color: white;
        }

        .tab-button:hover:not(.active) {
            background: #e0e0e0;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Common Styles */
        .error {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .success {
            background: #efe;
            border: 1px solid #cfc;
            color: #3c3;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: none;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 0.95em;
        }

        select, input, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-family: inherit;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        select:focus, input:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        textarea.features {
            min-height: 80px;
            resize: vertical;
        }

        textarea.input {
            min-height: 200px;
        }

        textarea.output {
            min-height: 200px;
            background-color: #f9f9f9;
            resize: vertical;
            white-space: pre-wrap;
            line-height: 1.6;
        }

        .button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .button:active {
            transform: translateY(0);
        }

        .button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        hr {
            border: none;
            border-top: 1px solid #e0e0e0;
            margin: 30px 0;
        }

        .output-section {
            position: relative;
        }

        .translations-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
            margin-top: 30px;
        }

        .translation-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            position: relative;
        }

        .german-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
            margin-bottom: 25px;
        }

        .german-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            position: relative;
        }

        .translation-box label,
        .german-box label {
            color: #667eea;
            font-size: 1em;
            margin-bottom: 10px;
        }

        .flag {
            font-size: 1.5em;
            margin-right: 8px;
        }

        .spinner-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: none;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            z-index: 10;
        }

        .spinner-overlay.active {
            display: flex;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(102, 126, 234, 0.2);
            border-radius: 50%;
            border-top-color: #667eea;
            animation: spin 1s ease-in-out infinite;
        }

        .spinner-text {
            color: #667eea;
            font-size: 14px;
            font-weight: 500;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .translation-box.loading textarea,
        .german-box.loading textarea,
        .output-section.loading textarea {
            opacity: 0.3;
        }

        @media (max-width: 1024px) {
            .german-grid {
                grid-template-columns: 1fr;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .translations-grid {
                grid-template-columns: 1fr;
            }
            
            .tabs {
                flex-wrap: wrap;
            }
            
            .tab-button {
                flex: 1 0 auto;
                text-align: center;
                padding: 12px 15px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏠 Combined Property Tools</h1>
        <p class="subtitle">Generate, translate, and rewrite property descriptions in one place</p>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-button active" data-tab="generator">📝 Description Generator</button>
            <button class="tab-button" data-tab="translator">🌐 Translator</button>
            <button class="tab-button" data-tab="rewriter">✨ Rewriter</button>
        </div>

        <!-- Generator Tab -->
        <div id="generator" class="tab-content active">
            <div id="error-message-generator" class="error" style="display: none;"></div>
            <div id="success-message-generator" class="success"></div>

            <form id="description-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="property_type">Immobilientyp *</label>
                        <select name="property_type" id="property_type" required>
                            <option value="">Bitte wählen...</option>
                            <option value="Villa">Villa</option>
                            <option value="Apartment">Apartment</option>
                            <option value="Finca">Finca</option>
                            <option value="Townhouse">Townhouse</option>
                            <option value="Penthouse">Penthouse</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="town">Ort *</label>
                        <select name="town" id="town" required>
                            <option value="">Bitte wählen...</option>
                            <option value="Palma">Palma</option>
                            <option value="Port d'Andratx">Port d'Andratx</option>
                            <option value="Pollença">Pollença</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="region">Region *</label>
                        <select name="region" id="region" required>
                            <option value="">Bitte wählen...</option>
                            <option value="Süd">Süd</option>
                            <option value="West">West</option>
                            <option value="Nord">Nord</option>
                            <option value="Ost">Ost</option>
                            <option value="Zentrum">Zentrum</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="bedrooms">Schlafzimmer *</label>
                        <select name="bedrooms" id="bedrooms" required>
                            <option value="">Bitte wählen...</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="bathrooms">Badezimmer *</label>
                        <select name="bathrooms" id="bathrooms" required>
                            <option value="">Bitte wählen...</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="living_area">Wohnfläche (m²) *</label>
                        <input type="number" name="living_area" id="living_area" min="1" placeholder="z.B. 150" required>
                    </div>

                    <div class="form-group">
                        <label for="plot_size">Grundstücksgröße (m²) *</label>
                        <input type="number" name="plot_size" id="plot_size" min="0" placeholder="z.B. 500 (0 für Apartment)" required>
                    </div>

                    <div class="form-group full-width">
                        <label for="features">Zusätzliche Ausstattung</label>
                        <textarea
                            name="features"
                            id="features"
                            class="features"
                            placeholder="z.B. Meerblick, Pool, Garage, Klimaanlage, Fußbodenheizung, Terrasse..."
                        ></textarea>
                    </div>
                </div>

                <button type="submit" class="button" id="generate-btn">✨ Beschreibung Erstellen</button>
            </form>

            <hr>
            <h2 style="margin-bottom: 20px; color: #333;">Generierte Beschreibung</h2>

            <div class="output-section" id="output-section-generator">
                <div class="spinner-overlay">
                    <div class="spinner"></div>
                    <div class="spinner-text">Beschreibung wird erstellt...</div>
                </div>
                <textarea class="output" id="output-description" readonly placeholder="Die generierte Immobilienbeschreibung wird hier angezeigt..."></textarea>
            </div>
        </div>

        <!-- Translator Tab -->
        <div id="translator" class="tab-content">
            <div id="error-message-translator" class="error" style="display: none;"></div>
            <div id="success-message-translator" class="success"></div>

            <form id="translation-form">
                <div class="form-group">
                    <label for="german_text_translator">🇩🇪 German Property Description</label>
                    <textarea
                        name="german_text"
                        id="german_text_translator"
                        class="input"
                        placeholder="Enter your German property description here..."
                        required
                    ></textarea>
                </div>

                <button type="submit" class="button" id="translate-btn">🌐 Translate to All Languages</button>
            </form>

            <hr>
            <h2 style="margin-bottom: 20px; color: #333;">Translations</h2>

            <div class="translations-grid">
                <div class="translation-box" id="box-english-translator">
                    <label><span class="flag">🇬🇧</span>English</label>
                    <div class="spinner-overlay">
                        <div class="spinner"></div>
                        <div class="spinner-text">Translating...</div>
                    </div>
                    <textarea class="output" id="output-english-translator" readonly></textarea>
                </div>

                <div class="translation-box" id="box-russian-translator">
                    <label><span class="flag">🇷🇺</span>Russian</label>
                    <div class="spinner-overlay">
                        <div class="spinner"></div>
                        <div class="spinner-text">Translating...</div>
                    </div>
                    <textarea class="output" id="output-russian-translator" readonly></textarea>
                </div>

                <div class="translation-box" id="box-czech-translator">
                    <label><span class="flag">🇨🇿</span>Czech</label>
                    <div class="spinner-overlay">
                        <div class="spinner"></div>
                        <div class="spinner-text">Translating...</div>
                    </div>
                    <textarea class="output" id="output-czech-translator" readonly></textarea>
                </div>

                <div class="translation-box" id="box-spanish-translator">
                    <label><span class="flag">🇪🇸</span>Spanish (European)</label>
                    <div class="spinner-overlay">
                        <div class="spinner"></div>
                        <div class="spinner-text">Translating...</div>
                    </div>
                    <textarea class="output" id="output-spanish-translator" readonly></textarea>
                </div>
            </div>
        </div>

        <!-- Rewriter Tab -->
        <div id="rewriter" class="tab-content">
            <div id="error-message-rewriter" class="error" style="display: none;"></div>
            <div id="success-message-rewriter" class="success"></div>

            <form id="rewrite-form">
                <div class="german-grid">
                    <div class="german-box">
                        <label for="german_text_original">🇩🇪 Original German Description</label>
                        <textarea
                            name="german_text_original"
                            id="german_text_original"
                            class="input"
                            placeholder="Paste your original German property description here..."
                            required
                        ></textarea>
                    </div>

                    <div class="german-box" id="box-rewritten">
                        <label for="german_text_rewritten">🇩🇪 Rewritten German Description</label>
                        <div class="spinner-overlay">
                            <div class="spinner"></div>
                            <div class="spinner-text">Rewriting...</div>
                        </div>
                        <textarea
                            name="german_text_rewritten"
                            id="german_text_rewritten"
                            class="output"
                            placeholder="Rewritten version will appear here..."
                            readonly
                        ></textarea>
                    </div>
                </div>

                <button type="submit" class="button" id="rewrite-btn">✨ Rewrite to All Languages</button>
            </form>

            <hr>
            <h2 style="margin-bottom: 20px; color: #333;">Translations</h2>

            <div class="translations-grid">
                <div class="translation-box" id="box-english-rewriter">
                    <label><span class="flag">🇬🇧</span>English</label>
                    <div class="spinner-overlay">
                        <div class="spinner"></div>
                        <div class="spinner-text">Translating...</div>
                    </div>
                    <textarea class="output" id="output-english-rewriter" readonly></textarea>
                </div>

                <div class="translation-box" id="box-russian-rewriter">
                    <label><span class="flag">🇷🇺</span>Russian</label>
                    <div class="spinner-overlay">
                        <div class="spinner"></div>
                        <div class="spinner-text">Translating...</div>
                    </div>
                    <textarea class="output" id="output-russian-rewriter" readonly></textarea>
                </div>

                <div class="translation-box" id="box-czech-rewriter">
                    <label><span class="flag">🇨🇿</span>Czech</label>
                    <div class="spinner-overlay">
                        <div class="spinner"></div>
                        <div class="spinner-text">Translating...</div>
                    </div>
                    <textarea class="output" id="output-czech-rewriter" readonly></textarea>
                </div>

                <div class="translation-box" id="box-spanish-rewriter">
                    <label><span class="flag">🇪🇸</span>Spanish (European)</label>
                    <div class="spinner-overlay">
                        <div class="spinner"></div>
                        <div class="spinner-text">Translating...</div>
                    </div>
                    <textarea class="output" id="output-spanish-rewriter" readonly></textarea>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab switching functionality
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons and content
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

                // Add active class to clicked button
                button.classList.add('active');

                // Show corresponding content
                const tabId = button.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Generator form handler
        document.getElementById('description-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const generateBtn = document.getElementById('generate-btn');
            const errorMessage = document.getElementById('error-message-generator');
            const successMessage = document.getElementById('success-message-generator');
            const outputSection = document.getElementById('output-section-generator');
            const outputDescription = document.getElementById('output-description');
            const spinner = outputSection.querySelector('.spinner-overlay');

            // Hide messages
            errorMessage.style.display = 'none';
            successMessage.style.display = 'none';

            // Get form data
            const formData = new FormData(this);
            formData.append('ajax', '1');
            formData.append('action', 'generate');

            // Disable button and show spinner
            generateBtn.disabled = true;
            generateBtn.textContent = '⏳ Erstelle Beschreibung...';
            outputDescription.value = '';
            outputSection.classList.add('loading');
            spinner.classList.add('active');

            try {
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    outputDescription.value = data.description;
                    successMessage.textContent = '✅ Beschreibung erfolgreich erstellt!';
                    successMessage.style.display = 'block';
                } else {
                    throw new Error(data.error);
                }
            } catch (error) {
                errorMessage.textContent = `⚠️ Fehler: ${error.message}`;
                errorMessage.style.display = 'block';
            } finally {
                // Hide spinner and enable button
                outputSection.classList.remove('loading');
                spinner.classList.remove('active');
                generateBtn.disabled = false;
                generateBtn.textContent = '✨ Beschreibung Erstellen';
            }
        });

        // Translator form handler
        document.getElementById('translation-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const germanText = document.getElementById('german_text_translator').value.trim();
            const translateBtn = document.getElementById('translate-btn');
            const errorMessage = document.getElementById('error-message-translator');
            const successMessage = document.getElementById('success-message-translator');

            if (!germanText) {
                errorMessage.textContent = '⚠️ Please enter a German property description to translate.';
                errorMessage.style.display = 'block';
                return;
            }

            // Hide messages
            errorMessage.style.display = 'none';
            successMessage.style.display = 'none';

            // Disable button
            translateBtn.disabled = true;
            translateBtn.textContent = '⏳ Translating...';

            // Clear previous translations
            const languages = ['english', 'russian', 'czech', 'spanish'];
            languages.forEach(lang => {
                document.getElementById(`output-${lang}-translator`).value = '';
            });

            // Translate each language
            const languageMap = {
                'english': 'English',
                'russian': 'Russian',
                'czech': 'Czech',
                'spanish': 'Spanish'
            };

            let completedCount = 0;
            let hasError = false;

            for (const [key, value] of Object.entries(languageMap)) {
                translateLanguage(germanText, value, key, 'translator').then(() => {
                    completedCount++;
                    if (completedCount === languages.length && !hasError) {
                        successMessage.textContent = '✅ Translation completed successfully!';
                        successMessage.style.display = 'block';
                        translateBtn.disabled = false;
                        translateBtn.textContent = '🌐 Translate to All Languages';
                    }
                }).catch(error => {
                    hasError = true;
                    errorMessage.textContent = `⚠️ Translation failed: ${error}`;
                    errorMessage.style.display = 'block';
                    translateBtn.disabled = false;
                    translateBtn.textContent = '🌐 Translate to All Languages';
                });
            }
        });

        async function translateLanguage(germanText, language, outputKey, prefix) {
            const box = document.getElementById(`box-${outputKey}-${prefix}`);
            const output = document.getElementById(`output-${outputKey}-${prefix}`);
            const spinner = box.querySelector('.spinner-overlay');

            // Show spinner
            box.classList.add('loading');
            spinner.classList.add('active');

            try {
                const formData = new FormData();
                formData.append('ajax', '1');
                formData.append('action', 'translate');
                formData.append('german_text', germanText);
                formData.append('language', language);

                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    output.value = data.translation;
                } else {
                    throw new Error(data.error);
                }
            } finally {
                // Hide spinner
                box.classList.remove('loading');
                spinner.classList.remove('active');
            }
        }

        // Rewriter form handler
        document.getElementById('rewrite-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const germanTextOriginal = document.getElementById('german_text_original').value.trim();
            const rewriteBtn = document.getElementById('rewrite-btn');
            const errorMessage = document.getElementById('error-message-rewriter');
            const successMessage = document.getElementById('success-message-rewriter');

            if (!germanTextOriginal) {
                errorMessage.textContent = '⚠️ Please enter a German property description.';
                errorMessage.style.display = 'block';
                return;
            }

            // Hide messages
            errorMessage.style.display = 'none';
            successMessage.style.display = 'none';

            // Disable button
            rewriteBtn.disabled = true;
            rewriteBtn.textContent = '⏳ Processing...';

            // Clear previous results
            document.getElementById('german_text_rewritten').value = '';
            const languages = ['english', 'russian', 'czech', 'spanish'];
            languages.forEach(lang => {
                document.getElementById(`output-${lang}-rewriter`).value = '';
            });

            try {
                // Step 1: Rewrite German text
                const rewrittenText = await rewriteGerman(germanTextOriginal);

                if (!rewrittenText) {
                    throw new Error('Failed to rewrite German text');
                }

                // Step 2: Translate the rewritten text to all languages
                const languageMap = {
                    'english': 'English',
                    'russian': 'Russian',
                    'czech': 'Czech',
                    'spanish': 'Spanish'
                };

                let completedCount = 0;
                let hasError = false;

                for (const [key, value] of Object.entries(languageMap)) {
                    translateLanguage(rewrittenText, value, key, 'rewriter').then(() => {
                        completedCount++;
                        if (completedCount === languages.length && !hasError) {
                            successMessage.textContent = '✅ Rewriting and translation completed successfully!';
                            successMessage.style.display = 'block';
                            rewriteBtn.disabled = false;
                            rewriteBtn.textContent = '✨ Rewrite to All Languages';
                        }
                    }).catch(error => {
                        hasError = true;
                        errorMessage.textContent = `⚠️ Translation failed: ${error}`;
                        errorMessage.style.display = 'block';
                        rewriteBtn.disabled = false;
                        rewriteBtn.textContent = '✨ Rewrite to All Languages';
                    });
                }

            } catch (error) {
                errorMessage.textContent = `⚠️ Error: ${error.message}`;
                errorMessage.style.display = 'block';
                rewriteBtn.disabled = false;
                rewriteBtn.textContent = '✨ Rewrite to All Languages';
            }
        });

        async function rewriteGerman(originalText) {
            const box = document.getElementById('box-rewritten');
            const output = document.getElementById('german_text_rewritten');
            const spinner = box.querySelector('.spinner-overlay');

            // Show spinner
            box.classList.add('loading');
            spinner.classList.add('active');

            try {
                const formData = new FormData();
                formData.append('ajax', '1');
                formData.append('action', 'rewrite');
                formData.append('german_text', originalText);

                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    output.value = data.rewritten;
                    return data.rewritten;
                } else {
                    throw new Error(data.error);
                }
            } finally {
                // Hide spinner
                box.classList.remove('loading');
                spinner.classList.remove('active');
            }
        }
    </script>
</body>
</html>