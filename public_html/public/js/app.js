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
            var targetElement = document.getElementById(targetId);
            if (!targetElement) return;

            // Get text content - works for both textarea and text elements
            var textToCopy = targetElement.value || targetElement.textContent || targetElement.innerText;
            if (!textToCopy) return;

            navigator.clipboard.writeText(textToCopy).then(function () {
                var original = btn.innerHTML;
                btn.innerHTML = '✅ Copied!';
                setTimeout(function () { btn.innerHTML = original; }, 2000);
            }).catch(function () {
                // Fallback
                if (targetElement.select) {
                    targetElement.select();
                    document.execCommand('copy');
                } else {
                    // For non-input elements, create temporary textarea
                    var tempTextarea = document.createElement('textarea');
                    tempTextarea.value = textToCopy;
                    tempTextarea.style.position = 'fixed';
                    tempTextarea.style.opacity = '0';
                    document.body.appendChild(tempTextarea);
                    tempTextarea.select();
                    document.execCommand('copy');
                    document.body.removeChild(tempTextarea);
                }
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

    // ============================================================
    // Tab 4: Alt Text Generator
    // ============================================================

    // Image source toggle (upload vs URL)
    var imageSourceUpload = document.getElementById('image_source_upload');
    var imageSourceUrl = document.getElementById('image_source_url');
    var uploadSection = document.getElementById('image_upload_section');
    var urlSection = document.getElementById('image_url_section');
    var imageFileInput = document.getElementById('image_file');
    var imageUrlInput = document.getElementById('image_url');
    var imagePreviewContainer = document.getElementById('image_preview_container');
    var imagePreview = document.getElementById('image_preview');

    if (imageSourceUpload && imageSourceUrl) {
        imageSourceUpload.addEventListener('change', function () {
            if (this.checked) {
                uploadSection.style.display = 'block';
                urlSection.style.display = 'none';
                imageUrlInput.value = '';
            }
        });

        imageSourceUrl.addEventListener('change', function () {
            if (this.checked) {
                uploadSection.style.display = 'none';
                urlSection.style.display = 'block';
                imageFileInput.value = '';
            }
        });
    }

    // File input change handler - show preview
    if (imageFileInput) {
        imageFileInput.addEventListener('change', function (e) {
            var file = e.target.files[0];
            if (file) {
                // Validate file size
                if (file.size > 5242880) { // 5MB
                    showError('error-message-alttext', 'Image file is too large. Maximum size is 5MB.');
                    imageFileInput.value = '';
                    imagePreviewContainer.style.display = 'none';
                    return;
                }

                // Validate file type
                var allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    showError('error-message-alttext', 'Invalid image format. Please upload JPG, PNG, or WebP.');
                    imageFileInput.value = '';
                    imagePreviewContainer.style.display = 'none';
                    return;
                }

                // Show preview
                var reader = new FileReader();
                reader.onload = function (e) {
                    imagePreview.src = e.target.result;
                    imagePreviewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                imagePreviewContainer.style.display = 'none';
            }
        });
    }

    // URL input change handler - show preview
    if (imageUrlInput) {
        imageUrlInput.addEventListener('blur', function () {
            var url = this.value.trim();
            if (url) {
                // Basic URL validation
                try {
                    new URL(url);
                    imagePreview.src = url;
                    imagePreviewContainer.style.display = 'block';

                    // Handle image load error
                    imagePreview.onerror = function () {
                        showError('error-message-alttext', 'Failed to load image from URL. Please check the URL.');
                        imagePreviewContainer.style.display = 'none';
                    };
                } catch (err) {
                    showError('error-message-alttext', 'Invalid URL format.');
                    imagePreviewContainer.style.display = 'none';
                }
            } else {
                imagePreviewContainer.style.display = 'none';
            }
        });
    }

    // Alt text form submission
    var alttextForm = document.getElementById('alttext-form');
    if (alttextForm) {
        alttextForm.addEventListener('submit', function (e) {
            e.preventDefault();

            var btn = document.getElementById('alttext-generate-btn');
            hideError('error-message-alttext');

            // Get form values
            var propertyType = document.getElementById('alttext_property_type').value;
            var location = document.getElementById('alttext_location').value;
            var city = document.getElementById('alttext_city').value;
            var imageSource = document.querySelector('input[name="image_source"]:checked').value;

            // Validate required fields
            if (!propertyType || !location || !city) {
                showError('error-message-alttext', 'Please fill in all required fields.');
                return;
            }

            // Validate image source
            if (imageSource === 'upload' && !imageFileInput.files[0]) {
                showError('error-message-alttext', 'Please upload an image file.');
                return;
            }

            if (imageSource === 'url' && !imageUrlInput.value.trim()) {
                showError('error-message-alttext', 'Please provide an image URL.');
                return;
            }

            btn.disabled = true;
            btn.textContent = 'Generating...';

            // Prepare form data
            var formData = new FormData();
            formData.append('property_type', propertyType);
            formData.append('location', location);
            formData.append('city', city);
            formData.append('image_source', imageSource);
            formData.append(window.CSRF_TOKEN_NAME, window.CSRF_TOKEN_HASH);

            if (imageSource === 'upload') {
                formData.append('image_file', imageFileInput.files[0]);
            } else {
                formData.append('image_url', imageUrlInput.value.trim());
            }

            // Submit to API
            fetch(window.BASE_URL + 'tools/alttext', {
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
                    window.location.href = window.BASE_URL + 'login';
                    return Promise.reject(new Error('Session expired'));
                }

                // Update CSRF token if returned in response body
                if (data.csrf_token) {
                    window.CSRF_TOKEN_HASH = data.csrf_token;
                }

                if (!data.success) {
                    showError('error-message-alttext', data.error);
                    return;
                }

                // Display the 3 alt text options
                var options = data.alt_text_options;
                for (var i = 0; i < 3; i++) {
                    var optionNum = i + 1;
                    var card = document.querySelector('.alttext-option-card[data-option="' + optionNum + '"]');
                    var content = document.getElementById('alttext_content_' + optionNum);
                    var count = document.getElementById('alttext_count_' + optionNum);

                    if (card && content && options[i]) {
                        content.textContent = options[i];
                        count.textContent = options[i].length;
                        card.style.display = 'block';
                    }
                }

                // Clear any previous selection
                document.querySelectorAll('input[name="selected_alttext"]').forEach(function (radio) {
                    radio.checked = false;
                });

                // Clear translation boxes
                document.querySelectorAll('#alttext .translation-box .output').forEach(function (textarea) {
                    textarea.value = '';
                });
                document.querySelectorAll('#alttext .translation-box .copy-btn').forEach(function (btn) {
                    btn.style.display = 'none';
                });

            }).catch(function (err) {
                showError('error-message-alttext', err.message || 'An error occurred. Please try again.');
            }).finally(function () {
                btn.disabled = false;
                btn.textContent = 'Generate Alt Text';
            });
        });
    }

    // Alt text option selection handler
    document.querySelectorAll('.alttext-option-card').forEach(function (card) {
        card.addEventListener('click', function () {
            var optionNum = card.getAttribute('data-option');
            var radio = document.getElementById('alttext_option_' + optionNum);

            if (radio) {
                radio.checked = true;

                // Remove selected styling from all cards
                document.querySelectorAll('.alttext-option-card').forEach(function (c) {
                    c.style.borderColor = '';
                    c.style.backgroundColor = '';
                });

                // Add selected styling to this card
                card.style.borderColor = '#D4AF37';
                card.style.backgroundColor = 'rgba(212, 175, 55, 0.1)';

                // Get the selected alt text
                var selectedText = document.getElementById('alttext_content_' + optionNum).textContent;

                // Clear and translate to all languages
                var boxes = document.querySelectorAll('#alttext .translation-box[data-lang]');
                var promises = [];

                boxes.forEach(function (box) {
                    var lang = box.getAttribute('data-lang');
                    if (lang) {
                        promises.push(translateLanguage(selectedText, lang, 'alttext'));
                    }
                });

                Promise.all(promises).catch(function (err) {
                    showError('error-message-alttext', 'Translation failed: ' + err.message);
                });
            }
        });
    });

    // Also handle radio button click directly
    document.querySelectorAll('input[name="selected_alttext"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (this.checked) {
                var card = this.closest('.alttext-option-card');
                if (card) {
                    card.click();
                }
            }
        });
    });
});
