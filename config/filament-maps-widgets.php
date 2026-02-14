<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Map Center
    |--------------------------------------------------------------------------
    |
    | The default center coordinates for maps when no specific center is provided.
    |
    */
    'default_center' => [
        'lat' => env('FILAMENT_MAPS_DEFAULT_LAT', 0),
        'lng' => env('FILAMENT_MAPS_DEFAULT_LNG', 0),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Map Zoom
    |--------------------------------------------------------------------------
    |
    | The default zoom level for maps.
    |
    */
    'default_zoom' => env('FILAMENT_MAPS_DEFAULT_ZOOM', 10),

    /*
    |--------------------------------------------------------------------------
    | Default Map Height
    |--------------------------------------------------------------------------
    |
    | The default height for map widgets.
    |
    */
    'default_height' => '500px',

    /*
    |--------------------------------------------------------------------------
    | Widget Border
    |--------------------------------------------------------------------------
    |
    | Whether widgets should have a border by default.
    |
    */
    'has_border' => false,

    /*
    |--------------------------------------------------------------------------
    | Map Options
    |--------------------------------------------------------------------------
    |
    | Default options that will be passed to the Leaflet map.
    | These can be overridden per widget.
    |
    */
    'map_options' => [
        'scrollWheelZoom' => true,
        'dragging' => true,
        'zoomControl' => true,
        'attributionControl' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Actions Position
    |--------------------------------------------------------------------------
    |
    | Default position for map actions/controls.
    | Options: 'topleft', 'topright', 'bottomleft', 'bottomright'
    |
    */
    'actions_position' => 'topright',
];
