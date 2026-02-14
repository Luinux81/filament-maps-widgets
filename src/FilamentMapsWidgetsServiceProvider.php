<?php

namespace LBCDev\FilamentMapsWidgets;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentMapsWidgetsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-maps-widgets';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasViews()
            ->hasConfigFile();
    }

    public function packageBooted(): void
    {
        // Assets publication if needed in the future
        // $this->publishes([
        //     $this->package->basePath('/../resources/dist') => public_path("vendor/{$this->package->shortName()}"),
        // ], "{$this->package->shortName()}-assets");
    }

    public function packageRegistered(): void
    {
        // Register any package services here
    }
}
