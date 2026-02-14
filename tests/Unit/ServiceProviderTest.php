<?php

namespace LBCDev\FilamentMapsWidgets\Tests\Unit;

use LBCDev\FilamentMapsWidgets\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    public function test_it_loads_the_service_provider(): void
    {
        $this->assertTrue(true);
    }

    public function test_it_loads_the_configuration(): void
    {
        $this->assertIsArray(config('filament-maps-widgets'));
        $this->assertArrayHasKey('default_center', config('filament-maps-widgets'));
        $this->assertArrayHasKey('default_zoom', config('filament-maps-widgets'));
    }

    public function test_it_has_correct_default_configuration_values(): void
    {
        $this->assertEquals(0, config('filament-maps-widgets.default_center.lat'));
        $this->assertEquals(0, config('filament-maps-widgets.default_center.lng'));
        $this->assertEquals(10, config('filament-maps-widgets.default_zoom'));
        $this->assertEquals('500px', config('filament-maps-widgets.default_height'));
        $this->assertFalse(config('filament-maps-widgets.has_border'));
    }

    public function test_it_has_map_options_configuration(): void
    {
        $mapOptions = config('filament-maps-widgets.map_options');

        $this->assertIsArray($mapOptions);
        $this->assertArrayHasKey('scrollWheelZoom', $mapOptions);
        $this->assertArrayHasKey('dragging', $mapOptions);
        $this->assertTrue($mapOptions['scrollWheelZoom']);
        $this->assertTrue($mapOptions['dragging']);
    }

    public function it_has_actions_position_configuration(): void
    {
        $this->assertEquals('topright', config('filament-maps-widgets.actions_position'));
    }
}
