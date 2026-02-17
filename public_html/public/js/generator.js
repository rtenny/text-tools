/**
 * ListingLingo Text Tools - Property Description Generator Tab
 */
function initGenerator() {
    var GENERATE_DEFAULT = '<i data-lucide="sparkles" class="w-4 h-4 mr-2 inline"></i> Generate Description';
    var GENERATE_LOADING = '<i data-lucide="sparkles" class="w-4 h-4 mr-2 inline"></i> Generating...';
    var TRANSLATE_DEFAULT = '<i data-lucide="languages" class="w-4 h-4 mr-2 inline"></i> Translate';
    var TRANSLATE_LOADING = '<i data-lucide="languages" class="w-4 h-4 mr-2 inline"></i> Translating...';

    // Generate description form
    var generatorForm = document.getElementById('generator-form');
    if (generatorForm) {
        generatorForm.addEventListener('submit', function (e) {
            e.preventDefault();

            var btn = document.getElementById('generate-btn');
            hideError('error-message-generator');
            setButtonLoading(btn, GENERATE_LOADING);
            showSpinner('box-en-generator');

            var formFields = {
                property_type: document.getElementById('property_type').value,
                location: document.getElementById('location').value,
                bedrooms: document.getElementById('bedrooms').value,
                bathrooms: document.getElementById('bathrooms').value,
                living_area: document.getElementById('living_area').value,
                plot_size: document.getElementById('plot_size').value || '0',
                feature_list: (function () {
                    var checked = [];
                    document.querySelectorAll('.feature-checkbox:checked').forEach(function (cb) {
                        checked.push(cb.value);
                    });
                    return checked.join(', ');
                })(),
                additional_description: document.getElementById('features').value.trim()
            };

            ajaxPost(window.BASE_URL + 'tools/generate', formFields).then(function (data) {
                hideSpinner('box-en-generator');

                if (!data.success) {
                    showError('error-message-generator', data.error);
                    return;
                }

                // Parse title from markdown heading (# Title) or bold (**Title**)
                var fullText = data.description;
                var title = '';
                var description = fullText;
                var titleMatch = fullText.match(/^#+\s+(.+)/m);
                if (!titleMatch) titleMatch = fullText.match(/^\*\*(.+?)\*\*\s*$/m);
                if (titleMatch) {
                    title = titleMatch[1].trim();
                    description = fullText.replace(titleMatch[0], '').trim();
                }

                document.getElementById('output-title-en-generator').value = title;
                if (title) showCopyBtn('output-title-en-generator');
                document.getElementById('output-en-generator').value = description;
                showCopyBtn('output-en-generator');

                // Reveal translate button for user to review/edit first
                document.getElementById('translate-btn-wrapper').style.display = '';
            }).catch(function (err) {
                hideSpinner('box-en-generator');
                showError('error-message-generator', err.message);
            }).finally(function () {
                restoreButton(btn, GENERATE_DEFAULT);
            });
        });
    }

    // Translate button
    var translateBtn = document.getElementById('translate-btn');
    if (translateBtn) {
        translateBtn.addEventListener('click', function () {
            var title = document.getElementById('output-title-en-generator').value.trim();
            var description = document.getElementById('output-en-generator').value.trim();
            if (!description) return;

            setButtonLoading(translateBtn, TRANSLATE_LOADING);

            // Translate description boxes
            var promises = translateBoxes(
                '#generator .translation-box[data-lang]:not([id^="box-title-"])',
                description, 'generator'
            );

            // Translate title boxes separately (different ID pattern)
            if (title) {
                document.querySelectorAll('#generator .translation-box[id^="box-title-"][data-lang]').forEach(function (box) {
                    var lang = box.getAttribute('data-lang');
                    if (lang) promises.push(translateTitle(title, lang));
                });
            }

            Promise.all(promises).catch(function (err) {
                showError('error-message-generator', err.message);
            }).finally(function () {
                restoreButton(translateBtn, TRANSLATE_DEFAULT);
            });
        });
    }
}
