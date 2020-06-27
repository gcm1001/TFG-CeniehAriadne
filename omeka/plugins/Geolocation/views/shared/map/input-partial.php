<?php
$center = js_escape($center);
$options = $this->geolocationMapOptions($options);
?>

<input type="hidden" name="geolocation[latitude]" value="<?= html_escape($lat); ?>">
<input type="hidden" name="geolocation[longitude]" value="<?= html_escape($lng); ?>">
<input type="hidden" name="geolocation[width]" value="<?= html_escape($width); ?>">
<input type="hidden" name="geolocation[height]" value="<?= html_escape($height); ?>">
<input type="hidden" name="geolocation[zoom_level]" value="<?= html_escape($zoom); ?>">
<input type="hidden" name="geolocation[map_type]" value="Leaflet">

<div class="field">
    <div id="location_form" class="two columns alpha">
        <label><?= html_escape($label); ?></label>
    </div>
    <div class="inputs five columns omega">
        <input type="text" name="geolocation[address]" id="geolocation_address" value="<?= html_escape($address); ?>">
        <button type="button" name="geolocation_find_location_by_address" id="geolocation_find_location_by_address"><?= __('Find'); ?></button>
    </div>
</div>
<div id="omeka-map-form" class="geolocation-map"></div>

<?= js_tag('geocoder');?>
<?php $geocoder = json_encode(get_option('geolocation_geocoder')); ?>
<script type="text/javascript">
var omekaGeolocationForm = new OmekaMapForm('omeka-map-form', <?= $center; ?>, <?= $options; ?>);
var geocoder = new OmekaGeocoder(<?= $geocoder; ?>);
jQuery(document).on('omeka:tabselected', function () {
    omekaGeolocationForm.resize();
});

jQuery(document).ready(function () {
    // Make the Find By Address button lookup the geocode of an address and add a marker.
    jQuery('#geolocation_find_location_by_address').on('click', function (event) {
        event.preventDefault();
        var address = jQuery('#geolocation_address').val();
        geocoder.geocode(address).then(function (coords) {
            var marker = omekaGeolocationForm.setMarker(L.latLng(coords));
            if (marker === false) {
                jQuery('#geolocation_address').val('');
                jQuery('#geolocation_address').focus();
            }
        }, function () {
            alert('Error: "' + address + '" was not found!');
        });
    });

    // Make the return key in the geolocation address input box click the button to find the address.
    jQuery('#geolocation_address').on('keydown', function (event) {
        if (event.which == 13) {
            event.preventDefault();
            jQuery('#geolocation_find_location_by_address').click();
        }
    });
    
    check();
    
    jQuery('#section-nav li a').click( function(){
        check();
    });
    
    function check(){
        var href = jQuery('a.active').attr("href");
        if(href == "#dublin-core-metadata"){
          <?php if(get_option('geolocation_sync_spatial')): ?>  
              alert("'Spatial Coverage' synchronization ACTIVATED: If you enter coordinates in the 'Spatial Coverage' element will be overwritten.");
          <?php endif; ?>
        };
        if(href == "#map-metadata"){
          <?php if(get_option('geolocation_sync_spatial_rev')): ?>
            alert("Reverse 'Spatial Coverage' synchronization ACTIVATED: Map changes will not be saved if the 'Spatial Coverage' element is filled.");
          <?php elseif(get_option('geolocation_sync_spatial')): ?>  
            alert("'Spatial Coverage' synchronization ACTIVATED: If you set a location, the 'Spatial Coverage' element will be updated with the coordinates of that location.");        
          <?php endif; ?>
        };
    };
});
</script>


