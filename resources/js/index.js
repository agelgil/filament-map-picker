import * as L from 'leaflet';
import 'leaflet-fullscreen';
import 'leaflet-hash';
import "@geoman-io/leaflet-geoman-free";


document.addEventListener('DOMContentLoaded', () => {
    const mapPicker = ($wire, config, state) => {
        return {
            map: null,
            layerControl: null,
            marker: null,
            drawItems: null,

            createMap: function (el) {
                const that = this;

                if (! config.prefix) {
                    L.Control.Attribution.prototype.options.prefix = false;
                }

                const baseMaps = {};
                config.layers.forEach(layer => {
                    baseMaps[layer.label] = L.tileLayer(layer.url, {
                        minZoom: 1,
                        maxZoom: 28,
                        tileSize: 256,
                        detectRetina: true,
                        ...layer.control,
                   });
                });

                this.map = L.map(el, {
                    ...config.controls,
                    layers: [baseMaps[Object.keys(baseMaps)[0]]],
                });

                this.layerControl = L.control.layers(baseMaps, {}).addTo(this.map);

                if(config.bounds)
                {
                    let southWest = L.latLng(config.bounds.sw.lat, config.bounds.sw.lng);
                    let northEast = L.latLng(config.bounds.ne.lat, config.bounds.ne.lng);
                    let bounds = L.latLngBounds(southWest, northEast);
                    this.map.setMaxBounds(bounds);
                    this.map.fitBounds(bounds);
                    this.map.on('drag', function() {
                        that.map.panInsideBounds(bounds, { animate: false });
                    });
                }
                this.map.on('load', () => {
                    setTimeout(() => this.map.invalidateSize(true), 0);
                    if (config.showMarker) {
                        this.marker.setLatLng(this.map.getCenter());
                    }
                });

                if (!config.draggable) {
                    this.map.dragging.disable();
                }

                if(config.clickable)
                {
                    this.map.on('click', function(e) {
                        that.setCoordinates(e.latlng);
                    });
                }

                if (config.showMarker) {
                    const markerColor = config.markerColor || "#3b82f6";
                    const svgIcon = L.divIcon({
                        html: `<svg xmlns="http://www.w3.org/2000/svg" class="map-icon" fill="${markerColor}" width="36" height="36" viewBox="0 0 24 24"><path d="M12 0c-4.198 0-8 3.403-8 7.602 0 4.198 3.469 9.21 8 16.398 4.531-7.188 8-12.2 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"/></svg>`,
                        className: "",
                        iconSize: [36, 36],
                        iconAnchor: [18, 36],
                    });
                    this.marker = L.marker([0, 0], {
                        icon: svgIcon,
                        draggable: false,
                        autoPan: true
                    }).addTo(this.map);
                }

                this.map.on('locationfound', function () {
                    that.map.setZoom(config.controls.zoom);
                });

                let location = state ?? this.getCoordinates();
                if (!location.lat && !location.lng) {
                    this.map.locate({
                        setView: true,
                        maxZoom: config.controls.maxZoom,
                        enableHighAccuracy: true,
                        watch: false
                    });
                } else {
                    this.map.setView(new L.LatLng(location.lat, location.lng));
                    this.marker.setLatLng(new L.LatLng(location.lat, location.lng));

                    setTimeout(() => {
                        this.map.flyTo(new L.LatLng(location.lat, location.lng), 18);
                    }, 500);
                }

                if (config.showMyLocationButton) {
                    this.addLocationButton();
                }

                if (config.liveLocation.send && config.liveLocation.realtime) {
                    setInterval(() => {
                        this.fetchCurrentLocation();
                    }, config.liveLocation.miliseconds);
                }

                // Geoman setup
                if (config.geoMan.show) {
                        this.map.pm.addControls({
                            position: config.geoMan.position,
                            drawCircleMarker: config.geoMan.drawCircleMarker,
                            rotateMode: config.geoMan.rotateMode,
                            drawMarker: config.geoMan.drawMarker,
                            drawPolygon: config.geoMan.drawPolygon,
                            drawPolyline: config.geoMan.drawPolyline,
                            drawCircle: config.geoMan.drawCircle,
                            drawText: config.geoMan.drawText,
                            drawRectangle: config.geoMan.drawRectangle,
                            editMode: config.geoMan.editMode,
                            dragMode: config.geoMan.dragMode,
                            cutPolygon: config.geoMan.cutPolygon,
                            editPolygon: config.geoMan.editPolygon,
                            deleteLayer: config.geoMan.deleteLayer
                        });

                        this.drawItems = new L.FeatureGroup().addTo(this.map);

                        this.map.on('pm:create', (e) => {
                            if (e.layer && e.layer.pm) {
                                e.layer.pm.enable();
                                this.drawItems.addLayer(e.layer);
                                this.updateGeoJson();
                            }
                        });

                        this.map.on('pm:edit', () => {
                            this.updateGeoJson();
                        });

                        this.map.on('pm:remove', (e) => {
                            try {
                                this.drawItems.removeLayer(e.layer);
                                this.updateGeoJson();
                            } catch (error) {
                                console.error("Error during removal of layer:", error);
                            }
                        });

                    // Load existing GeoJSON if available
                    const existingGeoJson = this.getGeoJson();
                    if (existingGeoJson) {
                            this.drawItems = L.geoJSON(existingGeoJson, {
                                pointToLayer: (feature, latlng) => {
                                    return L.circleMarker(latlng, {
                                        radius: 15,
                                        color: '#3388ff',
                                        fillColor: '#3388ff',
                                        fillOpacity: 0.6
                                    });
                                },
                                style: function(feature) {
                                    if (feature.geometry.type === 'Polygon') {
                                        return {
                                            color: config.geoMan.color || "#3388ff",
                                            fillColor: config.geoMan.filledColor || 'blue',
                                            weight: 2,
                                            fillOpacity: 0.4
                                        };
                                    }
                                },
                                onEachFeature: (feature, layer) => {
                                    if (feature.properties && feature.properties.popupContent) {
                                        layer.bindPopup(feature.properties.popupContent);
                                    } else if (feature.geometry.type === 'Polygon') {
                                        layer.bindPopup("Polygon Area");
                                    } else if (feature.geometry.type === 'Point') {
                                        layer.bindPopup("Point Location");
                                    }


                                    if (config.geoMan.editable) {
                                        if (feature.geometry.type === 'Polygon') {
                                            layer.pm.enable({
                                                allowSelfIntersection: false
                                            });
                                        } else if (feature.geometry.type === 'Point') {
                                            layer.pm.enable({
                                                draggable: true
                                            });
                                        }
                                    }

                                    layer.on('pm:edit', () => {
                                        this.updateGeoJson();
                                    });
                                }
                            }).addTo(this.map);

                            setTimeout(() => {
                                this.map.flyToBounds(this.drawItems.getBounds());
                            }, 500);

                            if(config.geoMan.editable){
                                // Enable editing for each layer
                                this.drawItems.eachLayer(layer => {
                                    layer.pm.enable({
                                        allowSelfIntersection: false,
                                    });
                                });
                            }
                    }
              }

                // Hash setup
                this.map.addHash()
            },

            updateGeoJson: function() {
                try {
                    const geoJsonData = this.drawItems.toGeoJSON();
                    if (typeof geoJsonData !== 'object') {
                        console.error("GeoJSON data is not an object:", geoJsonData);
                        return;
                    }

                    $wire.set(config.statePath, {
                        ...$wire.get(config.statePath),
                        geojson: geoJsonData
                    }, true);

                } catch (error) {
                    console.error("Error updating GeoJSON:", error);
                }
            },

            getGeoJson: function() {
                const state = $wire.get(config.statePath) ?? {};
                return state.geojson ?? this.state.geojson;
            },

            updateLocation: function(coordinates) {
                $wire.set(config.statePath, {
                    ...$wire.get(config.statePath),
                    ...coordinates,
                }, false);

                if (config.liveLocation.send) {
                    $wire.$refresh();
                }
            },

            removeMap: function (el) {
                if (this.marker) {
                    this.marker.remove();
                    this.marker = null;
                }
                this.layerControl.remove();
                this.layerControl = null;
                this.map.off();
                this.map.remove();
                this.map = null;
            },

            getCoordinates: function () {
                let location = $wire.get(config.statePath) ?? {};

                const hasValidCoordinates = location.hasOwnProperty('lat') && location.hasOwnProperty('lng') &&
                    location.lat !== null && location.lng !== null;

                if (!hasValidCoordinates) {
                    location = {
                        lat: config.default.lat,
                        lng: config.default.lng
                    };
                }

                return location;
            },

            setCoordinates: function (coords) {

                $wire.set(config.statePath, {
                    ...$wire.get(config.statePath),
                    lat: coords.lat,
                    lng: coords.lng
                }, false);

                if (config.liveLocation.send) {
                    $wire.$refresh();
                }
                this.updateMarker();
                return coords;
            },

            attach: function (el) {
                this.createMap(el);
                const observer = new IntersectionObserver(entries => {
                    entries.forEach(entry => {
                        if (entry.intersectionRatio > 0) {
                            if (!this.map)
                                this.createMap(el);
                        } else {
                            this.removeMap(el);
                        }
                    });
                }, {
                    root: null,
                    rootMargin: '0px',
                    threshold: 1.0
                });
                observer.observe(el);
            },

            fetchCurrentLocation: function () {
                if ('geolocation' in navigator) {
                    navigator.geolocation.getCurrentPosition(async position => {
                        const currentPosition = new L.LatLng(position.coords.latitude, position.coords.longitude);
                        const zoom = 16 - Math.log2(position.coords.accuracy / 500);
                        await this.map.flyTo(currentPosition, zoom - 3);
                    }, error => {
                        console.error('Error fetching current location:', error);
                    });
                } else {
                    alert('Geolocation is not supported by this browser.');
                }
            },

            addLocationButton: function() {
                const that = this;

                L.Control.Button = L.Control.extend({
                    options: {
                        position: 'bottomright'
                    },
                    onAdd: function (map) {
                        let container = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
                        let button = L.DomUtil.create('a', 'leaflet-control-button map-location-button-enabled', container);
                        L.DomEvent.disableClickPropagation(button);
                        L.DomEvent.on(button, 'click', () => that.fetchCurrentLocation());
                        container.title = "Get current location";

                        if ("geolocation" in navigator) {
                            navigator.geolocation.getCurrentPosition(async () => {
                                L.DomUtil.addClass(button, 'map-location-button-enabled');
                                L.DomUtil.removeClass(button, 'map-location-button-disabled');
                            }, async () => {
                                L.DomUtil.addClass(button, 'map-location-button-disabled');
                                L.DomUtil.removeClass(button, 'map-location-button-enabled');
                            });
                        }

                        return container;
                    },
                    onRemove: function(map) {},
                });

                let control = new L.Control.Button()
                control.addTo(this.map);
            },

            init: function() {
                this.$wire = $wire;
                this.config = config;
                this.state = state;
                $wire.on('refreshMap', this.refreshMap.bind(this));
            },

            updateMarker: function() {
                if (config.showMarker) {
                    let coordinates = this.getCoordinates()
                    this.marker.setLatLng(coordinates);
                    setTimeout(() => this.updateLocation(coordinates), 500);
                }
            },

            refreshMap: function() {
                this.map.flyTo(this.getCoordinates());
                this.updateMarker();
            }
        };
    };

    window.mapPicker = mapPicker;

    window.dispatchEvent(new CustomEvent('map-script-loaded'));
});
