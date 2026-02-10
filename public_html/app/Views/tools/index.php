<?= $this->extend('layouts/user') ?>

<?= $this->section('content') ?>

<!-- Tab Navigation -->
<div class="tabs flex mb-6 border-b border-[#3a3d42]">
    <button class="tab-button active flex items-center" data-tab="translator"><i data-lucide="globe" class="w-4 h-4 mr-2"></i> Translator</button>
    <button class="tab-button flex items-center" data-tab="rewriter"><i data-lucide="wand-2" class="w-4 h-4 mr-2"></i> Rewriter</button>
    <button class="tab-button flex items-center" data-tab="generator"><i data-lucide="sparkles" class="w-4 h-4 mr-2"></i> Generator</button>
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

<!-- CSRF Token for AJAX -->
<script>
    window.CSRF_TOKEN_NAME = '<?= csrf_token() ?>';
    window.CSRF_TOKEN_HASH = '<?= csrf_hash() ?>';
    window.BASE_URL = '<?= base_url() ?>';
</script>

<?= $this->endSection() ?>
