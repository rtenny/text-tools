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
});
