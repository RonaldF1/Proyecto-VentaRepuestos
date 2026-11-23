<?php

declare(strict_types=1);

namespace App\Providers;

use App\Conectores\ConectorInterface;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\ServiceProvider;
use Src\Pedidos\Pedidos\Domain\Contracts\ConectorLogisticoPort;
use Src\Pedidos\Pedidos\Domain\Contracts\NormalizadorDireccionPort;
use Src\Pedidos\Pedidos\Infrastructure\Adapters\GeminiNormalizadorAdapter;
use Src\Pedidos\Pedidos\Infrastructure\Adapters\ShippoConectorAdapter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ConectorLogisticoPort::class, function ($app) {
            $proveedor = config('services.conector.proveedor', 'shippo');

            return match ($proveedor) {
                'shippo' => new ShippoConectorAdapter(),
                default => throw new \RuntimeException("Proveedor de conector '{$proveedor}' no soportado."),
            };
        });

        $this->app->bind(ConectorInterface::class, function ($app) {
            return $app->make(ConectorLogisticoPort::class);
        });

        $this->app->bind(NormalizadorDireccionPort::class, function ($app) {
            return new GeminiNormalizadorAdapter();
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