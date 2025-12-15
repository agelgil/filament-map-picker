<?php

declare(strict_types=1);

namespace Agelgil\MapPicker\Concerns;

trait HasMap
{
    private array $extraStyle = [
        'min-height: 50vh',
    ];

    /** Extra leaflet controls variables */
    private array $extraControls = [];

    /** Create json configuration string */
    public function getMapConfig(): string
    {
        return json_encode(
            array_merge($this->mapConfig, [
                'statePath' => $this->getStatePath(),
                'controls' => array_merge($this->controls, $this->extraControls),
            ])
        );
    }

    /** Create extra styles string */
    public function getExtraStyle(): string
    {
        return implode(';', $this->extraStyle);
    }

    /** Determines if the user can click to place the marker on the map. */
    public function clickable(bool $clickable): self
    {
        $this->mapConfig['clickable'] = $clickable;

        return $this;
    }

    /** Determine if user can drag map around or not. */
    public function draggable(bool $draggable = true): self
    {
        $this->mapConfig['draggable'] = $draggable;

        return $this;
    }

    /**
     * Prevents the map from panning outside the defined box, and sets
     * a default location in the center of the box. It makes sense to
     * use this with a minimum zoom that suits the size of your map and
     * the size of the box or the way it pans back to the bounding box
     * looks strange. You can call with $on set to false to undo this.
     */
    public function boundaries(bool $on, int|float $southWestLat = 0, int|float $southWestLng = 0, int|float $northEastLat = 0, int|float $northEastLng = 0): self
    {
        if ( ! $on) {
            $this->mapConfig['boundaries'] = false;

            return $this;
        }

        $this->mapConfig['bounds']['sw'] = ['lat' => $southWestLat, 'lng' => $southWestLng];
        $this->mapConfig['bounds']['ne'] = ['lat' => $northEastLat, 'lng' => $northEastLng];
        $this->defaultLocation(($southWestLat + $northEastLat) / 2.0, ($southWestLng + $northEastLng) / 2.0);

        return $this;
    }

    public function defaultLocation(int|float $latitude, float|int $longitude): self
    {
        $this->mapConfig['default']['lat'] = $latitude;
        $this->mapConfig['default']['lng'] = $longitude;

        return $this;
    }

    /** Set extra style */
    public function extraStyles(array $styles = []): self
    {
        $this->extraStyle = $styles;

        return $this;
    }

    /**
     * Set default zoom
     *
     * @note Default value 19
     */
    public function zoom(int $zoom): self
    {
        $this->controls['zoom'] = $zoom;

        return $this;
    }

    /**
     * Set max zoom
     *
     * @return $this
     *
     * @note Default value 20
     */
    public function maxZoom(int $maxZoom): self
    {
        $this->controls['maxZoom'] = $maxZoom;

        return $this;
    }

    /**
     * Set min zoom
     *
     * @param  int  $maxZoom
     * @return $this
     *
     * @note Default value 1
     */
    public function minZoom(int $minZoom): self
    {
        $this->controls['minZoom'] = $minZoom;

        return $this;
    }

    /**
     * Determine if marker is visible or not.
     *
     * @return $this
     *
     * @note Default value is false
     */
    public function showMarker(bool $show = true): self
    {
        $this->mapConfig['showMarker'] = $show;

        return $this;
    }

    /**
     * Set prefix
     *
     * @return $this
     */
    public function prefix(bool $prefix): self
    {
        $this->mapConfig['prefix'] = $prefix;

        return $this;
    }

    /**
     * Use the value of another field on the form for the range of the
     * circle surrounding the marker
     *
     * @param  string  $rangeSelectField,
     *                                     return $this
     **/
    public function rangeSelectField(string $rangeSelectField): self
    {
        $this->mapConfig['rangeSelectField'] = $rangeSelectField;

        return $this;
    }

    /**
     * Determine if it detects retina monitors or not.
     *
     * @return $this
     */
    public function detectRetina(bool $detectRetina = true): self
    {
        $this->mapConfig['detectRetina'] = $detectRetina;

        return $this;
    }

    /**
     * Determine if zoom box is visible or not.
     *
     * @return $this
     */
    public function showZoomControl(bool $show = true): self
    {
        $this->controls['zoomControl'] = $show;

        return $this;
    }

    /**
     * Determine if fullscreen box is visible or not.
     *
     * @return $this
     */
    public function showFullscreenControl(bool $show = true): self
    {
        $this->controls['fullscreenControl'] = $show;

        return $this;
    }

    /**
     * Change the marker color.
     *
     * @return $this
     */
    public function markerColor(string $color): self
    {
        $this->mapConfig['markerColor'] = $color;

        return $this;
    }

    /**
     * Enable or disable live location updates for the map.
     *
     * @return $this
     */
    public function liveLocation(bool $send = true, bool $realtime = false, int $miliseconds = 5000): self
    {
        $this->mapConfig['liveLocation'] = [
            'send' => $send,
            'realtime' => $realtime,
            'miliseconds' => $miliseconds,
        ];

        return $this;
    }

    /**
     * Enable or disable show my location button on map.
     *
     * @return $this
     */
    public function showMyLocationButton(bool $showMyLocationButton = true): self
    {
        $this->mapConfig['showMyLocationButton'] = $showMyLocationButton;

        return $this;
    }

    /**
     * Append extra controls to be passed to leaflet map object
     *
     * @return $this
     */
    public function extraControl(array $control): self
    {
        $this->extraControls = array_merge($this->extraControls, $control);

        return $this;
    }

    /**
     * Append extra controls to be passed to leaflet tileLayer object
     *
     * @return $this
     */
    public function extraTileControl(array $control): self
    {
        $this->mapConfig = array_merge($this->mapConfig, $control);

        return $this;
    }

    /**
     * Enable or disable GeoMan functionality.
     *
     * @return $this
     */
    public function geoMan(bool $show = true): self
    {
        $this->mapConfig['geoMan']['show'] = $show;

        return $this;
    }

    /**
     * Enable or disable GeoMan edit mode.
     *
     * @return $this
     */
    public function geoManEditable(bool $show = true): self
    {
        $this->mapConfig['geoMan']['editable'] = $show;

        return $this;
    }

    /**
     * Set GeoMan control position.
     *
     * @return $this
     *
     * @note Valid values: 'topleft', 'topright', 'bottomleft', 'bottomright'
     */
    public function geoManPosition(string $position = 'topleft'): self
    {
        $this->mapConfig['geoMan']['position'] = $position;

        return $this;
    }

    /**
     * Enable or disable drawing of circle markers.
     *
     * @return $this
     */
    public function drawCircleMarker(bool $draw = true): self
    {
        $this->mapConfig['geoMan']['drawCircleMarker'] = $draw;

        return $this;
    }

    /**
     * Enable or disable rotate mode.
     *
     * @return $this
     */
    public function rotateMode(bool $rotate = true): self
    {
        $this->mapConfig['geoMan']['rotateMode'] = $rotate;

        return $this;
    }

    /**
     * Enable or disable drawing of markers.
     *
     * @return $this
     */
    public function drawMarker(bool $draw = true): self
    {
        $this->mapConfig['geoMan']['drawMarker'] = $draw;

        return $this;
    }

    /**
     * Enable or disable drawing of polygons.
     *
     * @return $this
     */
    public function drawPolygon(bool $draw = true): self
    {
        $this->mapConfig['geoMan']['drawPolygon'] = $draw;

        return $this;
    }

    /**
     * Enable or disable drawing of polylines.
     *
     * @return $this
     */
    public function drawPolyline(bool $draw = true): self
    {
        $this->mapConfig['geoMan']['drawPolyline'] = $draw;

        return $this;
    }

    /**
     * Enable or disable drawing of circles.
     *
     * @return $this
     */
    public function drawCircle(bool $draw = true): self
    {
        $this->mapConfig['geoMan']['drawCircle'] = $draw;

        return $this;
    }

    /**
     * Enable or disable drawing of text.
     *
     * @return $this
     */
    public function drawText(bool $draw = true): self
    {
        $this->mapConfig['geoMan']['drawText'] = $draw;

        return $this;
    }

    /**
     * Enable or disable drawing of rectangles.
     *
     * @return $this
     */
    public function drawRectangle(bool $draw = true): self
    {
        $this->mapConfig['geoMan']['drawRectangle'] = $draw;

        return $this;
    }

    /**
     * Enable or disable editing of polygons.
     *
     * @return $this
     */
    public function editPolygon(bool $edit = true): self
    {
        $this->mapConfig['geoMan']['editPolygon'] = $edit;

        return $this;
    }

    /**
     * Enable or disable deletion of layers.
     *
     * @return $this
     */
    public function deleteLayer(bool $delete = true): self
    {
        $this->mapConfig['geoMan']['deleteLayer'] = $delete;

        return $this;
    }

    /**
     * Enable or disable drag mode.
     *
     * @return $this
     */
    public function dragMode(bool $enable = true): self
    {
        $this->mapConfig['geoMan']['dragMode'] = $enable;

        return $this;
    }

    /**
     * Enable or disable polygon cutting.
     *
     * @return $this
     */
    public function cutPolygon(bool $enable = true): self
    {
        $this->mapConfig['geoMan']['cutPolygon'] = $enable;

        return $this;
    }

    /**
     * Set the stroke color for drawings.
     *
     * @return $this
     */
    public function setColor(string $color): self
    {
        $this->mapConfig['geoMan']['color'] = $color;

        return $this;
    }

    /**
     * Set the fill color for drawings.
     *
     * @return $this
     */
    public function setFilledColor(string $filledColor): self
    {
        $this->mapConfig['geoMan']['filledColor'] = $filledColor;

        return $this;
    }

    /**
     * Setup function
     */
    protected function setUp(): void
    {
        parent::setUp();
    }
}
