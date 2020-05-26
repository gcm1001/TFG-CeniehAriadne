<?php
 
$tabs = array();
foreach ($elementSets as $key => $elementSet) {
    $tabName = $elementSet->name;
    $tabContent  = '<p class="element-set-description" id="';
    $tabContent .= html_escape(text_to_id($elementSet->name) . '-description') . '">';
    $tabContent .= url_to_link(__($elementSet->description)) . '</p>' . "\n\n";
    $tabContent .= element_set_form($collection_file, $elementSet->name);    
    $tabs[$tabName] = $tabContent;    
}

$tabs = apply_filters('admin_collection_files_form_tabs', $tabs, array('file' => $collection_file));
?>

<!-- Create the sections for the various element sets -->

<ul id="section-nav" class="navigation tabs">
    <?php foreach ($tabs as $tabName => $tabContent): ?>
        <?php if (!empty($tabContent)): // Don't display tabs with no content. '?>
  <li><a href="#<?= html_escape(text_to_id($tabName) . '-metadata'); ?>"><?= html_escape(__($tabName)); ?></a></li>
        <?php endif; ?> 
    <?php endforeach; ?>
</ul>
