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
