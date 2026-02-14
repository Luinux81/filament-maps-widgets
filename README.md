# Filament Maps Widgets

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lbcdev/filament-maps-widgets.svg?style=flat-square)](https://packagist.org/packages/lbcdev/filament-maps-widgets)
[![Total Downloads](https://img.shields.io/packagist/dt/lbcdev/filament-maps-widgets.svg?style=flat-square)](https://packagist.org/packages/lbcdev/filament-maps-widgets)

Interactive map widgets for Filament panels, built on top of [livewire-maps-core](https://github.com/Luinux81/livewire-maps-core) and [map-geometries](https://github.com/Luinux81/map-geometries).

## Installation

You can install the package via composer:

```bash
composer require lbcdev/filament-maps-widgets
```

Optionally, you can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-maps-widgets-config"
```

## Quick Start

Create a map widget by extending `MapWidget`:

```php
namespace App\Filament\Widgets;

use LBCDev\FilamentMapsWidgets\Widgets\MapWidget;
use LBCDev\MapGeometries\Marker;
use LBCDev\MapGeometries\MarkerCollection;

class LocationsMap extends MapWidget
{
    protected function getMarkers(): MarkerCollection
    {
        $markers = new MarkerCollection();
        
        $markers->add(
            Marker::make('home')
                ->lat(40.7128)
                ->lng(-74.0060)
                ->popup('New York City')
        );
        
        return $markers;
    }
    
    protected function getMapCenter(): array
    {
        return ['lat' => 40.7128, 'lng' => -74.0060];
    }
}
```

## Features

- 🗺️ **Easy Integration**: Built specifically for Filament panels
- 🎨 **Customizable**: Configure map center, zoom, options, and more
- 🎯 **Actions System**: Add custom actions/controls to your maps
- 🧩 **Modular**: Built on top of separate core and geometries packages
- ✅ **Well Tested**: Comprehensive test coverage
- 📚 **Documented**: Full API documentation

## Basic Usage

### Simple Map Widget

```php
use LBCDev\FilamentMapsWidgets\Widgets\MapWidget;
use LBCDev\MapGeometries\Marker;

class MyMap extends MapWidget
{
    protected function getMarkers(): array
    {
        return [
            Marker::make('marker-1')
                ->lat(51.505)
                ->lng(-0.09)
                ->popup('London'),
        ];
    }
}
```

### Custom Map Configuration

```php
class MyMap extends MapWidget
{
    protected function getMapCenter(): array
    {
        return ['lat' => 51.505, 'lng' => -0.09];
    }
    
    protected function getMapZoom(): int
    {
        return 13;
    }
    
    protected function getMapOptions(): array
    {
        return [
            'scrollWheelZoom' => true,
            'dragging' => true,
            'minZoom' => 10,
            'maxZoom' => 18,
        ];
    }
}
```

### Dynamic Markers from Database

```php
use App\Models\Location;

class LocationsMap extends MapWidget
{
    protected function getMarkers(): MarkerCollection
    {
        $markers = new MarkerCollection();
        
        Location::all()->each(function ($location) use ($markers) {
            $markers->add(
                Marker::make($location->id)
                    ->lat($location->latitude)
                    ->lng($location->longitude)
                    ->color($location->is_active ? 'green' : 'red')
                    ->popup("<b>{$location->name}</b><br>{$location->address}")
            );
        });
        
        return $markers;
    }
}
```

### Adding Actions

```php
use LBCDev\FilamentMapsWidgets\Actions\ZoomAction;

class MyMap extends MapWidget
{
    protected function getActions(): array
    {
        return [
            ZoomAction::make()
                ->position('topright'),
        ];
    }
}
```

## Configuration

The package comes with sensible defaults, but you can customize everything via the config file:

```php
return [
    'default_center' => [
        'lat' => 0,
        'lng' => 0,
    ],
    'default_zoom' => 10,
    'default_height' => '500px',
    'map_options' => [
        'scrollWheelZoom' => true,
        'dragging' => true,
        // ... more options
    ],
];
```

## Advanced Usage

### Reactive Widgets with Filters

```php
class FilteredMap extends MapWidget
{
    public array $filters = [];
    
    protected $listeners = [
        'filtersUpdated' => 'handleFiltersUpdated',
    ];
    
    public function mount(array $filters = []): void
    {
        $this->filters = $filters;
        parent::mount();
    }
    
    protected function getMarkers(): MarkerCollection
    {
        // Apply filters to your query
        $query = Location::query();
        
        if ($this->filters['category'] ?? null) {
            $query->where('category_id', $this->filters['category']);
        }
        
        // Build markers from filtered results
        // ...
    }
    
    public function handleFiltersUpdated(array $filters): void
    {
        $this->filters = $filters;
        $this->refresh();
    }
}
```

### Custom Height and Styling

```php
class MyMap extends MapWidget
{
    public string $height = '700px';
    
    protected bool $hasBorder = true;
}
```

## Testing

```bash
composer test
```

Or with coverage:

```bash
composer test-coverage
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Luinux81](https://github.com/Luinux81)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Related Packages

This package is part of the LBCDev Maps Suite:

- [livewire-maps-core](https://github.com/Luinux81/livewire-maps-core) - Core Livewire map component
- [map-geometries](https://github.com/Luinux81/map-geometries) - Map geometry classes (Marker, Polyline, etc.)
- [filament-maps-fields](https://github.com/Luinux81/filament-maps-fields) - Map form fields for Filament
- [lbcdev-filament-maps-suite](https://github.com/Luinux81/lbcdev-filament-maps-suite) - Meta-package for all components
