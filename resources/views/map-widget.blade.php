{{-- 
    Map Widget View
    
    This view renders a map widget using the LivewireMap component from the core package.
    It integrates with Filament's widget system and provides support for actions/controls.
    
    Available variables:
    - $mapConfig: Complete map configuration (center, zoom, options, markers, actions)
    - $height: Height of the map container
    - $hasBorder: Whether to show a border around the widget
--}}

<x-filament-widgets::widget>
    <x-filament::section
        :heading="$this->getHeading()"
        :header-actions="$this->getCachedHeaderActions()"
    >
        {{-- Map Container --}}
        <div 
            class="relative"
            style="height: {{ $height }}"
            wire:ignore
        >
            {{-- Livewire Map Component from Core Package --}}
            @livewire('livewire-map', [
                'markers' => $mapConfig['markers'],
                'center' => $mapConfig['center'],
                'zoom' => $mapConfig['zoom'],
                'options' => $mapConfig['options'],
                'height' => $height,
            ], key($this->getId() . '-map'))

            {{-- Map Actions/Controls --}}
            @if(count($mapConfig['actions']) > 0)
                <div 
                    class="absolute z-[1000] flex flex-col gap-2"
                    style="
                        top: 10px;
                        {{ config('filament-maps-widgets.actions_position') === 'topleft' ? 'left: 10px;' : '' }}
                        {{ config('filament-maps-widgets.actions_position') === 'topright' ? 'right: 10px;' : '' }}
                        {{ config('filament-maps-widgets.actions_position') === 'bottomleft' ? 'bottom: 10px; left: 10px; top: auto;' : '' }}
                        {{ config('filament-maps-widgets.actions_position') === 'bottomright' ? 'bottom: 10px; right: 10px; top: auto;' : '' }}
                    "
                >
                    @foreach($mapConfig['actions'] as $action)
                        {!! $action->render() !!}
                    @endforeach
                </div>
            @endif

            {{-- Loading Overlay (optional) --}}
            @if($isLoading ?? false)
                <div 
                    class="absolute inset-0 z-[999] flex items-center justify-center bg-white/80 dark:bg-gray-900/80"
                    wire:loading
                >
                    <x-filament::loading-indicator class="h-8 w-8" />
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>