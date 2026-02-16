<?php

namespace LBCDev\FilamentMapsWidgets\Tests\Unit;

use Filament\Widgets\Widget;
use LBCDev\FilamentMapsWidgets\Tests\TestCase;
use LBCDev\FilamentMapsWidgets\Widgets\MapWidget;
use LBCDev\MapGeometries\Marker;
use LBCDev\MapGeometries\MarkerCollection;

class MapWidgetTest extends TestCase
{
    public function test_it_extends_filament_widget(): void
    {
        $widget = new TestMapWidget();

        $this->assertInstanceOf(Widget::class, $widget);
    }

    public function test_it_renders_correct_view(): void
    {
        $widget = new TestMapWidget();

        // Test that the widget can render without errors
        // The view will be resolved by Filament's Widget class
        $this->assertIsString($widget->render()->name());
        $this->assertStringContainsString('map-widget', $widget->render()->name());
    }

    public function test_it_has_default_height(): void
    {
        $widget = new TestMapWidget();

        $this->assertEquals('500px', $widget->height);
    }

    public function test_it_can_have_custom_height(): void
    {
        $widget = new TestMapWidgetWithCustomHeight();

        $this->assertEquals('700px', $widget->height);
    }

    public function test_it_returns_default_center_from_config(): void
    {
        $widget = new TestMapWidget();

        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getMapCenter');
        $method->setAccessible(true);
        $center = $method->invoke($widget);

        $this->assertIsArray($center);
        $this->assertArrayHasKey('lat', $center);
        $this->assertArrayHasKey('lng', $center);
        $this->assertEquals(0, $center['lat']);
        $this->assertEquals(0, $center['lng']);
    }

    public function test_it_can_have_custom_center(): void
    {
        $widget = new TestMapWidgetWithCustomCenter();

        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getMapCenter');
        $method->setAccessible(true);
        $center = $method->invoke($widget);

        $this->assertEquals(40.7128, $center['lat']);
        $this->assertEquals(-74.0060, $center['lng']);
    }

    public function test_it_returns_default_zoom_from_config(): void
    {
        $widget = new TestMapWidget();

        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getMapZoom');
        $method->setAccessible(true);
        $zoom = $method->invoke($widget);

        $this->assertEquals(10, $zoom);
    }

    public function test_it_can_have_custom_zoom(): void
    {
        $widget = new TestMapWidgetWithCustomZoom();

        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getMapZoom');
        $method->setAccessible(true);
        $zoom = $method->invoke($widget);

        $this->assertEquals(15, $zoom);
    }

    public function test_it_returns_map_options_from_config(): void
    {
        $widget = new TestMapWidget();

        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getMapOptions');
        $method->setAccessible(true);
        $options = $method->invoke($widget);

        $this->assertIsArray($options);
        $this->assertArrayHasKey('scrollWheelZoom', $options);
        $this->assertArrayHasKey('dragging', $options);
        $this->assertTrue($options['scrollWheelZoom']);
        $this->assertTrue($options['dragging']);
    }

    public function test_it_can_have_custom_map_options(): void
    {
        $widget = new TestMapWidgetWithCustomOptions();

        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getMapOptions');
        $method->setAccessible(true);
        $options = $method->invoke($widget);

        $this->assertArrayHasKey('minZoom', $options);
        $this->assertEquals(5, $options['minZoom']);
    }

    public function test_it_returns_markers_from_get_markers_method(): void
    {
        $widget = new TestMapWidget();

        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getMarkers');
        $method->setAccessible(true);
        $markers = $method->invoke($widget);

        $this->assertInstanceOf(MarkerCollection::class, $markers);
        $this->assertCount(2, $markers);
    }

    public function test_it_returns_empty_actions_by_default(): void
    {
        $widget = new TestMapWidget();

        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getActions');
        $method->setAccessible(true);
        $actions = $method->invoke($widget);

        $this->assertIsArray($actions);
        $this->assertEmpty($actions);
    }

    public function test_it_builds_complete_map_config(): void
    {
        $widget = new TestMapWidget();

        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getMapConfig');
        $method->setAccessible(true);
        $config = $method->invoke($widget);

        $this->assertIsArray($config);
        $this->assertArrayHasKey('center', $config);
        $this->assertArrayHasKey('zoom', $config);
        $this->assertArrayHasKey('options', $config);
        $this->assertArrayHasKey('markers', $config);
        $this->assertArrayHasKey('actions', $config);
    }

    public function test_it_provides_view_data(): void
    {
        $widget = new TestMapWidget();

        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getViewData');
        $method->setAccessible(true);
        $viewData = $method->invoke($widget);

        $this->assertIsArray($viewData);
        $this->assertArrayHasKey('mapConfig', $viewData);
        $this->assertArrayHasKey('height', $viewData);
        $this->assertArrayHasKey('hasBorder', $viewData);
        $this->assertEquals('500px', $viewData['height']);
    }

    public function test_it_can_be_viewed_by_default(): void
    {
        $this->assertTrue(TestMapWidget::canView());
    }

    public function test_it_returns_null_heading_by_default(): void
    {
        $widget = new TestMapWidget();

        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getHeading');
        $method->setAccessible(true);
        $heading = $method->invoke($widget);

        $this->assertNull($heading);
    }

    public function test_it_can_have_custom_heading(): void
    {
        $widget = new TestMapWidgetWithHeading();

        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getHeading');
        $method->setAccessible(true);
        $heading = $method->invoke($widget);

        $this->assertEquals('Test Map', $heading);
    }

    public function test_it_handles_marker_collection(): void
    {
        $widget = new TestMapWidget();

        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getMarkers');
        $method->setAccessible(true);
        $markers = $method->invoke($widget);

        $this->assertInstanceOf(MarkerCollection::class, $markers);
        $this->assertCount(2, $markers);

        // Verify markers can be iterated
        $count = 0;
        foreach ($markers as $marker) {
            $this->assertInstanceOf(Marker::class, $marker);
            $count++;
        }
        $this->assertEquals(2, $count);
    }

    public function test_it_handles_array_of_markers(): void
    {
        $widget = new TestMapWidgetWithArrayMarkers();

        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getMarkers');
        $method->setAccessible(true);
        $markers = $method->invoke($widget);

        $this->assertIsArray($markers);
        $this->assertCount(1, $markers);
        $this->assertInstanceOf(Marker::class, $markers[0]);
    }
}

// Test Helper Classes

class TestMapWidget extends MapWidget
{
    protected function getMarkers(): array|MarkerCollection
    {
        $markers = new MarkerCollection();

        // Marker::make(latitude, longitude, label)
        $markers->add(
            Marker::make(40.7128, -74.0060, 'New York')
                ->tooltip('New York City')
        );

        $markers->add(
            Marker::make(51.5074, -0.1278, 'London')
                ->tooltip('London, UK')
        );

        return $markers;
    }
}

class TestMapWidgetWithCustomHeight extends TestMapWidget
{
    public string $height = '700px';
}

class TestMapWidgetWithCustomCenter extends TestMapWidget
{
    protected function getMapCenter(): array
    {
        return ['lat' => 40.7128, 'lng' => -74.0060];
    }
}

class TestMapWidgetWithCustomZoom extends TestMapWidget
{
    protected function getMapZoom(): int
    {
        return 15;
    }
}

class TestMapWidgetWithCustomOptions extends TestMapWidget
{
    protected function getMapOptions(): array
    {
        return array_merge(parent::getMapOptions(), [
            'minZoom' => 5,
        ]);
    }
}

class TestMapWidgetWithHeading extends TestMapWidget
{
    protected function getHeading(): ?string
    {
        return 'Test Map';
    }
}

class TestMapWidgetWithArrayMarkers extends TestMapWidget
{
    protected function getMarkers(): array
    {
        return [
            Marker::make(40.7128, -74.0060, 'New York')
                ->tooltip('NYC'),
        ];
    }
}
