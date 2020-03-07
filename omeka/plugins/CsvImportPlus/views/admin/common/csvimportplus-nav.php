<nav id="section-nav" class="navigation vertical">
<?php
    $navArray = array(
        array(
            'label' => 'Import',
            'action' => 'index',
            'module' => 'csv-import-plus',
        ),
        array(
            'label' => 'Status',
            'action' => 'browse',
            'module' => 'csv-import-plus',
        ),
    );
    echo nav($navArray, 'admin_navigation_settings');
?>
</nav>
