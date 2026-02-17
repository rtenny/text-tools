/**
 * ListingLingo Text Tools - Alt Text Generator Tab
 */
function initAltText() {
    var GENERATE_DEFAULT = '<i data-lucide="sparkles" class="w-4 h-4 mr-2 inline"></i> Generate Alt Text';
    var GENERATE_LOADING = '<i data-lucide="sparkles" class="w-4 h-4 mr-2 inline"></i> Generating...';
    var TRANSLATE_DEFAULT = '<i data-lucide="languages" class="w-4 h-4 mr-2 inline"></i> Translate';
    var TRANSLATE_LOADING = '<i data-lucide="languages" class="w-4 h-4 mr-2 inline"></i> Translating...';

    var imageSourceUpload = document.getElementById('image_source_upload');
    var imageSourceUrl = document.getElementById('image_source_url');
    var uploadSection = document.getElementById('image_upload_section');
    var urlSection = document.getElementById('image_url_section');
    var imageFileInput = document.getElementById('image_file');
    var imageUrlInput = document.getElementById('image_url');
    var imagePreviewContainer = document.getElementById('image_preview_container');
    var imagePreview = document.getElementById('image_preview');

    // Image source toggle
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

    // File upload preview + validation
    if (imageFileInput) {
        imageFileInput.addEventListener('change', function (e) {
            var file = e.target.files[0];
            if (file) {
                if (file.size > MAX_IMAGE_SIZE) {
                    showError('error-message-alttext', 'Image file is too large. Maximum size is 5MB.');
                    imageFileInput.value = '';
                    imagePreviewContainer.style.display = 'none';
                    return;
                }
                if (!ALLOWED_IMAGE_TYPES.includes(file.type)) {
                    showError('error-message-alttext', 'Invalid image format. Please upload JPG, PNG, or WebP.');
                    imageFileInput.value = '';
                    imagePreviewContainer.style.display = 'none';
                    return;
                }
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

    // URL input preview
    if (imageUrlInput) {
        imageUrlInput.addEventListener('blur', function () {
            var url = this.value.trim();
            if (url) {
                try {
                    new URL(url);
                    imagePreview.src = url;
                    imagePreviewContainer.style.display = 'block';
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

    // Form submission (uses fetch directly to support file uploads)
    var alttextForm = document.getElementById('alttext-form');
    if (alttextForm) {
        alttextForm.addEventListener('submit', function (e) {
            e.preventDefault();

            var btn = document.getElementById('alttext-generate-btn');
            hideError('error-message-alttext');

            var propertyType = document.getElementById('alttext_property_type').value;
            var location = document.getElementById('alttext_location').value;
            var imageSource = document.querySelector('input[name="image_source"]:checked').value;

            if (!propertyType || !location) {
                showError('error-message-alttext', 'Please fill in all required fields.');
                return;
            }
            if (imageSource === 'upload' && !imageFileInput.files[0]) {
                showError('error-message-alttext', 'Please upload an image file.');
                return;
            }
            if (imageSource === 'url' && !imageUrlInput.value.trim()) {
                showError('error-message-alttext', 'Please provide an image URL.');
                return;
            }

            setButtonLoading(btn, GENERATE_LOADING);

            var formData = new FormData();
            formData.append('property_type', propertyType);
            formData.append('location', location);
            formData.append('image_source', imageSource);
            formData.append(window.CSRF_TOKEN_NAME, window.CSRF_TOKEN_HASH);

            if (imageSource === 'upload') {
                formData.append('image_file', imageFileInput.files[0]);
            } else {
                formData.append('image_url', imageUrlInput.value.trim());
            }

            fetch(window.BASE_URL + 'tools/alttext', {
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

                if (!data.success) {
                    showError('error-message-alttext', data.error);
                    return;
                }

                // Display generated options
                var options = data.alt_text_options;
                for (var i = 0; i < options.length; i++) {
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

                // Reset selection state
                document.querySelectorAll('input[name="selected_alttext"]').forEach(function (radio) {
                    radio.checked = false;
                });
                document.querySelectorAll('.alttext-option-card').forEach(function (c) {
                    c.classList.remove('alttext-option-selected');
                });

                // Clear outputs and hide translate button
                document.querySelectorAll('#alttext .translation-box .output').forEach(function (el) {
                    el.value = '';
                });
                document.querySelectorAll('#alttext .translation-box .copy-btn').forEach(function (el) {
                    el.style.display = 'none';
                });
                document.getElementById('alttext-translate-btn-wrapper').style.display = 'none';

            }).catch(function (err) {
                showError('error-message-alttext', err.message || 'An error occurred. Please try again.');
            }).finally(function () {
                restoreButton(btn, GENERATE_DEFAULT);
            });
        });
    }

    // Option card selection
    document.querySelectorAll('.alttext-option-card').forEach(function (card) {
        card.addEventListener('click', function () {
            var optionNum = card.getAttribute('data-option');
            var radio = document.getElementById('alttext_option_' + optionNum);

            if (radio) {
                radio.checked = true;

                document.querySelectorAll('.alttext-option-card').forEach(function (c) {
                    c.classList.remove('alttext-option-selected');
                });
                card.classList.add('alttext-option-selected');

                var selectedText = document.getElementById('alttext_content_' + optionNum).textContent;

                var englishTextarea = document.getElementById('output-en-alttext');
                if (englishTextarea) {
                    englishTextarea.value = selectedText;
                    showCopyBtn('output-en-alttext');
                }

                // Clear translation boxes and reveal translate button
                document.querySelectorAll('#alttext .translation-box[data-lang] .output').forEach(function (el) {
                    el.value = '';
                });
                document.querySelectorAll('#alttext .translation-box[data-lang] .copy-btn').forEach(function (el) {
                    el.style.display = 'none';
                });
                document.getElementById('alttext-translate-btn-wrapper').style.display = '';
            }
        });
    });

    // Radio button click syncs to card click
    document.querySelectorAll('input[name="selected_alttext"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (this.checked) {
                var card = this.closest('.alttext-option-card');
                if (card) card.click();
            }
        });
    });

    // Translate button
    var alttextTranslateBtn = document.getElementById('alttext-translate-btn');
    if (alttextTranslateBtn) {
        alttextTranslateBtn.addEventListener('click', function () {
            var text = document.getElementById('output-en-alttext').value.trim();
            if (!text) return;

            setButtonLoading(alttextTranslateBtn, TRANSLATE_LOADING);

            var promises = translateBoxes('#alttext .translation-box[data-lang]', text, 'alttext');

            Promise.all(promises).catch(function (err) {
                showError('error-message-alttext', 'Translation failed: ' + err.message);
            }).finally(function () {
                restoreButton(alttextTranslateBtn, TRANSLATE_DEFAULT);
            });
        });
    }
}
