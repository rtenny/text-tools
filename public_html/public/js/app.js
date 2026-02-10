/**
 * Property Text Tools - Frontend Application
 *
 * Handles tab switching, AJAX form submissions, spinners, and copy-to-clipboard.
 */

document.addEventListener('DOMContentLoaded', function () {
    // ============================================================
    // Tab Switching
    // ============================================================
    document.querySelectorAll('.tab-button').forEach(function (button) {
        button.addEventListener('click', function () {
            document.querySelectorAll('.tab-button').forEach(function (btn) { btn.classList.remove('active'); });
            document.querySelectorAll('.tab-content').forEach(function (content) { content.classList.remove('active'); });

            button.classList.add('active');
            var tabId = button.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });

    // ============================================================
    // Copy to Clipboard
    // ============================================================
    document.querySelectorAll('.copy-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = btn.getAttribute('data-target');
            var textarea = document.getElementById(targetId);
            if (!textarea || !textarea.value) return;

            navigator.clipboard.writeText(textarea.value).then(function () {
                var original = btn.textContent;
                btn.textContent = '✅ Copied!';
                setTimeout(function () { btn.textContent = original; }, 2000);
            }).catch(function () {
                // Fallback
                textarea.select();
                document.execCommand('copy');
                window.getSelection().removeAllRanges();
            });
        });
    });

    // ============================================================
    // Helper: AJAX POST with CSRF
    // ============================================================
    function ajaxPost(url, data) {
        data[window.CSRF_TOKEN_NAME] = window.CSRF_TOKEN_HASH;

        var formData = new FormData();
        for (var key in data) {
            formData.append(key, data[key]);
        }

        return fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(function (response) {
            // Update CSRF token from response header if present
            var newToken = response.headers.get('X-CSRF-TOKEN');
            if (newToken) {
                window.CSRF_TOKEN_HASH = newToken;
            }
            return response.json();
        }).then(function (data) {
            // Check if session has expired
            if (data.session_expired === true) {
                // Redirect to login page
                window.location.href = window.BASE_URL + 'login';
                // Reject the promise to prevent further processing
                return Promise.reject(new Error('Session expired'));
            }

            // Update CSRF token if returned in response body
            if (data.csrf_token) {
                window.CSRF_TOKEN_HASH = data.csrf_token;
            }
            return data;
        });
    }

    // ============================================================
    // Helper: Show/Hide Spinner
    // ============================================================
    function showSpinner(boxId) {
        var box = document.getElementById(boxId);
        if (!box) return;
        box.classList.add('loading');
        var spinner = box.querySelector('.spinner-overlay');
        if (spinner) spinner.classList.add('active');
    }

    function hideSpinner(boxId) {
        var box = document.getElementById(boxId);
        if (!box) return;
        box.classList.remove('loading');
        var spinner = box.querySelector('.spinner-overlay');
        if (spinner) spinner.classList.remove('active');
    }

    function showCopyBtn(targetId) {
        var btn = document.querySelector('.copy-btn[data-target="' + targetId + '"]');
        if (btn) btn.style.display = 'inline';
    }

    function showError(elementId, message) {
        var el = document.getElementById(elementId);
        if (el) {
            el.textContent = message;
            el.style.display = 'block';
        }
    }

    function hideError(elementId) {
        var el = document.getElementById(elementId);
        if (el) el.style.display = 'none';
    }

    // ============================================================
    // Helper: Translate a single language
    // ============================================================
    function translateLanguage(sourceText, lang, suffix) {
        var boxId = 'box-' + lang + '-' + suffix;
        var outputId = 'output-' + lang + '-' + suffix;

        showSpinner(boxId);

        return ajaxPost(window.BASE_URL + 'tools/translate', {
            source_text: sourceText,
            target_language: lang
        }).then(function (data) {
            if (data.success) {
                document.getElementById(outputId).value = data.translation;
                showCopyBtn(outputId);
            } else {
                document.getElementById(outputId).value = 'Error: ' + data.error;
            }
        }).catch(function (err) {
            document.getElementById(outputId).value = 'Error: ' + err.message;
        }).finally(function () {
            hideSpinner(boxId);
        });
    }

    // ============================================================
    // Tab 1: Translator Form
    // ============================================================
    var translationForm = document.getElementById('translation-form');
    if (translationForm) {
        translationForm.addEventListener('submit', function (e) {
            e.preventDefault();

            var sourceText = document.getElementById('source_text_translator').value.trim();
            if (!sourceText) return;

            var btn = document.getElementById('translate-btn');
            hideError('error-message-translator');

            btn.disabled = true;
            btn.textContent = 'Translating...';

            // Get all translation boxes in this tab
            var boxes = document.querySelectorAll('#translator .translation-box');
            var promises = [];

            boxes.forEach(function (box) {
                var lang = box.getAttribute('data-lang');
                if (lang) {
                    promises.push(translateLanguage(sourceText, lang, 'translator'));
                }
            });

            Promise.all(promises).catch(function (err) {
                showError('error-message-translator', err.message);
            }).finally(function () {
                btn.disabled = false;
                btn.textContent = 'Translate to All Languages';
            });
        });
    }

    // ============================================================
    // Tab 2: Rewriter
    // ============================================================
    var rewriteBtn = document.getElementById('rewrite-btn');
    if (rewriteBtn) {
        rewriteBtn.addEventListener('click', function () {
            var originalText = document.getElementById('original_text_rewriter').value.trim();
            if (!originalText) return;

            hideError('error-message-rewriter');
            rewriteBtn.disabled = true;
            rewriteBtn.textContent = 'Rewriting...';

            // Step 1: Rewrite
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

                // Step 2: Translate the rewritten text
                var boxes = document.querySelectorAll('#rewriter .translation-box[data-lang]');
                var promises = [];

                boxes.forEach(function (box) {
                    var lang = box.getAttribute('data-lang');
                    if (lang) {
                        promises.push(translateLanguage(data.rewritten, lang, 'rewriter'));
                    }
                });

                return Promise.all(promises);
            }).catch(function (err) {
                hideSpinner('box-rewritten');
                showError('error-message-rewriter', err.message);
            }).finally(function () {
                rewriteBtn.disabled = false;
                rewriteBtn.textContent = 'Rewrite & Translate';
            });
        });
    }

    // ============================================================
    // Tab 3: Generator Form
    // ============================================================
    var generatorForm = document.getElementById('generator-form');
    if (generatorForm) {
        generatorForm.addEventListener('submit', function (e) {
            e.preventDefault();

            var btn = document.getElementById('generate-btn');
            hideError('error-message-generator');

            btn.disabled = true;
            btn.textContent = 'Generating...';

            // Collect form data
            var formFields = {
                property_type: document.getElementById('property_type').value,
                location: document.getElementById('location').value,
                bedrooms: document.getElementById('bedrooms').value,
                bathrooms: document.getElementById('bathrooms').value,
                living_area: document.getElementById('living_area').value,
                plot_size: document.getElementById('plot_size').value || '0',
                features: document.getElementById('features').value
            };

            // Step 1: Generate English description
            showSpinner('box-en-generator');

            ajaxPost(window.BASE_URL + 'tools/generate', formFields).then(function (data) {
                hideSpinner('box-en-generator');

                if (!data.success) {
                    showError('error-message-generator', data.error);
                    return;
                }

                document.getElementById('output-en-generator').value = data.description;
                showCopyBtn('output-en-generator');

                // Step 2: Translate to all languages
                var boxes = document.querySelectorAll('#generator .translation-box[data-lang]');
                var promises = [];

                boxes.forEach(function (box) {
                    var lang = box.getAttribute('data-lang');
                    if (lang) {
                        promises.push(translateLanguage(data.description, lang, 'generator'));
                    }
                });

                return Promise.all(promises);
            }).catch(function (err) {
                hideSpinner('box-en-generator');
                showError('error-message-generator', err.message);
            }).finally(function () {
                btn.disabled = false;
                btn.textContent = 'Generate Description';
            });
        });
    }
});
