<div class="card p-6">
    <h2 class="text-xl font-semibold text-white mb-4">Property Description Generator</h2>
    <p class="text-sm text-gray-400 mb-6">Fill in the property details to generate a professional English description, then translate to all languages.</p>

    <div id="error-message-generator" class="alert alert-error mb-4" style="display: none;"></div>

    <form id="generator-form">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <!-- Location -->
            <div>
                <label for="location" class="form-label">Location/Town *</label>
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

            <!-- Feature Checkboxes -->
            <div class="md:col-span-2">
                <label class="form-label">Features</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-300 hover:text-white">
                        <input type="checkbox" name="feature_checks[]" value="Sea view" class="feature-checkbox accent-[#D4AF37]"> Sea View
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-300 hover:text-white">
                        <input type="checkbox" name="feature_checks[]" value="Golf view" class="feature-checkbox accent-[#D4AF37]"> Golf View
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-300 hover:text-white">
                        <input type="checkbox" name="feature_checks[]" value="First-line golf" class="feature-checkbox accent-[#D4AF37]"> First-line Golf
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-300 hover:text-white">
                        <input type="checkbox" name="feature_checks[]" value="First-line sea" class="feature-checkbox accent-[#D4AF37]"> First-line Sea
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-300 hover:text-white">
                        <input type="checkbox" name="feature_checks[]" value="Air conditioning" class="feature-checkbox accent-[#D4AF37]"> Air Conditioning
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-300 hover:text-white">
                        <input type="checkbox" name="feature_checks[]" value="Private pool" class="feature-checkbox accent-[#D4AF37]"> Private Pool
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-300 hover:text-white">
                        <input type="checkbox" name="feature_checks[]" value="Communal pool" class="feature-checkbox accent-[#D4AF37]"> Communal Pool
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-300 hover:text-white">
                        <input type="checkbox" name="feature_checks[]" value="Common garden" class="feature-checkbox accent-[#D4AF37]"> Common Garden
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-300 hover:text-white">
                        <input type="checkbox" name="feature_checks[]" value="Close to amenities" class="feature-checkbox accent-[#D4AF37]"> Close to Amenities
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-300 hover:text-white">
                        <input type="checkbox" name="feature_checks[]" value="Close to golf" class="feature-checkbox accent-[#D4AF37]"> Close to Golf
                    </label>
                </div>
            </div>

            <!-- Additional Description -->
            <div class="md:col-span-2">
                <label for="features" class="form-label">Additional Description</label>
                <textarea name="features" id="features" class="form-textarea" rows="3" placeholder="Any additional details about the property..."></textarea>
            </div>
        </div>

        <button type="submit" class="btn-primary" id="generate-btn"><i data-lucide="sparkles" class="w-4 h-4 mr-2 inline"></i> Generate Description</button>
    </form>

    <hr class="border-[#3a3d42] my-6">

    <h3 class="text-lg font-semibold text-white mb-4">English</h3>

    <!-- English Title -->
    <div class="translation-box mb-4" id="box-title-en-generator">
        <div class="flex justify-between items-center mb-2">
            <label class="form-label mb-0">Title</label>
            <button type="button" class="copy-btn text-xs text-[#D4AF37] hover:text-[#C29F2F]" data-target="output-title-en-generator" style="display: none;"><i data-lucide="copy" class="w-3 h-3 mr-1 inline"></i> Copy</button>
        </div>
        <input type="text" class="form-input output" id="output-title-en-generator" readonly placeholder="Generated title will appear here...">
    </div>

    <!-- English Description -->
    <div class="translation-box mb-6" id="box-en-generator">
        <div class="flex justify-between items-center mb-2">
            <label class="form-label mb-0">Description</label>
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
        <div>
            <h4 class="text-md font-medium text-white mb-3"><?= $langName ?></h4>

            <!-- Title -->
            <div class="translation-box mb-3" id="box-title-<?= $lang ?>-generator" data-lang="<?= $lang ?>">
                <div class="flex justify-between items-center mb-2">
                    <label class="form-label mb-0">Title</label>
                    <button type="button" class="copy-btn text-xs text-[#D4AF37] hover:text-[#C29F2F]" data-target="output-title-<?= $lang ?>-generator" style="display: none;"><i data-lucide="copy" class="w-3 h-3 mr-1 inline"></i> Copy</button>
                </div>
                <div class="spinner-overlay">
                    <div class="spinner"></div>
                    <div class="spinner-text">Translating...</div>
                </div>
                <input type="text" class="form-input output" id="output-title-<?= $lang ?>-generator" readonly placeholder="Translation will appear here...">
            </div>

            <!-- Description -->
            <div class="translation-box" id="box-<?= $lang ?>-generator" data-lang="<?= $lang ?>">
                <div class="flex justify-between items-center mb-2">
                    <label class="form-label mb-0">Description</label>
                    <button type="button" class="copy-btn text-xs text-[#D4AF37] hover:text-[#C29F2F]" data-target="output-<?= $lang ?>-generator" style="display: none;"><i data-lucide="copy" class="w-3 h-3 mr-1 inline"></i> Copy</button>
                </div>
                <div class="spinner-overlay">
                    <div class="spinner"></div>
                    <div class="spinner-text">Translating...</div>
                </div>
                <textarea class="form-textarea output" id="output-<?= $lang ?>-generator" rows="6" readonly placeholder="Translation will appear here..."></textarea>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
