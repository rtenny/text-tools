/**
 * ListingLingo Text Tools - Shared Utilities
 *
 * Global helper functions shared across all feature modules.
 * Loaded before feature files so all helpers are available immediately.
 */

// ============================================================
// Constants
// ============================================================
var MAX_IMAGE_SIZE = 5242880; // 5MB
var ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

// ============================================================
// AJAX POST with CSRF
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
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(function (response) {
        var newToken = response.headers.get('X-CSRF-TOKEN');
        if (newToken) window.CSRF_TOKEN_HASH = newToken;
        return response.json();
    }).then(function (data) {
        if (data.session_expired === true) {
            window.location.href = window.BASE_URL + 'login';
            return Promise.reject(new Error('Session expired'));
        }
        if (data.csrf_token) window.CSRF_TOKEN_HASH = data.csrf_token;
        return data;
    });
}

// ============================================================
// Spinner
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

// ============================================================
// Copy button
// ============================================================
function showCopyBtn(targetId) {
    var btn = document.querySelector('.copy-btn[data-target="' + targetId + '"]');
    if (btn) btn.style.display = 'inline';
}

// ============================================================
// Error messages
// ============================================================
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
// Button loading state
// ============================================================
function setButtonLoading(btn, loadingHtml) {
    btn.disabled = true;
    btn.innerHTML = loadingHtml;
    if (window.lucide) lucide.createIcons();
}

function restoreButton(btn, defaultHtml) {
    btn.disabled = false;
    btn.innerHTML = defaultHtml;
    if (window.lucide) lucide.createIcons();
}

// ============================================================
// Translation helpers
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

function translateTitle(sourceTitle, lang) {
    var boxId = 'box-title-' + lang + '-generator';
    var outputId = 'output-title-' + lang + '-generator';

    showSpinner(boxId);

    return ajaxPost(window.BASE_URL + 'tools/translate', {
        source_text: sourceTitle,
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

/**
 * Iterate all translation boxes matching a CSS selector and kick off
 * translateLanguage for each. Returns an array of promises.
 */
function translateBoxes(selector, text, suffix) {
    var promises = [];
    document.querySelectorAll(selector).forEach(function (box) {
        var lang = box.getAttribute('data-lang');
        if (lang) promises.push(translateLanguage(text, lang, suffix));
    });
    return promises;
}

// ============================================================
// Tab switching
// ============================================================
function initTabSwitching() {
    document.querySelectorAll('.tab-button').forEach(function (button) {
        button.addEventListener('click', function () {
            document.querySelectorAll('.tab-button').forEach(function (btn) {
                btn.classList.remove('active');
            });
            document.querySelectorAll('.tab-content').forEach(function (content) {
                content.classList.remove('active');
            });
            button.classList.add('active');
            var tabId = button.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });
}

// ============================================================
// Copy to clipboard (event delegation)
// ============================================================
function initCopyButtons() {
    document.addEventListener('click', function (e) {
        var copyBtn = e.target.closest('.copy-btn');
        if (!copyBtn) return;

        e.preventDefault();
        e.stopPropagation();

        var targetId = copyBtn.getAttribute('data-target');
        if (!targetId) {
            console.error('Copy button missing data-target attribute');
            return;
        }

        var targetElement = document.getElementById(targetId);
        if (!targetElement) {
            console.error('Target element not found:', targetId);
            return;
        }

        var textToCopy = targetElement.value || targetElement.textContent || targetElement.innerText;
        textToCopy = (textToCopy || '').trim();

        if (!textToCopy) {
            console.warn('No text to copy from element:', targetId);
            return;
        }

        var originalText = copyBtn.childNodes[copyBtn.childNodes.length - 1].textContent;

        function showSuccess() {
            if (copyBtn.childNodes.length > 0) {
                var lastNode = copyBtn.childNodes[copyBtn.childNodes.length - 1];
                if (lastNode.nodeType === Node.TEXT_NODE) {
                    lastNode.textContent = ' Copied!';
                }
            }
            setTimeout(function () {
                if (copyBtn.childNodes.length > 0) {
                    var lastNode = copyBtn.childNodes[copyBtn.childNodes.length - 1];
                    if (lastNode.nodeType === Node.TEXT_NODE) {
                        lastNode.textContent = originalText;
                    }
                }
            }, 2000);
        }

        if (typeof navigator !== 'undefined' &&
            navigator.clipboard &&
            typeof navigator.clipboard.writeText === 'function') {
            try {
                navigator.clipboard.writeText(textToCopy)
                    .then(function () { showSuccess(); })
                    .catch(function (err) {
                        console.warn('Clipboard API failed, using fallback:', err);
                        copyUsingFallback();
                    });
            } catch (err) {
                console.warn('Clipboard API not available, using fallback:', err);
                copyUsingFallback();
            }
        } else {
            copyUsingFallback();
        }

        function copyUsingFallback() {
            var success = false;

            if (targetElement.select) {
                targetElement.select();
                targetElement.setSelectionRange(0, 99999);
                success = document.execCommand('copy');
                window.getSelection().removeAllRanges();
            } else {
                var tempTextarea = document.createElement('textarea');
                tempTextarea.value = textToCopy;
                tempTextarea.style.position = 'fixed';
                tempTextarea.style.left = '-9999px';
                tempTextarea.style.top = '0';
                document.body.appendChild(tempTextarea);
                tempTextarea.focus();
                tempTextarea.select();
                tempTextarea.setSelectionRange(0, 99999);
                try {
                    success = document.execCommand('copy');
                } catch (err) {
                    console.error('Copy failed:', err);
                    success = false;
                }
                document.body.removeChild(tempTextarea);
                window.getSelection().removeAllRanges();
            }

            if (success) {
                showSuccess();
            } else {
                console.error('Copy operation failed');
                if (copyBtn.childNodes.length > 0) {
                    var lastNode = copyBtn.childNodes[copyBtn.childNodes.length - 1];
                    if (lastNode.nodeType === Node.TEXT_NODE) {
                        lastNode.textContent = ' Failed!';
                        setTimeout(function () { lastNode.textContent = originalText; }, 2000);
                    }
                }
            }
        }
    });
}
