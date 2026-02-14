# 📊 ANÁLISIS: Migración del Paquete Widgets

**Fecha**: 14 de febrero de 2026  
**Objetivo**: Refactorizar y migrar el código actual de widgets a la nueva arquitectura del monorepo

---

## 🔍 SITUACIÓN ACTUAL

### Código Existente a Migrar

Actualmente tienes **2 widgets principales** en tu aplicación:

1. **`NegociosMapWithFilters`** (17 KB, 190 líneas)
   - Widget "wrapper" que contiene filtros + mapa
   - Gestiona formulario de filtros (localidad, zona, categoría, suscripción)
   - Renderiza internamente `NegociosMap`
   - Vista: `negocios-map-with-filters.blade.php`

2. **`NegociosMap`** (277 líneas)
   - Extiende `MapWidget` de `webbingbrasil/filament-maps`
   - Funcionalidades:
     - Renderiza markers con colores según estado suscripción
     - Popups personalizados con botones de acción
     - Listeners Livewire (`cambiarSuscripcion`, `showEditNegocio`)
     - Configuración dinámica del mapa (center, zoom, bounds)
     - Integración con modelos Eloquent (Negocio, Suscripción)
   - Vista antigua: `negocios-map.blade.php` (Alpine.js + Leaflet básico)

### Dependencias Actuales

```php
// Del código actual
use Webbingbrasil\FilamentMaps\Marker;
use Webbingbrasil\FilamentMaps\Actions;
use Webbingbrasil\FilamentMaps\Widgets\MapWidget;
use Webbingbrasil\FilamentMaps\Actions\ZoomAction;
```

### Estado de la Nueva Arquitectura

✅ **Core (`livewire-maps-core`)**: Completo
- `LivewireMap` component con soporte single/multi marker
- Integración Alpine.js + Leaflet
- 13 tests unitarios

✅ **Geometries (`map-geometries`)**: Completo
- `Marker` class con Fluent API
- `MarkerCollection` con Iterator/Countable
- 36 tests unitarios

⚠️ **Fields (`filament-maps-fields`)**: Estructura básica
- Solo ServiceProvider, sin componentes

❌ **Widgets (`filament-maps-widgets`)**: Vacío
- Repositorio inicializado pero sin código

---

## 🎯 DIFERENCIAS CLAVE: Arquitectura Antigua vs Nueva

### Paquete `webbingbrasil/filament-maps`

**Características:**
- Widget base: `MapWidget`
- Sistema de Actions (ZoomAction, etc.)
- Clase `Marker` propia
- Integración directa con Filament

**Limitaciones identificadas:**
- Acoplamiento fuerte con Filament
- No hay separación entre lógica de mapa y widget
- Sin soporte modular para diferentes tipos de geometrías

### Nueva Arquitectura (Tu Sistema)

**Ventajas:**
- ✅ Separación de responsabilidades (Core, Geometries, Fields, Widgets)
- ✅ `Marker` reutilizable y testeable en `map-geometries`
- ✅ `LivewireMap` component en Core (independiente de Filament)
- ✅ Flexibilidad para diferentes contextos (Livewire standalone, Filament Fields, Filament Widgets)

---

## 📋 ANÁLISIS DE FUNCIONALIDADES A MIGRAR

### 1. Funcionalidades del `MapWidget` (webbingbrasil)

| Funcionalidad | Estado en Nueva Arquitectura | Acción Requerida |
|---------------|------------------------------|------------------|
| `getMapCenter()` | ✅ Existe en `LivewireMap` | Reutilizar |
| `getMapZoom()` | ✅ Existe en `LivewireMap` | Reutilizar |
| `getMapOptions()` | ✅ Existe en `LivewireMap` | Adaptar maxBounds |
| `getMarkers()` | ✅ Via `MarkerCollection` | Refactorizar lógica de negocio |
| `getActions()` | ❌ No existe | **Implementar sistema de acciones** |
| `centerTo()` | ✅ Existe en `LivewireMap` | Reutilizar |

### 2. Funcionalidades Específicas de `NegociosMap`

| Funcionalidad | Complejidad | Notas |
|---------------|-------------|-------|
| Filtrado dinámico | Media | Delegar a `NegociosMapWithFilters` |
| Listeners Livewire | Baja | Mantener en widget |
| Popups personalizados | Media | Usar `Marker::popup()` existente |
| Cambio de suscripción | Alta | **Lógica de negocio - mantener en aplicación** |
| Colores dinámicos | Baja | `Marker::color()` soporta esto |
| Cache invalidation | Alta | **Lógica de negocio - no del paquete** |

### 3. Funcionalidades de `NegociosMapWithFilters`

| Funcionalidad | Estrategia |
|---------------|-----------|
| Formulario Filament | ✅ Mantener - es específico de la aplicación |
| Renderizar widget hijo | ✅ Adaptar al nuevo `MapWidget` |
| `clearFilters()` | ✅ Mantener |
| Listeners padre-hijo | ✅ Mantener patrón Livewire |

---

## 🏗️ ARQUITECTURA PROPUESTA PARA WIDGETS

### Estructura del Paquete `filament-maps-widgets`

```
packages/widgets/
├── src/
│   ├── FilamentMapsWidgetsServiceProvider.php
│   ├── Widgets/
│   │   └── MapWidget.php                    # ← Widget base
│   ├── Actions/
│   │   ├── Action.php                       # ← Clase base abstracta
│   │   ├── ZoomAction.php
│   │   ├── FullscreenAction.php
│   │   └── LocateAction.php
│   ├── Contracts/
│   │   ├── HasActions.php
│   │   └── HasMapConfiguration.php
│   └── Concerns/
│       ├── InteractsWithMarkers.php
│       └── InteractsWithMapOptions.php
├── resources/
│   └── views/
│       ├── map-widget.blade.php             # ← Vista base del widget
│       └── components/
│           └── actions/
│               └── action-button.blade.php
├── tests/
│   ├── Unit/
│   │   ├── MapWidgetTest.php
│   │   └── Actions/
│   │       └── ZoomActionTest.php
│   └── Feature/
│       └── MapWidgetRenderingTest.php
├── config/
│   └── filament-maps-widgets.php
├── composer.json
└── README.md
```

---

## 🔄 PLAN DE ACCIÓN DETALLADO

### FASE 1: Setup Inicial del Paquete Widgets (1-2 días) ✅

#### Paso 1.1: Estructura Base
```bash
# En el repo filament-maps-widgets
mkdir -p src/{Widgets,Actions,Contracts,Concerns}
mkdir -p resources/views/{components/actions}
mkdir -p tests/{Unit,Feature}
mkdir -p config
```

#### Paso 1.2: Composer Configuration
```json
{
  "name": "lbcdev/filament-maps-widgets",
  "description": "Filament widgets for interactive maps",
  "type": "library",
  "require": {
    "php": "^8.1",
    "filament/filament": "^3.0",
    "lbcdev/livewire-maps-core": "^2.0",
    "lbcdev/map-geometries": "^1.0"
  },
  "autoload": {
    "psr-4": {
      "LBCDev\\FilamentMapsWidgets\\": "src/"
    }
  }
}
```

#### Paso 1.3: ServiceProvider
- Auto-discovery
- Registrar vistas
- Publicar configuración
- Cargar assets si es necesario

---

### FASE 2: Implementar Widget Base (2-3 días)

#### Paso 2.1: Crear `MapWidget.php`

**Responsabilidades:**
- Extender `Filament\Widgets\Widget`
- Usar `LivewireMap` del paquete Core
- Proveer API para configurar mapa
- Sistema de acciones

**Diseño de API:**

```php
namespace LBCDev\FilamentMapsWidgets\Widgets;

use Filament\Widgets\Widget;
use LBCDev\MapGeometries\MarkerCollection;

abstract class MapWidget extends Widget
{
    protected static string $view = 'filament-maps-widgets::map-widget';
    
    protected int|string|array $columnSpan = 'full';
    
    // Configuración del mapa
    public string $height = '500px';
    protected bool $hasBorder = false;
    
    // Métodos abstractos que el usuario debe implementar
    abstract protected function getMarkers(): array|MarkerCollection;
    
    // Métodos con defaults sobrescribibles
    protected function getMapCenter(): array
    {
        return config('filament-maps-widgets.default_center', [
            'lat' => 0,
            'lng' => 0
        ]);
    }
    
    protected function getMapZoom(): int
    {
        return config('filament-maps-widgets.default_zoom', 10);
    }
    
    protected function getMapOptions(): array
    {
        return [
            'scrollWheelZoom' => true,
            'dragging' => true,
        ];
    }
    
    protected function getActions(): array
    {
        return [];
    }
    
    // Método que compone la configuración final
    protected function getMapConfig(): array
    {
        return [
            'center' => $this->getMapCenter(),
            'zoom' => $this->getMapZoom(),
            'options' => $this->getMapOptions(),
            'actions' => $this->getActions(),
        ];
    }
}
```

#### Paso 2.2: Vista `map-widget.blade.php`

**Integración con Core:**

```blade
<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Usar el componente LivewireMap del paquete Core --}}
        @livewire('livewire-map', [
            'markers' => $this->getMarkers(),
            'center' => $this->getMapCenter(),
            'zoom' => $this->getMapZoom(),
            'options' => $this->getMapOptions(),
        ])
        
        {{-- Renderizar acciones si existen --}}
        @if(count($this->getActions()) > 0)
            <div class="absolute top-4 right-4 flex flex-col gap-2">
                @foreach($this->getActions() as $action)
                    {!! $action->render() !!}
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
```

---

### FASE 3: Sistema de Acciones (2 días)

#### Paso 3.1: Clase Base `Action.php`

```php
namespace LBCDev\FilamentMapsWidgets\Actions;

abstract class Action
{
    protected string $name;
    protected string $position = 'topright';
    protected ?string $icon = null;
    protected ?string $label = null;
    
    public static function make(string $name = null): static
    {
        return new static($name);
    }
    
    public function __construct(string $name = null)
    {
        $this->name = $name ?? static::getDefaultName();
    }
    
    abstract protected static function getDefaultName(): string;
    
    public function position(string $position): static
    {
        $this->position = $position;
        return $this;
    }
    
    abstract public function render(): string;
}
```

#### Paso 3.2: Implementar `ZoomAction`

```php
namespace LBCDev\FilamentMapsWidgets\Actions;

class ZoomAction extends Action
{
    protected static function getDefaultName(): string
    {
        return 'zoom';
    }
    
    public function render(): string
    {
        return view('filament-maps-widgets::components.actions.zoom', [
            'position' => $this->position,
        ])->render();
    }
}
```

---

### FASE 4: Migración del Código Existente (3-4 días)

#### Paso 4.1: Refactorizar `NegociosMap`

**ANTES (webbingbrasil):**
```php
class NegociosMap extends MapWidget  // webbingbrasil
{
    public function getMarkers(): array
    {
        // ... lógica compleja con Eloquent
        return $markers;
    }
}
```

**DESPUÉS (nueva arquitectura):**
```php
namespace App\Filament\Widgets;

use LBCDev\FilamentMapsWidgets\Widgets\MapWidget;
use LBCDev\MapGeometries\Marker;
use LBCDev\MapGeometries\MarkerCollection;

class NegociosMap extends MapWidget
{
    public ?Collection $negocios = null;
    public array $filters = [];
    
    protected $listeners = [
        'refreshNegocios' => 'refreshNegociosMap',
        'cambiarSuscripcion' => 'cambiarSuscripcion',
    ];
    
    public function mount(?Collection $negocios = null, array $filters = []): void
    {
        $this->negocios = $negocios ?? collect();
        $this->filters = $filters;
    }
    
    protected function getMarkers(): MarkerCollection
    {
        $markers = new MarkerCollection();
        
        foreach ($this->negocios as $negocio) {
            $status = $negocio->suscripcion?->status;
            $color = $status == EstadoSuscripcion::ACTIVE ? 'green' : 'red';
            
            $markers->add(
                Marker::make($negocio->id)
                    ->lat($negocio->ubicacion['latitud'])
                    ->lng($negocio->ubicacion['longitud'])
                    ->color($color)
                    ->popup($this->buildPopup($negocio))
            );
        }
        
        return $markers;
    }
    
    protected function getMapCenter(): array
    {
        $localidad = $this->getLocalidadEnFiltro();
        
        return $localidad 
            ? [
                'lat' => $localidad->ubicacion['latitud'],
                'lng' => $localidad->ubicacion['longitud'],
            ]
            : config('filament-maps-widgets.default_center');
    }
    
    protected function getMapOptions(): array
    {
        $options = parent::getMapOptions();
        
        // Calcular maxBounds dinámicamente
        $localidades = Localidad::all();
        if ($localidades->count() > 0) {
            $options['maxBounds'] = $this->calculateMaxBounds($localidades);
        }
        
        return $options;
    }
    
    protected function getActions(): array
    {
        return [
            ZoomAction::make()->position('topright'),
        ];
    }
    
    // Métodos privados de lógica de negocio
    private function buildPopup(Negocio $negocio): string
    {
        $status = $negocio->suscripcion->status;
        // ... resto de lógica del popup
        return $html;
    }
    
    // Listeners Livewire
    public function cambiarSuscripcion(int $negocioId)
    {
        // ... lógica de negocio
        $this->refresh();
    }
}
```

#### Paso 4.2: Mantener `NegociosMapWithFilters` sin cambios

Este widget NO necesita migración porque:
- Es específico de tu aplicación
- Solo gestiona filtros y delega al `NegociosMap`
- Solo necesita actualizar la referencia al nuevo `NegociosMap`

---

### FASE 5: Testing (2 días)

#### Tests Unitarios

```php
// tests/Unit/MapWidgetTest.php
it('renders with default configuration', function () {
    $widget = new TestMapWidget();
    
    expect($widget->getMapCenter())->toBe(['lat' => 0, 'lng' => 0]);
    expect($widget->getMapZoom())->toBe(10);
});

it('accepts custom markers', function () {
    $widget = new TestMapWidget();
    $markers = $widget->getMarkers();
    
    expect($markers)->toBeInstanceOf(MarkerCollection::class);
});
```

#### Tests de Integración

```php
// tests/Feature/MapWidgetRenderingTest.php
it('renders map widget in Filament panel', function () {
    livewire(TestMapWidget::class)
        ->assertSee('id="map"')
        ->assertViewHas('markers');
});
```

---

### FASE 6: Documentación (1-2 días)

#### README.md completo con:
- Instalación
- Quickstart
- API reference
- Ejemplos de uso
- Migración desde webbingbrasil

---

## 📊 COMPARATIVA: Antes vs Después

| Aspecto | Antigua (webbingbrasil) | Nueva (lbcdev) |
|---------|------------------------|----------------|
| **Acoplamiento** | Alto (todo en un paquete) | Bajo (modular) |
| **Testabilidad** | Baja (dependencias acopladas) | Alta (separación de concerns) |
| **Reutilización** | Solo en Filament | Core reutilizable fuera de Filament |
| **Geometrías** | Solo `Marker` | Extensible (Polyline, Polygon...) |
| **Mantenimiento** | Difícil (sin separación) | Fácil (paquetes independientes) |
| **Flexibilidad** | Limitada | Alta (4 paquetes modulares) |

---

## ⚠️ CONSIDERACIONES IMPORTANTES

### 1. **Separación de Responsabilidades**

❌ **NO incluir en el paquete Widgets:**
- Lógica de negocio específica (cambiar suscripciones, cache invalidation)
- Queries Eloquent específicas de la aplicación
- Validaciones de dominio

✅ **SÍ incluir en el paquete Widgets:**
- Widget base extensible
- Sistema de acciones reutilizable
- Configuración del mapa
- Integración con LivewireMap del Core

### 2. **Compatibilidad con la Aplicación Existente**

La migración debe ser **incremental**:

1. **Paso 1**: Crear paquete Widgets con API similar a webbingbrasil
2. **Paso 2**: Testear paquete de forma aislada
3. **Paso 3**: Migrar `NegociosMap` en la aplicación
4. **Paso 4**: Verificar que todo funciona igual
5. **Paso 5**: Remover dependencia de webbingbrasil

### 3. **Retrocompatibilidad de API**

El nuevo `MapWidget` debe ofrecer una API **similar** (pero no idéntica) a webbingbrasil:

```php
// webbingbrasil (antiguo)
protected function getMapConfig(): array
{
    return [
        'center' => [...],
        'zoom' => 14,
        'mapOptions' => [...],
    ];
}

// lbcdev (nuevo) - similar pero mejorado
protected function getMapCenter(): array { return [...]; }
protected function getMapZoom(): int { return 14; }
protected function getMapOptions(): array { return [...]; }
```

---

## 🎯 PRIORIDADES RECOMENDADAS

### 🔥 CRÍTICAS (Semana 1)
1. ✅ Estructura básica del paquete Widgets
2. ✅ `MapWidget` base funcional
3. ✅ Integración con `LivewireMap` (Core)
4. ✅ Migrar `NegociosMap` a nueva arquitectura

### ⚡ IMPORTANTES (Semana 2)
5. Sistema de Actions básico (ZoomAction)
6. Tests unitarios de `MapWidget`
7. Documentación básica (README)

### 💡 OPCIONALES (Semana 3+)
8. Actions adicionales (Fullscreen, Locate)
9. Tests de integración con Filament
10. Documentación avanzada y ejemplos

---

## 📝 CHECKLIST DE MIGRACIÓN

### Paquete Widgets
- [ ] Crear estructura de directorios
- [ ] Configurar `composer.json`
- [ ] Crear `ServiceProvider`
- [ ] Implementar `MapWidget` base
- [ ] Sistema de Actions (ZoomAction mínimo)
- [ ] Vista `map-widget.blade.php`
- [ ] Tests unitarios (>80% coverage)
- [ ] README completo

### Aplicación (mia-webapp)
- [ ] Actualizar `composer.json` (añadir `lbcdev/filament-maps-widgets`)
- [ ] Refactorizar `NegociosMap`
- [ ] Verificar `NegociosMapWithFilters` (debería funcionar sin cambios)
- [ ] Ejecutar tests
- [ ] Verificar funcionalidad en navegador
- [ ] Remover dependencia `webbingbrasil/filament-maps`

### Monorepo
- [ ] Añadir submodule `packages/widgets`
- [ ] Actualizar documentación global
- [ ] CI/CD para nuevo paquete
- [ ] Actualizar `03-CHECKLIST_PROGRESO.md`

---

## 🚀 PRÓXIMOS PASOS INMEDIATOS

### Paso 1: Confirmar el Plan
- Revisar este análisis
- Ajustar prioridades si es necesario
- Confirmar enfoque modular

### Paso 2: Setup del Paquete (Hoy)
```bash
cd packages/widgets
# Crear estructura básica
# Configurar composer.json
# Crear ServiceProvider
```

### Paso 3: Implementar MapWidget Base (Mañana)
- Extender `Filament\Widgets\Widget`
- Integrar con `LivewireMap`
- API pública inicial

### Paso 4: Primera Migración (Día 3)
- Migrar `NegociosMap` como caso de prueba
- Verificar que funciona igual que antes

---

## 💬 PREGUNTAS PARA CONFIRMAR

1. **¿Quieres mantener 100% de compatibilidad con la API de webbingbrasil?**
   - Ventaja: Migración más fácil
   - Desventaja: Menos flexibilidad en diseño

2. **¿Prefieres migrar incrementalmente o "big bang"?**
   - Incremental: Primero Widgets, luego Fields
   - Big Bang: Ambos paquetes a la vez

3. **¿Qué nivel de prioridad tiene el sistema de Actions?**
   - Alta: Implementar ZoomAction, FullscreenAction, etc.
   - Media: Solo ZoomAction por ahora
   - Baja: Dejar para después

4. **¿Necesitas soporte para clustering de markers?**
   - Sí: Planificar desde ahora
   - No: Implementar más adelante

---

**¿Confirmamos este plan y empezamos con la Fase 1?** 🚀