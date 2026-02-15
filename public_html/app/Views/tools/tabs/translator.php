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
        <?php foreach ($languages as $lang):
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
