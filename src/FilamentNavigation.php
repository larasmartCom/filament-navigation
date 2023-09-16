<?php

namespace RyanChandler\FilamentNavigation;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Forms;
use Filament\Panel;
use Illuminate\Support\Str;
use RyanChandler\FilamentNavigation\Filament\Resources\NavigationResource;
use RyanChandler\FilamentNavigation\Models\Navigation;

class FilamentNavigation implements Plugin
{
    protected string $model = Navigation::class;

    protected string $resource = NavigationResource::class;

    protected array $itemTypes = [];

    protected array|Closure $extraFields = [];

    public function getId(): string
    {
        return 'navigation';
    }

    /** @param  class-string<\Filament\Resources\Resource>  $resource */
    public function usingResource(string $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    /** @param  class-string<\Illuminate\Database\Eloquent\Model>  $model */
    public function usingModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function itemType(string $name, array|Closure $fields, string $slug = null): static
    {
        $this->itemTypes[$slug ?? Str::slug($name)] = [
            'name' => $name,
            'fields' => $fields,
        ];

        return $this;
    }

    public function withExtraFields(array|Closure $schema): static
    {
        $this->extraFields = $schema;

        return $this;
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([$this->getResource()]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return new static();
    }

    public static function get(): static
    {
        return filament('navigation');
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getResource(): string
    {
        return $this->resource;
    }

    public function getExtraFields(): array|Closure
    {
        return $this->extraFields;
    }

    public function getItemTypes(): array
    {
        return array_merge(
            [
                'external-link' => [
                    'name' => __('filament-navigation::filament-navigation.attributes.external-link'),
                    'fields' => [
                        Forms\Components\Section::make()
                        ->description(__('hints.nav.add_url'))
                        ->icon('heroicon-m-link')
                            ->schema([
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('url')
                                            ->string()
                                            ->hiddenLabel()
                                            ->maxLength(255)
                                            ->placeholder(__('action.add', ['option' => __('label.url')]))
                                            ->suffixIcon('heroicon-m-globe-alt')
                                            ->label(__('filament-navigation::filament-navigation.attributes.url'))
                                            ->required(),
                                        Forms\Components\Radio::make('target')
                                            ->hiddenLabel()
                                            ->label(__('filament-navigation::filament-navigation.attributes.target'))
                                            ->options([
                                                '_self' => __('filament-navigation::filament-navigation.select-options.same-tab'),
                                                '_blank' => __('filament-navigation::filament-navigation.select-options.new-tab'),
                                            ])
                                            ->default('_self')
                                            ->inline(),
                                    ]),
                            ]),
                    ],
                ],
            ],
            $this->itemTypes
        );
    }
}
