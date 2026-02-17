/**
 * ListingLingo Text Tools - Rewriter Tab
 */
function initRewriter() {
    var rewriteBtn = document.getElementById('rewrite-btn');
    if (!rewriteBtn) return;

    var BTN_DEFAULT = '<i data-lucide="wand-2" class="w-4 h-4 mr-2 inline"></i> Rewrite & Translate';
    var BTN_LOADING = '<i data-lucide="wand-2" class="w-4 h-4 mr-2 inline"></i> Rewriting...';

    rewriteBtn.addEventListener('click', function () {
        var originalText = document.getElementById('original_text_rewriter').value.trim();
        if (!originalText) return;

        hideError('error-message-rewriter');
        setButtonLoading(rewriteBtn, BTN_LOADING);
        showSpinner('box-rewritten');

        ajaxPost(window.BASE_URL + 'tools/rewrite', {
            original_text: originalText
        }).then(function (data) {
            hideSpinner('box-rewritten');

            if (!data.success) {
                showError('error-message-rewriter', data.error);
                return;
            }

            document.getElementById('output-rewritten').value = data.rewritten;
            showCopyBtn('output-rewritten');

            var promises = translateBoxes('#rewriter .translation-box[data-lang]', data.rewritten, 'rewriter');
            return Promise.all(promises);
        }).catch(function (err) {
            hideSpinner('box-rewritten');
            showError('error-message-rewriter', err.message);
        }).finally(function () {
            restoreButton(rewriteBtn, BTN_DEFAULT);
        });
    });
}
