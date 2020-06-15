<?php 
queue_css_file('geolocation-items-map');
    
$title = __("Browse Items on the Map").' (' . html_escape($totalItems).' '.__('total').')';
?>
<?= head(array('title' => $title)); ?>
<?= item_search_filters(); ?>
<?= pagination_links(); ?>


<div id="geolocation-browse">
    <?=  $this->geolocationMapBrowse('map_browse', array('list' => 'map-links', 'params' => $params)); ?>
  <div id="map-links"><h2><?= __('Find An Item on the Map'); ?></h2></div>
</div>

<div id="search_block">
    <?=  items_search_form(array('id'=>'search'), filter_input(INPUT_SERVER,'REQUEST_URI')); ?>
</div><!-- end search_block -->

<?= foot(); ?>
