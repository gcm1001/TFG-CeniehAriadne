<?= '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<kml xmlns="http://earth.google.com/kml/2.0">
    <Document>
        <name>Omeka Items KML</name>
        <?php /* Here is the styling for the balloon that appears on the map */ ?>
        <Style id="item-info-balloon">
            <BalloonStyle>
                <text><![CDATA[
                    <div class="geolocation_balloon">
                        <div class="geolocation_balloon_title">$[namewithlink]</div>
                        <div class="geolocation_balloon_thumbnail">$[description]</div>
                        <p class="geolocation_balloon_description">$[Snippet]</p>
                    </div>
                ]]></text>
            </BalloonStyle>
        </Style>
        <?php
        foreach(loop('item') as $item):        
        $location = $locations[$item->id];
        ?>
        <Placemark>
            <name><![CDATA[<?= html_escape(metadata('item', array('Dublin Core', 'Title')));?>]]></name>
            <namewithlink><![CDATA[<?= link_to_item(html_escape(metadata('item' , array('Dublin Core', 'Title'))), array('class' => 'view-item')); ?>]]></namewithlink>
            <Snippet maxLines="2"><![CDATA[<?= html_escape(metadata('item', array('Dublin Core', 'Description'), array('snippet' => 150)));
            ?>]]></Snippet>    
            <description><![CDATA[<?php 
            // @since 3/26/08: movies do not display properly on the map in IE6, 
            // so can't use display_files(). Description field contains the HTML 
            // for displaying the first file (if possible).
            if (metadata($item, 'has thumbnail')): ?>
                <?= link_to_item(item_image('thumbnail'), array('class' => 'view-item')); ?>            
            <?php endif; ?>
            ?>]]></description>
            <Point>
                <coordinates><?= html_escape($location['longitude']); ?>,<?= html_escape($location['latitude']); ?></coordinates>
            </Point>
            <?php if ($location['address']): ?>
            <address><![CDATA[<?= html_escape($location['address']); ?>]]></address>
            <?php endif; ?>
        </Placemark>
        <?php endforeach; ?>
    </Document>
</kml>
