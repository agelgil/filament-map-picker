<?php

declare(strict_types=1);

namespace Agelgil\MapPicker\Infolists;

use Agelgil\MapPicker\Concerns\HasMap;
use Filament\Infolists\Components\Entry;

class MapEntry extends Entry
{
    use HasMap;

    /** Entry view */
    public string $view = 'map-picker::infolists.map-entry';

    /** Main entry config variables */
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
        'default' => ['lat' => 38.7025146996512, 'lng' => 9.003668847821515],
        'geoMan' => [
            'show' => false,
            'editable' => false,
            'position' => 'topleft',
            'drawCircleMarker' => false,
            'rotateMode' => false,
            'drawMarker' => false,
            'drawPolygon' => false,
            'drawPolyline' => false,
            'drawCircle' => false,
            'drawText' => false,
            'drawRectangle' => false,
            'dragMode' => false,
            'cutPolygon' => false,
            'editPolygon' => false,
            'deleteLayer' => false,
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
