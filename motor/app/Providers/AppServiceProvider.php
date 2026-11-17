<?php

namespace App\Providers;

use App\Conectores\ConectorInterface;
use App\Conectores\ShippoConector;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ConectorInterface::class, function ($app) {
            $proveedor = config('services.conector.proveedor', 'shippo');

            return match ($proveedor) {
                'shippo' => new ShippoConector(),
                default => throw new \RuntimeException("Proveedor de conector '{$proveedor}' no soportado."),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Date::use(CarbonImmutable::class);
        Model::shouldBeStrict(! $this->app->environment('production'));
    }
}
