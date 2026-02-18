/**
 * ListingLingo Text Tools - Main Entry Point
 *
 * Initialises tab switching, copy-to-clipboard, and all feature modules.
 * Shared utilities live in utils.js; feature logic in translator/rewriter/generator/alttext.js.
 */
document.addEventListener('DOMContentLoaded', function () {
    initTabSwitching();
    initCopyButtons();
    initTranslator();
    initRewriter();
    initGenerator();
    initAltText();

    // Apply limit state from server-rendered data on page load
    if (window.TRANSLATION_LIMIT && window.TRANSLATION_LIMIT.limit_reached) {
        disableTranslateButtons();
        showTranslateLimitMessage();
    }
});
