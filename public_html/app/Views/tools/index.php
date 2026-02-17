<?= $this->extend('layouts/user') ?>

<?= $this->section('content') ?>

<?php
$langLabels = [
    'de' => 'German',
    'es' => 'Spanish (European)',
];
?>

<!-- Tab Navigation -->
<div class="tabs flex mb-6 border-b border-[#3a3d42]">
    <button class="tab-button active flex items-center" data-tab="generator"><i data-lucide="sparkles" class="w-4 h-4 mr-2"></i> Generator</button>
    <button class="tab-button flex items-center" data-tab="translator"><i data-lucide="globe" class="w-4 h-4 mr-2"></i> Translator</button>
    <button class="tab-button flex items-center" data-tab="rewriter"><i data-lucide="wand-2" class="w-4 h-4 mr-2"></i> Rewriter</button>
    <button class="tab-button flex items-center" data-tab="alttext"><i data-lucide="image" class="w-4 h-4 mr-2"></i> Alt Text</button>
</div>

<!-- Tab 1: Property Description Generator -->
<div id="generator" class="tab-content active">
    <?= view('tools/tabs/generator', ['languages' => $languages, 'langLabels' => $langLabels, 'towns' => $towns]) ?>
</div>

<!-- Tab 2: Multilingual Translator -->
<div id="translator" class="tab-content">
    <?= view('tools/tabs/translator', ['languages' => $languages, 'langLabels' => $langLabels]) ?>
</div>

<!-- Tab 3: Unique Rewriter -->
<div id="rewriter" class="tab-content">
    <?= view('tools/tabs/rewriter', ['languages' => $languages, 'langLabels' => $langLabels]) ?>
</div>

<!-- Tab 4: Image Alt Text Generator -->
<div id="alttext" class="tab-content">
    <?= view('tools/tabs/alttext', ['languages' => $languages, 'langLabels' => $langLabels, 'towns' => $towns]) ?>
</div>

<!-- CSRF Token for AJAX -->
<script>
    window.CSRF_TOKEN_NAME = '<?= csrf_token() ?>';
    window.CSRF_TOKEN_HASH = '<?= csrf_hash() ?>';
    window.BASE_URL = '<?= base_url() ?>';
</script>

<?= $this->endSection() ?>
