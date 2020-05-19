<?php
$center = js_escape($center);
$options = $this->geolocationMapOptions($options);
?>

<input type="hidden" name="geolocation[latitude]" value="<?= htmlspecialchars($lat); ?>">
<input type="hidden" name="geolocation[longitude]" value="<?= htmlspecialchars($lng); ?>">
<input type="hidden" name="geolocation[width]" value="<?= htmlspecialchars($width); ?>">
<input type="hidden" name="geolocation[height]" value="<?= htmlspecialchars($height); ?>">
<input type="hidden" name="geolocation[zoom_level]" value="<?= htmlspecialchars($zoom); ?>">
<input type="hidden" name="geolocation[map_type]" value="Leaflet">

<div class="field">
    <div id="location_form" class="two columns alpha">
        <label><?= htmlspecialchars($label); ?></label>
    </div>
    <div class="inputs five columns omega">
        <input type="text" name="geolocation[address]" id="geolocation_address" value="<?= htmlspecialchars($address); ?>">
        <button type="button" name="geolocation_find_location_by_address" id="geolocation_find_location_by_address"><?= htmlspecialchars(__('Find')); ?></button>
    </div>
</div>
<div id="omeka-map-form" class="geolocation-map"></div>

<?= js_tag('geocoder');?>
<?php $geocoder = json_encode(get_option('geolocation_geocoder')); ?>
<script type="text/javascript">
var omekaGeolocationForm = new OmekaMapForm('omeka-map-form', <?= htmlspecialchars($center); ?>, <?= htmlspecialchars($options); ?>);
var geocoder = new OmekaGeocoder(<?= htmlspecialchars($geocoder); ?>);
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
});
</script>
