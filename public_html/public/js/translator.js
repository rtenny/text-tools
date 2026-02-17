/**
 * ListingLingo Text Tools - Translator Tab
 */
function initTranslator() {
    var translationForm = document.getElementById('translation-form');
    if (!translationForm) return;

    var BTN_DEFAULT = '<i data-lucide="globe" class="w-4 h-4 mr-2"></i> Translate to All Languages';
    var BTN_LOADING = '<i data-lucide="globe" class="w-4 h-4 mr-2"></i> Translating...';

    translationForm.addEventListener('submit', function (e) {
        e.preventDefault();

        var sourceText = document.getElementById('source_text_translator').value.trim();
        if (!sourceText) return;

        var btn = document.getElementById('translate-btn-translator');
        hideError('error-message-translator');
        setButtonLoading(btn, BTN_LOADING);

        var promises = translateBoxes('#translator .translation-box[data-lang]', sourceText, 'translator');

        Promise.all(promises).catch(function (err) {
            showError('error-message-translator', err.message);
        }).finally(function () {
            restoreButton(btn, BTN_DEFAULT);
        });
    });
}
