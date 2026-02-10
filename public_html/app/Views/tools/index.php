<?= $this->extend('layouts/user') ?>

<?= $this->section('content') ?>

<!-- Tab Navigation -->
<div class="tabs flex mb-6 border-b border-[#3a3d42]">
    <button class="tab-button active flex items-center" data-tab="translator"><i data-lucide="globe" class="w-4 h-4 mr-2"></i> Translator</button>
    <button class="tab-button flex items-center" data-tab="rewriter"><i data-lucide="wand-2" class="w-4 h-4 mr-2"></i> Rewriter</button>
    <button class="tab-button flex items-center" data-tab="generator"><i data-lucide="sparkles" class="w-4 h-4 mr-2"></i> Generator</button>
    <button class="tab-button flex items-center" data-tab="alttext"><i data-lucide="image" class="w-4 h-4 mr-2"></i> Alt Text</button>
</div>

<!-- ============================================================ -->
<!-- Tab 1: Multilingual Translator -->
<!-- ============================================================ -->
<div id="translator" class="tab-content active">
    <div class="card p-6">
        <h2 class="text-xl font-semibold text-white mb-4">Multilingual Translator</h2>
        <p class="text-sm text-gray-400 mb-6">Enter your English property description and translate it to all configured languages.</p>

        <div id="error-message-translator" class="alert alert-error mb-4" style="display: none;"></div>

        <form id="translation-form">
            <div class="mb-4">
                <label for="source_text_translator" class="form-label">English Source Text</label>
                <textarea name="source_text" id="source_text_translator" class="form-textarea" rows="8" placeholder="Enter your English property description here..." required></textarea>
            </div>

            <button type="submit" class="btn-primary flex items-center justify-center" id="translate-btn"><i data-lucide="globe" class="w-4 h-4 mr-2"></i> Translate to All Languages</button>
        </form>

        <hr class="border-[#3a3d42] my-6">

        <h3 class="text-lg font-semibold text-white mb-4">Translations</h3>

        <div class="translations-grid grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php
            $langLabels = [
                'de' => 'German',
                'es' => 'Spanish (European)',
            ];
            foreach ($languages as $lang):
                if ($lang === 'en') continue;
                $langName = $langLabels[$lang] ?? strtoupper($lang);
            ?>
            <div class="translation-box" id="box-<?= $lang ?>-translator" data-lang="<?= $lang ?>">
                <div class="flex justify-between items-center mb-2">
                    <label class="form-label mb-0"><?= $langName ?></label>
                    <button type="button" class="copy-btn text-xs text-[#D4AF37] hover:text-[#C29F2F]" data-target="output-<?= $lang ?>-translator" style="display: none;"><i data-lucide="copy" class="w-3 h-3 mr-1 inline"></i> Copy</button>
                </div>
                <div class="spinner-overlay">
                    <div class="spinner"></div>
                    <div class="spinner-text">Translating...</div>
                </div>
                <textarea class="form-textarea output" id="output-<?= $lang ?>-translator" rows="6" readonly placeholder="Translation will appear here..."></textarea>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- Tab 2: Unique Rewriter -->
<!-- ============================================================ -->
<div id="rewriter" class="tab-content">
    <div class="card p-6">
        <h2 class="text-xl font-semibold text-white mb-4">Unique Rewriter</h2>
        <p class="text-sm text-gray-400 mb-6">Paste an English description to rewrite it uniquely, then translate to all languages.</p>

        <div id="error-message-rewriter" class="alert alert-error mb-4" style="display: none;"></div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
            <div>
                <label for="original_text_rewriter" class="form-label">Original English Description</label>
                <textarea name="original_text" id="original_text_rewriter" class="form-textarea" rows="8" placeholder="Paste your original English property description here..." required></textarea>
            </div>

            <div class="translation-box" id="box-rewritten">
                <div class="flex justify-between items-center mb-2">
                    <label class="form-label mb-0">Rewritten English Description</label>
                    <button type="button" class="copy-btn text-xs text-[#D4AF37] hover:text-[#C29F2F]" data-target="output-rewritten" style="display: none;"><i data-lucide="copy" class="w-3 h-3 mr-1 inline"></i> Copy</button>
                </div>
                <div class="spinner-overlay">
                    <div class="spinner"></div>
                    <div class="spinner-text">Rewriting...</div>
                </div>
                <textarea class="form-textarea output" id="output-rewritten" rows="8" readonly placeholder="Rewritten version will appear here..."></textarea>
            </div>
        </div>

        <button type="button" class="btn-primary" id="rewrite-btn"><i data-lucide="wand-2" class="w-4 h-4 mr-2 inline"></i> Rewrite & Translate</button>

        <hr class="border-[#3a3d42] my-6">

        <h3 class="text-lg font-semibold text-white mb-4">Translations</h3>

        <div class="translations-grid grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($languages as $lang):
                if ($lang === 'en') continue;
                $langName = $langLabels[$lang] ?? strtoupper($lang);
            ?>
            <div class="translation-box" id="box-<?= $lang ?>-rewriter" data-lang="<?= $lang ?>">
                <div class="flex justify-between items-center mb-2">
                    <label class="form-label mb-0"><?= $langName ?></label>
                    <button type="button" class="copy-btn text-xs text-[#D4AF37] hover:text-[#C29F2F]" data-target="output-<?= $lang ?>-rewriter" style="display: none;"><i data-lucide="copy" class="w-3 h-3 mr-1 inline"></i> Copy</button>
                </div>
                <div class="spinner-overlay">
                    <div class="spinner"></div>
                    <div class="spinner-text">Translating...</div>
                </div>
                <textarea class="form-textarea output" id="output-<?= $lang ?>-rewriter" rows="6" readonly placeholder="Translation will appear here..."></textarea>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- Tab 3: Property Description Generator -->
<!-- ============================================================ -->
<div id="generator" class="tab-content">
    <div class="card p-6">
        <h2 class="text-xl font-semibold text-white mb-4">Property Description Generator</h2>
        <p class="text-sm text-gray-400 mb-6">Fill in the property details to generate a professional English description, then translate to all languages.</p>

        <div id="error-message-generator" class="alert alert-error mb-4" style="display: none;"></div>

        <form id="generator-form">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <!-- Location -->
                <div>
                    <label for="location" class="form-label">Location *</label>
                    <select name="location" id="location" class="form-select" required>
                        <option value="">Please select...</option>
                        <?php if (!empty($towns)): ?>
                            <?php foreach ($towns as $town): ?>
                                <option value="<?= esc($town['name']) ?>"><?= esc($town['name']) ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No towns available for this project</option>
                        <?php endif; ?>
                    </select>
                    <?php if (empty($towns)): ?>
                        <p class="text-xs text-red-400 mt-1">
                            No towns have been assigned to this project yet. Contact your administrator.
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Property Type -->
                <div>
                    <label for="property_type" class="form-label">Property Type *</label>
                    <select name="property_type" id="property_type" class="form-select" required>
                        <option value="">Please select...</option>
                        <option value="Villa">Villa</option>
                        <option value="Apartment">Apartment</option>
                        <option value="Finca">Finca</option>
                        <option value="Townhouse">Townhouse</option>
                        <option value="Penthouse">Penthouse</option>
                    </select>
                </div>

                <!-- Bedrooms -->
                <div>
                    <label for="bedrooms" class="form-label">Bedrooms *</label>
                    <select name="bedrooms" id="bedrooms" class="form-select" required>
                        <option value="">Please select...</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                    </select>
                </div>

                <!-- Bathrooms -->
                <div>
                    <label for="bathrooms" class="form-label">Bathrooms *</label>
                    <select name="bathrooms" id="bathrooms" class="form-select" required>
                        <option value="">Please select...</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                    </select>
                </div>

                <!-- Living Area -->
                <div>
                    <label for="living_area" class="form-label">Living Area (m²) *</label>
                    <input type="number" name="living_area" id="living_area" class="form-input" min="1" placeholder="e.g. 150" required>
                </div>

                <!-- Plot Size -->
                <div>
                    <label for="plot_size" class="form-label">Plot Size (m²)</label>
                    <input type="number" name="plot_size" id="plot_size" class="form-input" min="0" placeholder="e.g. 500 (0 for apartment)">
                </div>

                <!-- Features -->
                <div class="md:col-span-2">
                    <label for="features" class="form-label">Additional Features</label>
                    <textarea name="features" id="features" class="form-textarea" rows="3" placeholder="e.g. Sea view, pool, garage, air conditioning, underfloor heating, terrace..."></textarea>
                </div>
            </div>

            <button type="submit" class="btn-primary" id="generate-btn"><i data-lucide="sparkles" class="w-4 h-4 mr-2 inline"></i> Generate Description</button>
        </form>

        <hr class="border-[#3a3d42] my-6">

        <h3 class="text-lg font-semibold text-white mb-4">Generated Description</h3>

        <!-- English Output -->
        <div class="translation-box mb-6" id="box-en-generator">
            <div class="flex justify-between items-center mb-2">
                <label class="form-label mb-0">English Description</label>
                <button type="button" class="copy-btn text-xs text-[#D4AF37] hover:text-[#C29F2F]" data-target="output-en-generator" style="display: none;"><i data-lucide="copy" class="w-3 h-3 mr-1 inline"></i> Copy</button>
            </div>
            <div class="spinner-overlay">
                <div class="spinner"></div>
                <div class="spinner-text">Generating description...</div>
            </div>
            <textarea class="form-textarea output" id="output-en-generator" rows="8" readonly placeholder="Generated English description will appear here..."></textarea>
        </div>

        <h3 class="text-lg font-semibold text-white mb-4">Translations</h3>

        <div class="translations-grid grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($languages as $lang):
                if ($lang === 'en') continue;
                $langName = $langLabels[$lang] ?? strtoupper($lang);
            ?>
            <div class="translation-box" id="box-<?= $lang ?>-generator" data-lang="<?= $lang ?>">
                <div class="flex justify-between items-center mb-2">
                    <label class="form-label mb-0"><?= $langName ?></label>
                    <button type="button" class="copy-btn text-xs text-[#D4AF37] hover:text-[#C29F2F]" data-target="output-<?= $lang ?>-generator" style="display: none;"><i data-lucide="copy" class="w-3 h-3 mr-1 inline"></i> Copy</button>
                </div>
                <div class="spinner-overlay">
                    <div class="spinner"></div>
                    <div class="spinner-text">Translating...</div>
                </div>
                <textarea class="form-textarea output" id="output-<?= $lang ?>-generator" rows="6" readonly placeholder="Translation will appear here..."></textarea>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- Tab 4: Image Alt Text Generator -->
<!-- ============================================================ -->
<div id="alttext" class="tab-content">
    <div class="card p-6">
        <h2 class="text-xl font-semibold text-white mb-4">Image Alt Text Generator</h2>
        <p class="text-sm text-gray-400 mb-6">Upload a property image or provide an image URL to generate SEO-optimized alt text in multiple languages.</p>

        <div id="error-message-alttext" class="alert alert-error mb-4" style="display: none;"></div>

        <form id="alttext-form">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <!-- Property Type -->
                <div>
                    <label for="alttext_property_type" class="form-label">Property Type *</label>
                    <select name="property_type" id="alttext_property_type" class="form-select" required>
                        <option value="">Please select...</option>
                        <option value="Villa">Villa</option>
                        <option value="Apartment">Apartment</option>
                        <option value="Finca">Finca</option>
                        <option value="Townhouse">Townhouse</option>
                        <option value="Penthouse">Penthouse</option>
                    </select>
                </div>

                <!-- Location -->
                <div>
                    <label for="alttext_location" class="form-label">Location *</label>
                    <select name="location" id="alttext_location" class="form-select" required>
                        <option value="">Please select...</option>
                        <?php if (!empty($towns)): ?>
                            <?php foreach ($towns as $town): ?>
                                <option value="<?= esc($town['name']) ?>"><?= esc($town['name']) ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No towns available for this project</option>
                        <?php endif; ?>
                    </select>
                    <?php if (empty($towns)): ?>
                        <p class="text-xs text-red-400 mt-1">
                            No towns have been assigned to this project yet. Contact your administrator.
                        </p>
                    <?php endif; ?>
                </div>

                <!-- City -->
                <div>
                    <label for="alttext_city" class="form-label">City *</label>
                    <input type="text" name="city" id="alttext_city" class="form-input" placeholder="e.g. Marbella" required>
                </div>

                <!-- Image Source Toggle -->
                <div>
                    <label class="form-label">Image Source *</label>
                    <div class="flex gap-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="image_source" value="upload" id="image_source_upload" class="mr-2" checked>
                            <span class="text-sm text-gray-300">Upload File</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="image_source" value="url" id="image_source_url" class="mr-2">
                            <span class="text-sm text-gray-300">Image URL</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- File Upload -->
            <div id="image_upload_section" class="mb-4">
                <label for="image_file" class="form-label">Upload Image (JPG, PNG, WebP - Max 5MB)</label>
                <input type="file" name="image_file" id="image_file" class="form-input" accept="image/jpeg,image/png,image/webp">
            </div>

            <!-- URL Input -->
            <div id="image_url_section" class="mb-4" style="display: none;">
                <label for="image_url" class="form-label">Image URL</label>
                <input type="url" name="image_url" id="image_url" class="form-input" placeholder="https://example.com/image.jpg">
            </div>

            <!-- Image Preview -->
            <div id="image_preview_container" class="mb-4" style="display: none;">
                <label class="form-label">Image Preview</label>
                <div class="border border-[#3a3d42] rounded-lg p-4 bg-[#1e2024]">
                    <img id="image_preview" src="" alt="Preview" class="max-w-full max-h-64 mx-auto">
                </div>
            </div>

            <button type="submit" class="btn-primary" id="alttext-generate-btn"><i data-lucide="sparkles" class="w-4 h-4 mr-2 inline"></i> Generate Alt Text</button>
        </form>

        <hr class="border-[#3a3d42] my-6">

        <h3 class="text-lg font-semibold text-white mb-4">Alt Text Options</h3>
        <p class="text-sm text-gray-400 mb-4">Select your preferred alt text option to translate it to other languages.</p>

        <!-- Alt Text Options -->
        <div class="grid grid-cols-1 gap-4 mb-6">
            <div class="alttext-option-card border border-[#3a3d42] rounded-lg p-4 cursor-pointer hover:border-[#D4AF37] transition-colors" data-option="1" style="display: none;">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center">
                        <input type="radio" name="selected_alttext" value="1" id="alttext_option_1" class="mr-3">
                        <label for="alttext_option_1" class="form-label mb-0 cursor-pointer">Option 1</label>
                    </div>
                    <button type="button" class="copy-btn text-xs text-[#D4AF37] hover:text-[#C29F2F]" data-target="alttext_content_1"><i data-lucide="copy" class="w-3 h-3 mr-1 inline"></i> Copy</button>
                </div>
                <p class="text-sm text-gray-300" id="alttext_content_1"></p>
                <p class="text-xs text-gray-500 mt-2">Character count: <span id="alttext_count_1">0</span></p>
            </div>

            <div class="alttext-option-card border border-[#3a3d42] rounded-lg p-4 cursor-pointer hover:border-[#D4AF37] transition-colors" data-option="2" style="display: none;">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center">
                        <input type="radio" name="selected_alttext" value="2" id="alttext_option_2" class="mr-3">
                        <label for="alttext_option_2" class="form-label mb-0 cursor-pointer">Option 2</label>
                    </div>
                    <button type="button" class="copy-btn text-xs text-[#D4AF37] hover:text-[#C29F2F]" data-target="alttext_content_2"><i data-lucide="copy" class="w-3 h-3 mr-1 inline"></i> Copy</button>
                </div>
                <p class="text-sm text-gray-300" id="alttext_content_2"></p>
                <p class="text-xs text-gray-500 mt-2">Character count: <span id="alttext_count_2">0</span></p>
            </div>

            <div class="alttext-option-card border border-[#3a3d42] rounded-lg p-4 cursor-pointer hover:border-[#D4AF37] transition-colors" data-option="3" style="display: none;">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center">
                        <input type="radio" name="selected_alttext" value="3" id="alttext_option_3" class="mr-3">
                        <label for="alttext_option_3" class="form-label mb-0 cursor-pointer">Option 3</label>
                    </div>
                    <button type="button" class="copy-btn text-xs text-[#D4AF37] hover:text-[#C29F2F]" data-target="alttext_content_3"><i data-lucide="copy" class="w-3 h-3 mr-1 inline"></i> Copy</button>
                </div>
                <p class="text-sm text-gray-300" id="alttext_content_3"></p>
                <p class="text-xs text-gray-500 mt-2">Character count: <span id="alttext_count_3">0</span></p>
            </div>
        </div>

        <h3 class="text-lg font-semibold text-white mb-4">Translations</h3>

        <div class="translations-grid grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php
            $langLabels = [
                'de' => 'German',
                'es' => 'Spanish (European)',
            ];
            foreach ($languages as $lang):
                if ($lang === 'en') continue;
                $langName = $langLabels[$lang] ?? strtoupper($lang);
            ?>
            <div class="translation-box" id="box-<?= $lang ?>-alttext" data-lang="<?= $lang ?>">
                <div class="flex justify-between items-center mb-2">
                    <label class="form-label mb-0"><?= $langName ?></label>
                    <button type="button" class="copy-btn text-xs text-[#D4AF37] hover:text-[#C29F2F]" data-target="output-<?= $lang ?>-alttext" style="display: none;"><i data-lucide="copy" class="w-3 h-3 mr-1 inline"></i> Copy</button>
                </div>
                <div class="spinner-overlay">
                    <div class="spinner"></div>
                    <div class="spinner-text">Translating...</div>
                </div>
                <textarea class="form-textarea output" id="output-<?= $lang ?>-alttext" rows="3" readonly placeholder="Translation will appear here..."></textarea>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- CSRF Token for AJAX -->
<script>
    window.CSRF_TOKEN_NAME = '<?= csrf_token() ?>';
    window.CSRF_TOKEN_HASH = '<?= csrf_hash() ?>';
    window.BASE_URL = '<?= base_url() ?>';
</script>

<?= $this->endSection() ?>
