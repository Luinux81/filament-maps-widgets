<?php

namespace LBCDev\FilamentMapsWidgets\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;
use LBCDev\MapGeometries\Marker;
use LBCDev\MapGeometries\MarkerCollection;

/**
 * Base class for creating map widgets in Filament panels.
 * 
 * This widget integrates with the LivewireMap component from the core package
 * to provide interactive map functionality within Filament panels.
 * 
 * @example
 * ```php
 * class LocationsMap extends MapWidget
 * {
 *     protected function getMarkers(): MarkerCollection
 *     {
 *         $markers = new MarkerCollection();
 *         
 *         \App\Models\Location::all()->each(function ($location) use ($markers) {
 *             $markers->add(
 *                 Marker::make(
 *                     $location->latitude,
 *                     $location->longitude,
 *                     $location->name
 *                 )->tooltip($location->description)
 *             );
 *         });
 *         
 *         return $markers;
 *     }
 * }
 * ```
 */
abstract class MapWidget extends Widget
{
    /**
     * The view that will render this widget.
     * 
     * @var view-string
     */
    protected string $view = 'filament-maps-widgets::map-widget';

    /**
     * The number of columns the widget should span.
     * 
     * @var int|string|array
     */
    protected int | string | array $columnSpan = 'full';

    /**
     * The height of the map widget.
     * 
     * Can be any valid CSS height value (e.g., '500px', '100vh', '50%').
     * 
     * @var string
     */
    public string $height = '500px';

    /**
     * Whether the widget should have a border.
     * 
     * @var bool
     */
    protected bool $hasBorder = false;

    /**
     * Whether the widget is currently loading.
     * 
     * @var bool
     */
    public bool $isLoading = false;

    /**
     * Get the markers to display on the map.
     * 
     * This method must be implemented by child classes to provide
     * the markers that will be rendered on the map.
     * 
     * @return array|MarkerCollection Array of Marker objects or a MarkerCollection instance
     */
    abstract protected function getMarkers(): array|MarkerCollection;

    /**
     * Get the center coordinates for the map.
     * 
     * Returns an array with 'lat' and 'lng' keys.
     * Override this method to provide custom center coordinates.
     * 
     * @return array{lat: float, lng: float}
     */
    protected function getMapCenter(): array
    {
        return config('filament-maps-widgets.default_center', [
            'lat' => 0,
            'lng' => 0,
        ]);
    }

    /**
     * Get the initial zoom level for the map.
     * 
     * Override this method to provide a custom zoom level.
     * Typical values range from 1 (world) to 18 (street level).
     * 
     * @return int
     */
    protected function getMapZoom(): int
    {
        return config('filament-maps-widgets.default_zoom', 10);
    }

    /**
     * Get the Leaflet map options.
     * 
     * Override this method to customize map behavior.
     * Common options include:
     * - scrollWheelZoom: Enable/disable zoom with mouse wheel
     * - dragging: Enable/disable map dragging
     * - minZoom/maxZoom: Limit zoom levels
     * - maxBounds: Restrict map panning to specific bounds
     * 
     * @return array
     */
    protected function getMapOptions(): array
    {
        return config('filament-maps-widgets.map_options', [
            'scrollWheelZoom' => true,
            'dragging' => true,
            'zoomControl' => true,
            'attributionControl' => true,
        ]);
    }

    /**
     * Get the actions/controls to display on the map.
     * 
     * Override this method to add custom actions (e.g., ZoomAction, FullscreenAction).
     * Actions will be rendered on the map at the position specified in their configuration.
     * 
     * @return array
     */
    protected function getActions(): array
    {
        return [];
    }

    /**
     * Get the complete map configuration.
     * 
     * This method combines all configuration methods into a single array
     * that will be passed to the LivewireMap component.
     * 
     * @return array{center: array, zoom: int, options: array, markers: array|MarkerCollection, actions: array}
     */
    protected function getMapConfig(): array
    {
        return [
            'center' => $this->getMapCenter(),
            'zoom' => $this->getMapZoom(),
            'options' => $this->getMapOptions(),
            'markers' => $this->getMarkers(),
            'actions' => $this->getActions(),
        ];
    }

    /**
     * Get the data to pass to the view.
     * 
     * This method is called by Filament when rendering the widget.
     * 
     * @return array
     */
    protected function getViewData(): array
    {
        return [
            'mapConfig' => $this->getMapConfig(),
            'height' => $this->height,
            'hasBorder' => $this->hasBorder,
        ];
    }

    /**
     * Determine if the widget can be viewed.
     * 
     * Override this method to add authorization logic.
     * 
     * @return bool
     */
    public static function canView(): bool
    {
        return true;
    }

    /**
     * Get the widget's heading.
     * 
     * Override this method to provide a custom heading for the widget.
     * Return null to hide the heading.
     * 
     * @return string|null
     */
    protected function getHeading(): ?string
    {
        return null;
    }

    /**
     * Refresh the widget.
     * 
     * This method can be called to force a refresh of the widget's data.
     * Useful when data changes and the map needs to be updated.
     * 
     * @return void
     */
    public function refresh(): void
    {
        // Trigger Livewire refresh
        $this->dispatch('$refresh');
    }

    /**
     * Center the map to specific coordinates.
     * 
     * This method allows programmatically centering the map to specific coordinates.
     * 
     * @param float $lat Latitude
     * @param float $lng Longitude
     * @param int|null $zoom Optional zoom level
     * @return void
     */
    public function centerTo(float $lat, float $lng, ?int $zoom = null): void
    {
        $this->dispatch('fly-to-coordinates', [
            'lat' => $lat,
            'lng' => $lng,
            'zoom' => $zoom ?? $this->getMapZoom(),
        ]);
    }

    /**
     * Add a marker to the map dynamically.
     * 
     * Note: This requires the LivewireMap component to support dynamic marker addition.
     * 
     * @param Marker $marker The marker to add
     * @return void
     */
    public function addMarker(Marker $marker): void
    {
        $this->dispatch('map-add-marker', [
            'marker' => $marker->toArray(),
        ]);
    }

    /**
     * Remove a marker from the map by its coordinates or label.
     * 
     * Note: This requires the LivewireMap component to support dynamic marker removal.
     * Since markers don't have unique IDs, you may need to identify them by coordinates.
     * 
     * @param float|array $identifier Latitude (if float) or [lat, lng] array
     * @param float|null $longitude Longitude (if first param is latitude)
     * @return void
     */
    public function removeMarker(float|array $identifier, ?float $longitude = null): void
    {
        $coords = is_array($identifier)
            ? $identifier
            : ['lat' => $identifier, 'lng' => $longitude];

        $this->dispatch('map-remove-marker', $coords);
    }

    /**
     * Clear all markers from the map.
     * 
     * Note: This requires the LivewireMap component to support clearing markers.
     * 
     * @return void
     */
    public function clearMarkers(): void
    {
        $this->dispatch('map-clear-markers');
    }
}
