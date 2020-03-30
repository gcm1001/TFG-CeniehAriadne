function OmekaMap(mapDivId, center, options) {
    this.mapDivId = mapDivId;
    this.center = center;
    this.options = options;
}

OmekaMap.prototype = {

    map: null,
    mapDivId: null,
    markers: [],
    options: {},
    center: null,
    markerBounds: null,
    clusterGroup: null,

    addMarker: function (latLng, options, bindHtml)
    {
        var map = this.map;
        var marker = L.marker(latLng, options);

        if (this.clusterGroup) {
            this.clusterGroup.addLayer(marker);
        } else {
            marker.addTo(map);
        }

        if (bindHtml) {
            marker.bindPopup(bindHtml, {autoPanPadding: [50, 50]});
            // Fit images on the map on first load
            marker.once('popupopen', function (event) {
                var popup = event.popup;
                var imgs = popup.getElement().getElementsByTagName('img');
                for (var i = 0; i < imgs.length; i++) {
                    imgs[i].addEventListener('load', function imgLoadListener(event) {
                        event.target.removeEventListener('load', imgLoadListener);
                        // Marker autopan is disabled during panning, so defer
                        if (map._panAnim && map._panAnim._inProgress) {
                            map.once('moveend', function () {
                                popup.update();
                            });
                        } else {
                            popup.update();
                        }
                    });
                }
            });
        }

        this.markers.push(marker);
        this.markerBounds.extend(latLng);
        return marker;
    },
    fitMarkers: function () {
        if (this.markers.length == 1) {
            this.map.panTo(this.markers[0].getLatLng());
        } else if (this.markers.length > 0) {
            this.map.fitBounds(this.markerBounds, {padding: [25, 25]});
        }
    },

    initMap: function () {
        if (!this.center) {
            alert('Error: The center of the map has not been set!');
            return;
        }

        this.map = L.map(this.mapDivId).setView([this.center.latitude, this.center.longitude], this.center.zoomLevel);
        this.markerBounds = L.latLngBounds();

        L.tileLayer.provider(this.options.basemap, this.options.basemapOptions).addTo(this.map);

        if (this.options.cluster) {
            this.clusterGroup = L.markerClusterGroup({
                showCoverageOnHover: false
            });
            this.map.addLayer(this.clusterGroup);
        }

        jQuery(this.map.getContainer()).trigger('o:geolocation:init_map', this);

        // Show the center marker if we have that enabled.
        if (this.center.show) {
            this.addMarker([this.center.latitude, this.center.longitude],
                           {title: "(" + this.center.latitude + ',' + this.center.longitude + ")"},
                           this.center.markerHtml);
        }
    }
};

function OmekaMapBrowse(mapDivId, center, options) {
    var omekaMap = new OmekaMap(mapDivId, center, options);
    jQuery.extend(true, this, omekaMap);
    this.initMap();

    //XML loads asynchronously, so need to call for further config only after it has executed
    this.loadKmlIntoMap(this.options.uri, this.options.params);
}

OmekaMapBrowse.prototype = {

    afterLoadItems: function () {
        if (this.options.fitMarkers) {
            this.fitMarkers();
        }

        if (!this.options.list) {
            return;
        }
        var listDiv = jQuery('#' + this.options.list);

        if (!listDiv.size()) {
            alert('Error: You have no map links div!');
        } else {
            //Create HTML links for each of the markers
            this.buildListLinks(listDiv);
        }
    },

    /* Need to parse KML manually b/c Google Maps API cannot access the KML
       behind the admin interface */
    loadKmlIntoMap: function (kmlUrl, params) {
        var that = this;
        jQuery.ajax({
            type: 'GET',
            dataType: 'xml',
            url: kmlUrl,
            data: params,
            success: function(data) {
                var xml = jQuery(data);

                /* KML can be parsed as:
                    kml - root element
                        Placemark
                            namewithlink
                            description
                            Point - longitude,latitude
                */
                var placeMarks = xml.find('Placemark');

                // If we have some placemarks, load them
                if (placeMarks.size()) {
                    // Retrieve the balloon styling from the KML file
                    that.browseBalloon = that.getBalloonStyling(xml);

                    // Build the markers from the placemarks
                    jQuery.each(placeMarks, function (index, placeMark) {
                        placeMark = jQuery(placeMark);
                        that.buildMarkerFromPlacemark(placeMark);
                    });

                    // We have successfully loaded some map points, so continue setting up the map object
                    return that.afterLoadItems();
                } else {
                    // @todo Elaborate with an error message
                    return false;
                }
            }
        });
    },

    getBalloonStyling: function (xml) {
        return xml.find('BalloonStyle text').text();
    },

    // Build a marker given the KML XML Placemark data
    // I wish we could use the KML file directly, but it's behind the admin interface so no go
    buildMarkerFromPlacemark: function (placeMark) {
        // Get the info for each location on the map
        var title = placeMark.find('name').text();
        var titleWithLink = placeMark.find('namewithlink').text();
        var body = placeMark.find('description').text();
        var snippet = placeMark.find('Snippet').text();

        // Extract the lat/long from the KML-formatted data
        var coordinates = placeMark.find('Point coordinates').text().split(',');
        var longitude = coordinates[0];
        var latitude = coordinates[1];

        // Use the KML formatting (do some string sub magic)
        var balloon = this.browseBalloon;
        balloon = balloon.replace('$[namewithlink]', titleWithLink).replace('$[description]', body).replace('$[Snippet]', snippet);

        // Build a marker, add HTML for it
        this.addMarker([latitude, longitude], {title: title}, balloon);
    },

    buildListLinks: function (container) {
        var that = this;
        var list = jQuery('<ul></ul>');
        list.appendTo(container);

        // Loop through all the markers
        jQuery.each(this.markers, function (index, marker) {
            var listElement = jQuery('<li></li>');

            // Make an <a> tag, give it a class for styling
            var link = jQuery('<a></a>');
            link.addClass('item-link');

            // Links open up the markers on the map, clicking them doesn't actually go anywhere
            link.attr('href', 'javascript:void(0);');

            // Each <li> starts with the title of the item
            link.html(marker.options.title);

            // Clicking the link should take us to the map
            link.bind('click', {}, function (event) {
                if (that.clusterGroup) {
                    that.clusterGroup.zoomToShowLayer(marker, function () {
                        marker.fire('click');
                    });
                } else {
                    that.map.once('moveend', function () {
                        marker.fire('click');
                    });
                    that.map.flyTo(marker.getLatLng());
                }
            });

            link.appendTo(listElement);
            listElement.appendTo(list);
        });
    }
};

function OmekaMapSingle(mapDivId, center, options) {
    var omekaMap = new OmekaMap(mapDivId, center, options);
    jQuery.extend(true, this, omekaMap);
    this.initMap();
}

function OmekaMapForm(mapDivId, center, options) {
    var that = this;
    var omekaMap = new OmekaMap(mapDivId, center, options);
    jQuery.extend(true, this, omekaMap);
    this.initMap();

    this.formDiv = jQuery('#' + this.options.form.id);
    // Si está activada la funcionalidad de dibujo...
    if (this.options.draw) {
        var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                  osmAttrib = '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                  osm = L.tileLayer(osmUrl, { maxZoom: 18, attribution: osmAttrib }),
                  drawnItems = L.featureGroup().addTo(this.map);

      	L.control.layers({
              'osm': osm.addTo(this.map),
              "google": L.tileLayer('http://www.google.cn/maps/vt?lyrs=s@189&gl=cn&x={x}&y={y}&z={z}', {
                  attribution: 'google'
              })
          }, { 'location view': drawnItems }, { position: 'topright', collapsed: false }).addTo(this.map);

      	this.map.addControl(new L.Control.Draw({
              edit: {
                  featureGroup: drawnItems,
                  poly: {
                      allowIntersection: false
                  }
              },
              draw: {
                  polygon: false,
                  circlemarker: false,
                  circle: false,
                  polyline: false
              }
          }));

          // Se activa cuando una figura / marcador es creada.
          this.map.on(L.Draw.Event.CREATED, function (event) {
              var layer = event.layer,
                  type = event.layerType;
              // solo se admite una localización por objeto
              drawnItems.clearLayers();
              that.clearForm();
              that.clearBoxForm();
              //si es un objeto de tipo 'marker' (Marcador).
              if (type === 'marker') {
                  // coordenada del marcador
                  var point = layer.getLatLng();
                  // objeto (layer)
                  var marker = that.setMarker(point);
                  // añado un Pop Up al objeto informando su longitud y latitud
                  marker.bindPopup('Lat: ' + point.lat + ' Lon: ' + point.lng).openPopup();
                  // añado el objeto a la capa de objetos (location view)
                  drawnItems.addLayer(marker);
              }
              // si es un objeto de tipo 'rectangle' (Rectángulo)
              if (type === 'rectangle') {
                //coordenadas de los cuatro puntos que lo componen
                var points = layer.getLatLngs();
                // objeto (layer)
                var boxMarker = that.setBoxMarker(points);
                // añado el objeto a la capa de objetos (location view)
                drawnItems.addLayer(boxMarker);
              }
          });
          // Se activa cuando una figura / marcador es eliminado (siempre que se guarde)
          this.map.on('draw:deleted', function (event) {
              // Antes de guardar la eliminación, se pregunta al usuario
              if (!confirm('Are you sure you want to remove the location of the item?')
              ) {
                  return false;
              }
              // objetos eliminados (en nuestro caso,siempre será uno)
              var layers = event.layers;
              //por cada objeto eliminado se limpian sus elementos
              layers.eachLayer(function (layer) {
                if (layer instanceof L.Marker) {
                    that.clearForm();
                }
                if (layer instanceof L.Rectangle) {
                    that.clearBoxForm();
                }
              });
          });
        // Se activa cuando una figura / marcador es editado (siempre que se guarde)
        this.map.on('draw:edited', function (event) {
            // Antes de guardar la edición, se pregunta al usuario
            if (!confirm('Are you sure you want to change the location of the item?')
            ) {
                return false;
            }
            // objetos editados(en nuestro caso,siempre será uno)
            var layers = event.layers;
            //por cada objeto editado, se actualizan sus elementos
            layers.eachLayer(function (layer) {
                if (layer instanceof L.Marker) {
                    that.setMarker(layer.getLatLng());
                }
                if (layer instanceof L.Rectangle) {
                    that.setBoxMarker(layer.getLatLngs());
                }
            });
        });
    // Si no está activada la función dibujo se conserva el funcionamiento normal...
    } else {
        this.map.on('click', function (event) {
            var marker = that.setMarker(event.latlng);
              if (marker) {
                jQuery('#geolocation_address').val('');
            }
        });
    }

    // Make the map update on zoom changes.
    this.map.on('zoomend', function () {
        that.updateZoomForm();
        that.updateZoomBoxForm();
    });

    // Add the existing map point.
    if (this.options.point) {
        var point = L.latLng(this.options.point.latitude, this.options.point.longitude);
        var drawpoint = new L.marker(point);
        this.setMarker(point);
        drawnItems.addLayer(drawpoint.bindPopup('Lat: ' + point.lat + ' Lon: ' + point.lng).openPopup());
        this.map.setView(point, this.options.point.zoomLevel);
    }
    // Añado el rectángulo si es que lo hubiera
    if(this.options.points){
      // objeto (layer)
    	var minlat = this.options.points.latitude - this.options.points.height;
    	var maxlon = this.options.points.longitude + this.options.points.width;
        var box = new L.rectangle([[minlat,this.options.points.longitude],
                                   [this.options.points.latitude,this.options.points.longitude],
                                   [this.options.points.latitude,maxlon],
                                   [minlat,maxlon]]);
        // calculo el punto medio del rectángulo para centrar la vista
        var midpoint = L.latLng((minlat+this.options.points.latitude)/2,(this.options.points.longitude+maxlon)/2);
        // plasmo el objeto en el mapa
        drawnItems.addLayer(box);
        // centro la vista en base al punto medio y zoom asociado al objeto
        this.map.setView(midpoint, this.options.points.zoomLevel);
    }

}

OmekaMapForm.prototype = {
    /* Set the marker to the point. */
    setMarker: function (point) {
        var that = this;

            if (this.options.confirmLocationChange
                && this.markers.length > 0
                && !confirm('Are you sure you want to change the location of the item?')
            ) {
                return false;
            }

            // Get rid of existing markers.
            this.clearForm();

            // Add the marker
            var marker = (this.options.draw) ? L.marker(point) : this.addMarker(point);

            // Pan the map to the marker
            this.map.panTo(point);

            //  Make the marker clear the form if clicked.
            marker.on('click', function (event) {
                if (!that.options.confirmLocationChange || confirm('Are you sure you want to remove the location of the item?')) {
                    that.clearForm();
                }
            });

            this.updateForm(point);
            return marker;
    },

    setBoxMarker: function (points) {
        var box = new L.rectangle(points);

        var midpoint = L.latLng((points[0][0].lat + points[0][2].lat)/2,(points[0][0].lng + points[0][2].lng)/2);

        this.map.panTo(midpoint);

        this.updateBoxForm(points);
        return box;
    },
    /* Update the latitude, longitude, and zoom of the form. */
    updateForm: function (point) {
        var latElement = document.getElementsByName('geolocation[latitude]')[0];
        var lngElement = document.getElementsByName('geolocation[longitude]')[0];
        var zoomElement = document.getElementsByName('geolocation[zoom_level]')[0];

        // If we passed a point, then set the form to that. If there is no point, clear the form
        if (point) {
            latElement.value = point.lat;
            lngElement.value = point.lng;
            zoomElement.value = this.map.getZoom();
        } else {
            latElement.value = '';
            lngElement.value = '';
            zoomElement.value = this.map.getZoom();
        }
    },
    /* Se actiza la latitud y longitud de cada punto existente en la forma del rectángulo */
    updateBoxForm: function (points) {
        var latElement = document.getElementsByName('geolocation[latitude]')[0];
        var lngElement = document.getElementsByName('geolocation[longitude]')[0];
        var widthElement = document.getElementsByName('geolocation[width]')[0];
        var heightElement = document.getElementsByName('geolocation[height]')[0];
        var zoomElement = document.getElementsByName('geolocation[zoom_level]')[0];

        if (points) {
        	var width = Math.abs(points[0][1].lng - points[0][2].lng);
        	var height = Math.abs(points[0][1].lat - points[0][0].lat);
        	latElement.value = points[0][1].lat;
        	lngElement.value = points[0][1].lng;
        	widthElement.value = width;
        	heightElement.value = height;
        	zoomElement.value = this.map.getZoom();
        	
        } else {
        	latElement.value = '';
        	lngElement.value = '';
        	widthElement.value = '';
        	heightElement.value = '';
        	zoomElement.value = this.map.getZoom();
        }
    },
    /* Update the zoom input of the form to be the current zoom on the map. */
    updateZoomForm: function () {
        var zoomElement = document.getElementsByName('geolocation[zoom_level]')[0];
        zoomElement.value = this.map.getZoom();
    },
    /* Se actualiza el zoom de la forma asociada al rectángulo dibujado en el mapa. */
    updateZoomBoxForm: function () {
        var zoomElement = document.getElementsByName('geolocation[zoom_level]')[0];
        zoomElement.value = this.map.getZoom();
    },
    /* Clear the form of all markers. */
    clearForm: function () {
        // Remove the markers from the map
        for (var i = 0; i < this.markers.length; i++) {
            this.markers[i].remove();
        }

        // Clear the markers array
        this.markers = [];

        // Update the form
        this.updateForm();
    },
    /* Se limpia el valor de los elementos de la forma asociada al rectángulo*/
    clearBoxForm: function () {
        // Update the form
        this.updateBoxForm();
    },
    /* Resize the map and center it on the first marker. */
    resize: function () {
        this.map.invalidateSize();
    }
};
