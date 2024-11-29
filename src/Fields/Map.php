<?php

declare(strict_types=1);

namespace Agelgil\MapPicker\Fields;

use Agelgil\MapPicker\Concerns\HasMap;
use Filament\Forms\Components\Field;

class Map extends Field
{
    use HasMap;

    /** Field view */
    public string $view = 'map-picker::fields.map-picker';

    /** Main field config variables */
    private array $mapConfig = [
        'statePath' => '',
        'prefix' => true,
        'layers' => [],
        'bounds' => [
            'sw' => ['lat' => 8.7, 'lng' => 38.6],
            'ne' => ['lat' => 9.2, 'lng' => 39.1],
        ],
        'showMarker' => true,
        'draggable' => true,
        'clickable' => true,
        'markerColor' => '#3b82f6',
        'liveLocation' => false,
        'showMyLocationButton' => true,
        'default' => ['lat' => 38.70251, 'lng' => 9.00366],
        'geoMan' => [
            'show' => false,
            'editable' => true,
            'position' => 'topleft',
            'drawCircleMarker' => true,
            'rotateMode' => true,
            'drawMarker' => true,
            'drawPolygon' => true,
            'drawPolyline' => true,
            'drawCircle' => true,
            'drawText' => true,
            'drawRectangle' => true,
            'dragMode' => true,
            'cutPolygon' => true,
            'editPolygon' => true,
            'deleteLayer' => true,
            'color' => '#3388ff',
            'filledColor' => '#cad9ec',
        ],
    ];

    /** Leaflet controls variables */
    private array $controls = [
        'zoom' => 15,
        'minZoom' => 10,
        'maxZoom' => 28,
        'zoomControl' => true,
        'fullscreenControl' => true,
    ];
}
