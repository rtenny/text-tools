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
            <div class="flex items-center mb-2">
                <input type="radio" name="selected_alttext" value="1" id="alttext_option_1" class="mr-3">
                <label for="alttext_option_1" class="form-label mb-0 cursor-pointer">Option 1</label>
            </div>
            <p class="text-sm text-gray-300" id="alttext_content_1"></p>
            <p class="text-xs text-gray-500 mt-2">Character count: <span id="alttext_count_1">0</span></p>
        </div>

        <div class="alttext-option-card border border-[#3a3d42] rounded-lg p-4 cursor-pointer hover:border-[#D4AF37] transition-colors" data-option="2" style="display: none;">
            <div class="flex items-center mb-2">
                <input type="radio" name="selected_alttext" value="2" id="alttext_option_2" class="mr-3">
                <label for="alttext_option_2" class="form-label mb-0 cursor-pointer">Option 2</label>
            </div>
            <p class="text-sm text-gray-300" id="alttext_content_2"></p>
            <p class="text-xs text-gray-500 mt-2">Character count: <span id="alttext_count_2">0</span></p>
        </div>

        <div class="alttext-option-card border border-[#3a3d42] rounded-lg p-4 cursor-pointer hover:border-[#D4AF37] transition-colors" data-option="3" style="display: none;">
            <div class="flex items-center mb-2">
                <input type="radio" name="selected_alttext" value="3" id="alttext_option_3" class="mr-3">
                <label for="alttext_option_3" class="form-label mb-0 cursor-pointer">Option 3</label>
            </div>
            <p class="text-sm text-gray-300" id="alttext_content_3"></p>
            <p class="text-xs text-gray-500 mt-2">Character count: <span id="alttext_count_3">0</span></p>
        </div>
    </div>

    <hr class="border-[#3a3d42] my-6">

    <h3 class="text-lg font-semibold text-white mb-4">English</h3>

    <!-- English Alt Text -->
    <div class="translation-box mb-6" id="box-en-alttext">
        <div class="flex justify-between items-center mb-2">
            <label class="form-label mb-0">Alt Text</label>
            <button type="button" class="copy-btn text-xs text-[#D4AF37] hover:text-[#C29F2F]" data-target="output-en-alttext" style="display: none;"><i data-lucide="copy" class="w-3 h-3 mr-1 inline"></i> Copy</button>
        </div>
        <textarea class="form-textarea output" id="output-en-alttext" rows="3" placeholder="Select an option above to populate..."></textarea>
    </div>

    <h3 class="text-lg font-semibold text-white mb-4">Translations</h3>

    <div class="translations-grid grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php foreach ($languages as $lang):
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
