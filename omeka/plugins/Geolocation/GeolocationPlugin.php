<?php

class GeolocationPlugin extends Omeka_Plugin_AbstractPlugin
{
    const DEFAULT_LOCATIONS_PER_PAGE = 10;
    const DEFAULT_BASEMAP = 'CartoDB.Voyager';
    const DEFAULT_GEOCODER = 'nominatim';
    
    protected $_hooks = array(
        'install',
        'uninstall',
        'upgrade',
        'config_form',
        'config',
        'define_acl',
        'define_routes',
        'after_save_item',
        'admin_items_show_sidebar',
        'public_items_show',
        'admin_items_search',
        'public_items_search',
        'items_browse_sql',
        'public_head',
        'admin_head',
        'initialize',
        'contribution_type_form',
        'contribution_save_form'
    );

    protected $_filters = array(
        'admin_navigation_main',
        'public_navigation_main',
        'response_contexts',
        'action_contexts',
        'admin_items_form_tabs',
        'public_navigation_items',
        'api_resources',
        'api_extend_items',
        'exhibit_layouts',
        'api_import_omeka_adapters',
        'item_search_filters'
    );

    public function hookInstall()
    {
        $database = get_db();
        $sql = "
        CREATE TABLE IF NOT EXISTS `$database->Location` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `item_id` BIGINT UNSIGNED NOT NULL ,
        `latitude` DOUBLE NOT NULL ,
        `longitude` DOUBLE NOT NULL ,
        `zoom_level` INT NOT NULL ,
        `map_type` VARCHAR( 255 ) NOT NULL ,
        `address` TEXT NOT NULL ,
        INDEX (`item_id`)) ENGINE = InnoDB";
        $database->query($sql);

        $sqlBox = "
        CREATE TABLE IF NOT EXISTS `$database->BoxLocation` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `item_id` BIGINT UNSIGNED NOT NULL ,
        `latitude` DOUBLE NOT NULL ,
        `longitude` DOUBLE NOT NULL ,
        `width` DOUBLE NOT NULL ,
        `height` DOUBLE NOT NULL ,
        `zoom_level` INT NOT NULL ,
        `map_type` VARCHAR( 255 ) NOT NULL ,
        `address` TEXT NOT NULL ,
        INDEX (`item_id`)) ENGINE = InnoDB";
        $database->query($sqlBox);

        set_option('geolocation_default_latitude', '55.673730');
        set_option('geolocation_default_longitude', '12.561809');
        set_option('geolocation_default_zoom_level', '3');
        set_option('geolocation_per_page', self::DEFAULT_LOCATIONS_PER_PAGE);
        set_option('geolocation_add_map_to_contribution_form', '0');
        set_option('geolocation_default_radius', 10);
        set_option('geolocation_use_metric_distances', '0');
        set_option('geolocation_draw','1');
        set_option('geolocation_sync_spatial','1');
        set_option('geolocation_basemap', self::DEFAULT_BASEMAP);
        set_option('geolocation_geocoder', self::DEFAULT_GEOCODER);
    }

    public function hookUninstall()
    {
        // Delete the plugin options
        delete_option('geolocation_default_latitude');
        delete_option('geolocation_default_longitude');
        delete_option('geolocation_default_zoom_level');
        delete_option('geolocation_per_page');
        delete_option('geolocation_add_map_to_contribution_form');
        delete_option('geolocation_use_metric_distances');
        delete_option('geolocation_link_to_nav');
        delete_option('geolocation_default_radius');
        delete_option('geolocation_basemap');
        delete_option('geolocation_geocoder');
        delete_option('geolocation_auto_fit_browse');
        delete_option('geolocation_mapbox_access_token');
        delete_option('geolocation_mapbox_map_id');
        delete_option('geolocation_cluster');
        delete_option('geolocation_draw');
        delete_option('geolocation_sync_spatial_rev');
        delete_option('geolocation_sync_spatial');
        // This is for older versions of Geolocation, which used to store a Google Map API key.
        delete_option('geolocation_gmaps_key');
        // Drop the Location table
        $database = get_db();
        $database->query("DROP TABLE IF EXISTS `$database->Location`");
        $database->query("DROP TABLE IF EXISTS `$database->BoxLocation`");

    }
    
    public function hookUpgrade($args)
    {
        if (version_compare($args['old_version'], '1.1', '<')) {
            // If necessary, upgrade the plugin options
            // Check for old plugin options, and if necessary, transfer to new options
            $options = array('default_latitude', 'default_longitude', 'default_zoom_level', 'per_page');
            foreach($options as $option) {
                $oldOptionValue = get_option('geo_' . $option);
                if ($oldOptionValue != '') {
                    set_option('geolocation_' . $option, $oldOptionValue);
                    delete_option('geo_' . $option);
                }
            }
            delete_option('geo_gmaps_key');
        }
        if (version_compare($args['old_version'], '2.2.3', '<')) {
            set_option('geolocation_default_radius', 10);
        }
        if (version_compare($args['old_version'], '3.0', '<')) {
            delete_option('geolocation_api_key');
            delete_option('geolocation_map_type');
            set_option('geolocation_basemap', self::DEFAULT_BASEMAP);
        }
        if (version_compare($args['old_version'], '3.1', '<')) {
            set_option('geolocation_geocoder', self::DEFAULT_GEOCODER);

            if (get_option('geolocation_basemap') == 'OpenStreetMap.BlackAndWhite') {
                set_option('geolocation_basemap', self::DEFAULT_BASEMAP);
            }
        }
        if (version_compare($args['old_version'], '3.2', '<')) {
            $newMapboxIds = array(
                'mapbox.streets' => 'mapbox/streets-v11',
                'mapbox.outdoors' => 'mapbox/outdoors-v11',
                'mapbox.light' => 'mapbox/light-v10',
                'mapbox.dark' => 'mapbox/dark-v10',
                'mapbox.satellite' => 'mapbox/satellite-v9',
                'mapbox.streets-satellite' => 'mapbox/satellite-streets-v11',
            );

            $oldMapboxId = get_option('geolocation_mapbox_map_id');
            if ($oldMapboxId && isset($newMapboxIds[$oldMapboxId])) {
                set_option('geolocation_mapbox_map_id', $newMapboxIds[$oldMapboxId]);
            }
        }
    }

    /**
     * Shows plugin configuration page.
     */
    public function hookConfigForm($args)
    {
        include 'config_form.php';
    }

    /**
     * Saves plugin configuration page.
     *
     * @param array Options set in the config form.
     */
    public function hookConfig($args)
    {
        $post = $args['post'];
        // Use the form to set a bunch of default options in the db
        set_option('geolocation_default_latitude', $post['default_latitude']);
        set_option('geolocation_default_longitude', $post['default_longitude']);
        set_option('geolocation_default_zoom_level', $post['default_zoom_level']);
        set_option('geolocation_item_map_width', $post['item_map_width']);
        set_option('geolocation_item_map_height', $post['item_map_height']);
        $perPage = (int)$post['per_page'];
        if ($perPage <= 0) {
            $perPage = self::DEFAULT_LOCATIONS_PER_PAGE;
        }
        set_option('geolocation_per_page', $perPage);
        set_option('geolocation_add_map_to_contribution_form', $post['geolocation_add_map_to_contribution_form']);
        set_option('geolocation_link_to_nav', $post['geolocation_link_to_nav']);
        set_option('geolocation_default_radius', $post['geolocation_default_radius']);
        set_option('geolocation_use_metric_distances', $post['geolocation_use_metric_distances']);
        set_option('geolocation_basemap', $post['basemap']);
        set_option('geolocation_auto_fit_browse', $post['auto_fit_browse']);
        set_option('geolocation_mapbox_access_token', $post['mapbox_access_token']);
        set_option('geolocation_mapbox_map_id', $post['mapbox_map_id']);
        set_option('geolocation_cluster', $post['cluster']);
        set_option('geolocation_draw', $post['draw']);
        set_option('geolocation_sync_spatial', $post['geolocation_sync_spatial']);
        set_option('geolocation_sync_spatial_rev', $post['geolocation_sync_spatial_rev']);
        set_option('geolocation_geocoder', $post['geocoder']);
    }

    public function hookDefineAcl($args)
    {
        $acl = $args['acl'];
        $acl->addResource('Locations');
        $acl->allow(null, 'Locations');
    }

    public function hookDefineRoutes($args)
    {
        $router = $args['router'];
        $mapRoute = new Zend_Controller_Router_Route('items/map',
                        array('controller' => 'map',
                                'action'     => 'browse',
                                'module'     => 'geolocation'));
        $router->addRoute('items_map', $mapRoute);

        // Trying to make the route look like a KML file so google will eat it.
        // @todo Include page parameter if this works.
        $kmlRoute = new Zend_Controller_Router_Route_Regex('geolocation/map\.kml',
                        array('controller' => 'map',
                                'action' => 'browse',
                                'module' => 'geolocation',
                                'output' => 'kml'));
        $router->addRoute('map_kml', $kmlRoute);
    }

    public function hookAdminHead($args)
    {
        $this->_head();
    }

    public function hookPublicHead($args)
    {
        $this->_head();
    }

    private function _head()
    {
        $pluginLoader = Zend_Registry::get('plugin_loader');
        $geolocation = $pluginLoader->getPlugin('Geolocation');
        $version = $geolocation->getIniVersion();
        queue_css_file('leaflet/leaflet', null, null, 'javascripts', $version);
        queue_css_file('geolocation-marker', null, null, 'css', $version);
        queue_js_file(array('leaflet/leaflet', 'leaflet/leaflet-providers', 'map'), 'javascripts', array(), $version);

        if (get_option('geolocation_cluster')) {
            queue_css_file(array('MarkerCluster', 'MarkerCluster.Default'), null, null,
               'javascripts/leaflet-markercluster', $version);
            queue_js_file('leaflet-markercluster/leaflet.markercluster', 'javascripts', array(), $version);
        }
        if (get_option('geolocation_draw')) {
            queue_js_file(array('Leaflet.draw/src/Leaflet.draw','Leaflet.draw/src/Leaflet.Draw.Event'));
            queue_css_file('Leaflet.draw/src/leaflet.draw', null, null,'javascripts',null);
            queue_js_file(array('Leaflet.draw/src/Toolbar', 'Leaflet.draw/src/Tooltip'),'javascripts',null,null);
            queue_js_file(array('Leaflet.draw/src/ext/GeometryUtil','Leaflet.draw/src/ext/LatLngUtil','Leaflet.draw/src/ext/LineUtil.Intersect', 'Leaflet.draw/src/ext/Polygon.Intersect','Leaflet.draw/src/ext/Polyline.Intersect','Leaflet.draw/src/ext/TouchEvents'),'javascripts',null,null);
            queue_js_file(array('Leaflet.draw/src/draw/DrawToolbar','Leaflet.draw/src/draw/handler/Draw.Feature','Leaflet.draw/src/draw/handler/Draw.SimpleShape','Leaflet.draw/src/draw/handler/Draw.Polyline','Leaflet.draw/src/draw/handler/Draw.Marker','Leaflet.draw/src/draw/handler/Draw.Circle','Leaflet.draw/src/draw/handler/Draw.CircleMarker','Leaflet.draw/src/draw/handler/Draw.Polygon','Leaflet.draw/src/draw/handler/Draw.Rectangle'));
            queue_js_file(array('Leaflet.draw/src/edit/EditToolbar','Leaflet.draw/src/edit/handler/EditToolbar.Edit','Leaflet.draw/src/edit/handler/EditToolbar.Delete'));
            queue_js_file('Leaflet.draw/src/Control.Draw');
            queue_js_file(array('Leaflet.draw/src/edit/handler/Edit.Poly','Leaflet.draw/src/edit/handler/Edit.SimpleShape','Leaflet.draw/src/edit/handler/Edit.Rectangle', 'Leaflet.draw/src/edit/handler/Edit.Marker','Leaflet.draw/src/edit/handler/Edit.CircleMarker','Leaflet.draw/src/edit/handler/Edit.Circle'));
        }
    }

    /**
     *  Update the map with a new Location based on item's Dublin Core "Spatial Coverage" metadata.
     * 
     * @param type $item
     * @return boolean
     */
    private function synchronizeSpatialCoverage_Map($item) {
        $spatialtext = metadata($item, array('Dublin Core', 'Spatial Coverage'));
        $location = $this->_db->getTable('Location')->findLocationByItem($item, true);
        $boxlocation = $this->_db->getTable('BoxLocation')->findLocationByItem($item, true);

        if(!empty($spatialtext)){
            $nCoords = count(explode(',',$spatialtext));
            if ($nCoords == 4) {
                list($minlat,$minlon,$maxlat,$maxlon) = explode(',',$spatialtext); 
                if(is_numeric($minlat) && is_numeric($maxlat) && is_numeric($minlon) && is_numeric($maxlon)){
                    if($location) $location->delete();
                    if (!$boxlocation) {
                        $boxlocation = new BoxLocation; 
                        $boxlocation->item_id = $item->id;
                    }
                    $boxlocation->latitude = $maxlat;
                    $boxlocation->longitude = $minlon;
                    $boxlocation->width = abs($maxlon - $minlon);
                    $boxlocation->height = abs($maxlat - $minlat);
                    $boxlocation->zoom_level = '5';
                    $boxlocation->save();
                    return true;
                }
            } else if ($nCoords == 2){ 
                list($lat,$lon) = explode(',',$spatialtext); 
                if(is_numeric($lat) && is_numeric($lon)){
                    if($boxlocation) $boxlocation->delete();
                    if (!$location) {
                        $location = new Location; 
                        $location->item_id = $item->id; 
                    }
                    
                    $location->latitude = $lat;
                    $location->longitude = $lon;
                    $location->zoom_level = '5';

                    $location->save();
                    return true;
                }
            } 
    } else {
        if($location){
        	$location->delete();
        }
        if($boxlocation){
            $boxlocation->delete();
        }
    }
        return;
    }

    /**
     * Update the item's Dublin Core "Spatial Coverage" metadata with the lat/long and address provided from Geolocation.
     * 
     * @param type $item
     */
    private function synchronizeMap_SpatialCoverage($item){
        // obtenemos el elemento 'Spatial Coverage' de la tabla 'Element' alojada en la base de datos
        $elementTable = $this->_db->getTable('Element');
        $spatialElement = $elementTable->findByElementSetNameAndElementName('Dublin Core', 'Spatial Coverage');
        // obtenemos de la tabla 'Location' las características asociadas a la localización del item actual
        $location = $this->_db->getTable('Location')->findLocationByItem($item, true);
        $boxlocation = $this->_db->getTable('BoxLocation')->findLocationByItem($item, true);
        $this->_deleteExistingCoords($item);
        
        if ($location) { //si existe una localización (Point)
            $lon = $location->longitude;
            if ((-100 < $lon) && ($lon < 100)) { //si la parte entera de la longitud está entre -100 y 100
                $formatoSalida = "%'.0+".(strlen(sprintf('%+f', $lon)) + 1)."f"; //añado 0 para rellenar
            } else {
                $formatoSalida = "%+f";
            }
            $latlon = sprintf('%+f',$location->latitude).','.sprintf($formatoSalida, $lon); // creo la cadena (latitud,longitud)
            $item->addTextForElement($spatialElement, $latlon); // añado al elemento 'Spatial Coverage' del item la cadena
        }
        if ($boxlocation) { //si existe una localización (BBox)
            $box_minlon = $boxlocation->longitude;
            $box_maxlat= $boxlocation->latitude;

            if ((-100 < $box_minlon) && ($box_minlon < 100)) { //si la parte entera de la longitud está entre -100 y 100
                $formatoSalida = "%'.0+".(strlen(sprintf('%+f', $box_minlon)) + 1)."f"; // añado 0 para rellenar
            } else {
                $formatoSalida = "%+f";
            }
            $latlonMin = sprintf('%+f',$box_maxlat-$boxlocation->height).','.sprintf($formatoSalida, $box_minlon); // coordenada del punto mínimo
            $latlonMax = sprintf('%+f',$box_maxlat).','.sprintf($formatoSalida, $box_minlon + $boxlocation->width); // coordenada del punto máximo
            $item->addTextForElement($spatialElement, $latlonMin.','.$latlonMax); // añado al elemento 'Spatial Coverage' las coordendas en formato "mincoord;maxcoord"
        }
        $item->saveElementTexts(); //guardo las modificaciones
    }

    /**
     * Delete existing coordinates in the "Spatial Coverage" metadata.
     * 
     * @param type $item
     * @return type
     */
    private function _deleteExistingCoords($item){
        $etexts = $item->getElementTexts('Dublin Core', 'Spatial Coverage');
        if(!$etexts) return;
        foreach ($etexts as $etext){
            $text = $etext->text;
            $coords = explode(',',$text);
            if(count($coords) == 4){
                list($minlat,$minlon,$maxlat,$maxlon) = $coords;
                if(is_numeric($coords[0]) && is_numeric($coords[1]) && 
                        is_numeric($coords[2]) && is_numeric($coords[3])){
                    $etext->delete();
                    continue;
                }
            }else if(count(explode(',',$text)) == 2){
                if(is_numeric($coords[0]) && is_numeric($coords[1])){
                    $etext->delete();
                    continue;
                }
            }
        }
    }
    
    public function hookAfterSaveItem($args) {
    	$item = $args['record'];

        if (!($post = $args['post'])) {
            return;
        }

        // If we don't have the geolocation form on the page, don't do anything!
        if (!isset($post['geolocation'])) {
            return;
        }
        
        $location = $this->_db->getTable('Location')->findLocationByItem($item, true);
        $boxlocation = $this->_db->getTable('BoxLocation')->findLocationByItem($item, true);

        // If we have filled out info for the geolocation, then submit to the db
        $geolocationPost = $post['geolocation'];

        if (!empty($geolocationPost)
            && $geolocationPost['latitude'] != ''
            && $geolocationPost['longitude'] != ''
            && $geolocationPost['width'] == ''
            && $geolocationPost['height'] == ''
        ) {
            if (!$location) {
                $location = new Location;
                $location->item_id = $item->id;
            }
            $location->setPostData($geolocationPost);
            $location->save();
        } else {
            if($location){
                $location->delete();
            }
        }
        if (!empty($geolocationPost)
            && $geolocationPost['latitude'] != ''
            && $geolocationPost['longitude'] != ''
            && $geolocationPost['width'] != ''
            && $geolocationPost['height'] != ''
        ) {
            if (!$boxlocation) {
                $boxlocation = new BoxLocation;
                $boxlocation->item_id = $item->id;
            }
            $boxlocation->setPostData($geolocationPost);
            $boxlocation->save();
        } else {
            if($boxlocation){
               $boxlocation->delete();
            } 
        }
        if(get_option('geolocation_sync_spatial')) $this->synchronizeMap_SpatialCoverage($item);
        if(get_option('geolocation_sync_spatial_rev')) $this->synchronizeSpatialCoverage_Map($item);
    }

    public function hookAdminItemsShowSidebar($args)
    {
        $view = $args['view'];
        $item = $args['item'];
        $location = $this->_db->getTable('Location')->findLocationByItem($item, true);

        if ($location) {
            $html = ''
                  . '<div class="geolocation panel">'
                  . '<h4>' . __('Geolocation') . '</h4>'
                  . '<div style="margin: 14px 0">'
                  . $view->geolocationMapSingle($item, '100%', '270px' )
                  . '</div></div>';
            $this->_p_html($html);
        }
    }

    public function hookPublicItemsShow($args)
    {
        $view = $args['view'];
        $item = $args['item'];
        $location = $this->_db->getTable('Location')->findLocationByItem($item, true);

        if ($location) {
            $width = get_option('geolocation_item_map_width') ? get_option('geolocation_item_map_width') : '';
            $height = get_option('geolocation_item_map_height') ? get_option('geolocation_item_map_height') : '300px';
            $html = "<div id='geolocation'>";
            $html .= '<h2>'.__('Geolocation').'</h2>';
            $html .= $view->geolocationMapSingle($item, $width, $height);
            $html .= "</div>";
            $this->_p_html( $html);
        }
    }

    /**
     * Hook to include a form in the admin items search form.
     *
     * @internal Themed partial should go to "my_theme/map".
     */
    public function hookAdminItemsSearch($args)
    {
        $view = $args['view'];
        $this->_p_html( $view->partial('map/advanced-search-partial.php'));
    }

    /**
     * Hook to include a form in the admin items search form.
     *
     * @internal Themed partial should go to "my_theme/map".
     */
    public function hookPublicItemsSearch($args)
    {
        $view = $args['view'];
        $this->_p_html( $view->partial('map/advanced-search-partial.php'));
    }

    public function hookItemsBrowseSql($args)
    {
        $database = $this->_db;
        $select = $args['select'];
        $alias = $this->_db->getTable('Location')->getTableAlias();

        $isMapped = null;
        if (array_key_exists('geolocation-mapped', $args['params'])
            && $args['params']['geolocation-mapped'] !== ''
        ) {
            $isMapped = (bool) $args['params']['geolocation-mapped'];
        }

        if ($isMapped === true
            || !empty($args['params']['geolocation-address'])
        ) {
            $select->joinInner(
                array($alias => $database->Location),
                "$alias.item_id = items.id",
                array()
            );
        } else if ($isMapped === false) {
            $select->joinLeft(
                array($alias => $database->Location),
                "$alias.item_id = items.id",
                array()
            );
            $select->where("$alias.id IS NULL");
        }
        if (!empty($args['params']['geolocation-address'])) {
            // Get the address, latitude, longitude, and the radius from parameters
            $address = trim($args['params']['geolocation-address']);
            $lat = trim($args['params']['geolocation-latitude']);
            $lng = trim($args['params']['geolocation-longitude']);
            $radius = trim($args['params']['geolocation-radius']);
            // Limit items to those that exist within a geographic radius if an address and radius are provided
            if ($address != ''
                && is_numeric($lat)
                && is_numeric($lng)
                && is_numeric($radius)
            ) {
                // SELECT distance based upon haversine forumula
                if (get_option('geolocation_use_metric_distances')) {
                    $denominator = 111;
                    $earthRadius = 6371;
                } else {
                    $denominator = 69;
                    $earthRadius = 3959;
                }

                $radius = $database->quote($radius, Zend_Db::FLOAT_TYPE);
                $lat = $database->quote($lat, Zend_Db::FLOAT_TYPE);
                $lng = $database->quote($lng, Zend_Db::FLOAT_TYPE);

                $sqlMathExpression =
                    new Zend_Db_Expr(
                        "$earthRadius * ACOS(
                        COS(RADIANS($lat)) *
                        COS(RADIANS(locations.latitude)) *
                        COS(RADIANS($lng) - RADIANS(locations.longitude))
                        +
                        SIN(RADIANS($lat)) *
                        SIN(RADIANS(locations.latitude))
                        ) AS distance");

                $select->columns($sqlMathExpression);

                // WHERE the distance is within radius miles/kilometers of the specified lat & long
                $locationWithinRadius =
                    new Zend_Db_Expr(
                        "(locations.latitude BETWEEN $lat - $radius / $denominator
                            AND $lat + $radius / $denominator)
                            AND
                        (locations.longitude BETWEEN $lng - $radius / $denominator
                            AND $lng + $radius / $denominator)");
                $select->where($locationWithinRadius);

                // Actually use distance calculation.
                //$select->having('distance < radius');

                //ORDER by the closest distances
                $select->order('distance');
            }
        }
    }

    /**
     * Add geolocation search options to filter output.
     *
     * @param array $displayArray
     * @param array $args
     * @return array
     */
    public function filterItemSearchFilters($displayArray, $args)
    {
        $requestArray = $args['request_array'];
        if (!empty($requestArray['geolocation-address']) && !empty($requestArray['geolocation-radius'])) {
            if (get_option('geolocation_use_metric_distances')) {
                $unit = __('kilometers');
            } else {
                $unit = __('miles');
            }
            $displayArray['location'] = __('within %1$s %2$s of "%3$s"',
                $requestArray['geolocation-radius'],
                $unit,
                $requestArray['geolocation-address']
            );
        }
        if (array_key_exists('geolocation-mapped', $requestArray)
            && $requestArray['geolocation-mapped'] !== ''
        ) {
            if ($requestArray['geolocation-mapped']) {
                $displayArray['Geolocation Status'] = __('Only Items with Locations');
            } else {
                $displayArray['Geolocation Status'] = __('Only Items without Locations');
            }
        }
        return $displayArray;
    }

    /**
     * Add the translations.
     */
    public function hookInitialize()
    {
        add_translation_source(dirname(__FILE__) . '/languages');
        add_shortcode( 'geolocation', array($this, 'geolocationShortcode'));
    }

    public function filterAdminNavigationMain($navArray)
    {
        $navArray['Geolocation'] = array('label'=>__('Map'), 'uri'=>url('geolocation/map/browse'));
        return $navArray;
    }

    public function filterPublicNavigationMain($navArray)
    {
        $navArray['Geolocation'] = array('label'=>__('Map'), 'uri'=>url('geolocation/map/browse'));
        return $navArray;
    }

    public function filterResponseContexts($contexts)
    {
        $contexts['kml'] = array('suffix'  => 'kml',
                'headers' => array('Content-Type' => 'text/xml'));
        return $contexts;
    }

    public function filterActionContexts($contexts, $args)
    {
        $controller = $args['controller'];
        if ($controller instanceof Geolocation_MapController) {
            $contexts['browse'] = array('kml');
        }
        return $contexts;
    }

    public function filterAdminItemsFormTabs($tabs, $args)
    {
        // insert the map tab before the Miscellaneous tab
        $item = $args['item'];
        $tabs['Map'] = $this->_mapForm($item);

        return $tabs;
    }

    public function filterPublicNavigationItems($navArray)
    {
        if (get_option('geolocation_link_to_nav')) {
            $navArray['Browse Map'] = array(
                'label'=>__('Browse Map'),
                'uri' => url('items/map')
            );
        }
        return $navArray;
    }

    /**
     * Register the geolocations API resource.
     *
     * @param array $apiResources
     * @return array
     */
    public function filterApiResources($apiResources)
    {
        $apiResources['geolocations'] = array(
            'record_type' => 'Location',
            'actions' => array('get', 'index', 'post', 'put', 'delete'),
        );
        return $apiResources;
    }

    /**
     * Add geolocations to item API representations.
     *
     * @param array $extend
     * @param array $args
     * @return array
     */
    public function filterApiExtendItems($extend, $args)
    {
        $item = $args['record'];
        $location = $this->_db->getTable('Location')->findBy(array('item_id' => $item->id));
        if (!$location) {
            return $extend;
        }
        $locationId = $location[0]['id'];
        $extend['geolocations'] = array(
            'id' => $locationId,
            'url' => Omeka_Record_Api_AbstractRecordAdapter::getResourceUrl("/geolocations/$locationId"),
            'resource' => 'geolocations',
        );
        return $extend;
    }

    /**
     * Hook to include a form in a contribution type form.
     *
     * @internal Themed partial should go to "my_theme/contribution/map".
     */
    public function hookContributionTypeForm($args)
    {
        if (get_option('geolocation_add_map_to_contribution_form')) {
            $contributionType = $args['type'];
            $view = $args['view'];
            $this->_p_html( $this->_mapForm(null, __('Find A Geographic Location For The %s:', $contributionType->display_name), false, $view, null));
        }
    }

    public function hookContributionSaveForm($args)
    {
        $this->hookAfterSaveItem($args);
    }

    public function filterExhibitLayouts($layouts)
    {
        $layouts['geolocation-map'] = array(
            'name' => __('Geolocation Map'),
            'description' => __('Show attached items on a map')
        );
        return $layouts;
    }

    public function filterApiImportOmekaAdapters($adapters, $args)
    {
        $geolocationAdapter = new ApiImport_ResponseAdapter_Omeka_GenericAdapter(null, $args['endpointUri'], 'Location');
        $geolocationAdapter->setResourceProperties(array('item' => 'Item'));
        $adapters['geolocations'] = $geolocationAdapter;
        return $adapters;
    }

    public function geolocationShortcode($args)
    {
        static $index = 0;
        $index++;

        $booleanFilter = new Omeka_Filter_Boolean;

        if (isset($args['lat'])) {
            $latitude = $args['lat'];
        } else {
            $latitude  = get_option('geolocation_default_latitude');
        }

        if (isset($args['lon'])) {
            $longitude = $args['lon'];
        } else {
            $longitude = get_option('geolocation_default_longitude');
        }

        if (isset($args['zoom'])) {
            $zoomLevel = $args['zoom'];
        } else {
            $zoomLevel = get_option('geolocation_default_zoom_level');
        }

        $center = array('latitude' => (double) $latitude, 'longitude' => (double) $longitude, 'zoomLevel' => (double) $zoomLevel);

        $options = array();

        if (isset($args['fit'])) {
            $options['fitMarkers'] = $booleanFilter->filter($args['fit']);
        } else {
            $options['fitMarkers'] = '1';
        }

        if (isset($args['type'])) {
            $options['mapType'] = $args['type'];
        }

        if (isset($args['collection'])) {
            $options['params']['collection'] = $args['collection'];
        }

        if (isset($args['tags'])) {
            $options['params']['tags'] = $args['tags'];
        }

        $pattern = '#^[0-9]*(px|%)$#';

        if (isset($args['height']) && preg_match($pattern, $args['height'])) {
            $height = $args['height'];
        } else {
            $height = '436px';
        }

        if (isset($args['width']) && preg_match($pattern, $args['width'])) {
            $width = $args['width'];
        } else {
            $width = '100%';
        }

        $attrs = array('style' => "height:$height;width:$width");
        return get_view()->geolocationMapBrowse("geolocation-shortcode-$index", $options, $attrs, $center);
    }

    /**
     * Returns the form code for geographically searching for items.
     *
     * @param Item $item
     * @param string $label if empty string, a default string will be used. Set
     * null if you don't want a label.
     * @param boolean $confirmLocChange
     * @param Omeka_View $view
     * @param array $post
     * @return string Html string.
     */
    protected function _mapForm($item, $label = '', $confirmLocChange = true, $view = null, $post = null)
    {
        if ($view === null) $view = get_view();

        // Need to be translated.
        if ($label == '') $label = __('Find a Location by Address:');

        $center = $this->_getCenter();
        $center['show'] = false;

        $location = $this->_db->getTable('Location')->findLocationByItem($item, true);
        $boxlocation = $this->_db->getTable('BoxLocation')->findLocationByItem($item, true);
        
        if ($post === null) {
            $request = Zend_Controller_Front::getInstance()->getRequest();
            $post = $request->getPost();
        }

        $usePost = !empty($post) && !empty($post['geolocation'])
                     && $post['geolocation']['longitude'] != ''
                     && $post['geolocation']['latitude'] != '';
        $usePostBox = false;
        if($usePost) $usePostBox = $post['geolocation']['width'] != ''
                               && $post['geolocation']['height'] != '';   
        
        if ($usePost && !$usePostBox) {
            $lng  = empty($post['geolocation']['longitude']) ? '' : (double) $post['geolocation']['longitude'];
            $lat  = empty($post['geolocation']['latitude']) ? '' : (double) $post['geolocation']['latitude'];
            $zoom = empty($post['geolocation']['zoom_level']) ? '' : (int) $post['geolocation']['zoom_level'];
            $address = html_escape($post['geolocation']['address']);
        } else {
            if ($location){
              $lng  = (double) $location['longitude'];
              $lat  = (double) $location['latitude'];
              $zoom = (int) $location['zoom_level'];
              $address = html_escape($location['address']);
            } else {
              $lng = $lat = $zoom = $address = '';
            }
        }
        
        if ($usePost && $usePostBox){
            $box_lat  = empty($post['geolocation']['latitude']) ? '' : (double) $post['geolocation']['latitude'];
            $box_lon  = empty($post['geolocation']['longitude']) ? '' : (double) $post['geolocation']['longitude'];
            $width  = empty($post['geolocation']['width']) ? '' : (double) $post['geolocation']['width'];
            $height  = empty($post['geolocation']['height']) ? '' : (double) $post['geolocation']['height'];
            $box_zoom = empty($post['geolocation']['zoom_level']) ? '' : (int) $post['geolocation']['zoom_level'];
            $box_address = html_escape($post['geolocation']['address']);
        } else {
            if ($boxlocation) {
                $box_lat  = (double) $boxlocation['latitude'];
                $box_lon  = (double) $boxlocation['longitude'];
                $width  = (double) $boxlocation['width'];
                $height  = (double) $boxlocation['height'];
                $box_zoom = (int) $boxlocation['zoom_level'];
                $box_address = html_escape($boxlocation['address']);
            } else {
                $box_lat = $box_lon = $box_address = $width = $height = $box_zoom = '';
            }
        }
        // Prepare javascript.
        $options = array();
        $options['form'] = array('id' => 'location_form','posted' => $usePost);
        if ($location or $usePost) {
            $options['point'] = array(
                'latitude' => $lat,
                'longitude' => $lng,
                'zoomLevel' => $zoom);
            $center = $options['point'];
        }
        if($boxlocation or $usePostBox){
          $options['points'] = array (
            'latitude' => $box_lat,
            'longitude' => $box_lon,
            'width' => $width,
            'height' => $height,
            'zoomLevel' => $box_zoom,
            'address' => $box_address);
        }
        $options['confirmLocationChange'] = $confirmLocChange;
        $options['cluster'] = false;
        
        return $view->partial('map/input-partial.php', array('label' => $label,
            'address' => $address,
            'center' => $center,
            'options' => $options,
            'lng' => empty($lng) ? $box_lon : $lng,
            'lat' => empty($lat) ? $box_lat : $lat,
            'width' => $width,
            'height' => $height,
            'zoom' => empty($zoom) ? $box_zoom : $zoom,
        ));
    }

    protected function _getCenter()
    {
        return array(
            'latitude'=>  (double) get_option('geolocation_default_latitude'),
            'longitude'=> (double) get_option('geolocation_default_longitude'),
            'zoomLevel'=> (double) get_option('geolocation_default_zoom_level'),
        );
    }
    
    private function _p_html($html){ ?>
      <?= $html ?> <?php
    }
}
