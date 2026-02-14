<?php

namespace LBCDev\FilamentMapsWidgets\Tests;

use Filament\FilamentServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use LBCDev\FilamentMapsWidgets\FilamentMapsWidgetsServiceProvider;
use LBCDev\LivewireMaps\LivewireMapsServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn(string $modelName) => 'LBCDev\\FilamentMapsWidgets\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            FilamentServiceProvider::class,
            LivewireMapsServiceProvider::class,
            FilamentMapsWidgetsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');

        config()->set('filament-maps-widgets.default_center', [
            'lat' => 0,
            'lng' => 0,
        ]);

        config()->set('filament-maps-widgets.default_zoom', 10);
    }
}
