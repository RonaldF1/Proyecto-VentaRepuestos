<?php

namespace Tests\Support;

use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;

trait RefreshDatabaseAsOwner
{
    use RefreshDatabase {
        migrateFreshUsing as private frameworkMigrateFreshUsing;
    }

    protected function migrateFreshUsing(): array
    {
        $baseApp = config('database.connections.pgsql.database');
        $baseAdmin = config('database.connections.pgsql_admin.database');

        // Evitar reconstruir la base de datos principal.
        if (
            ! app()->environment('testing') ||
            $baseApp !== 'rdc_repuestos_api_testing' ||
            $baseAdmin !== 'rdc_repuestos_api_testing' ||
            config('database.connections.pgsql.url') ||
            config('database.connections.pgsql_admin.url')
        ) {
            throw new RuntimeException(
                'Seguridad: las conexiones deben utilizar la base de pruebas.'
            );
        }

        return array_merge(
            $this->frameworkMigrateFreshUsing(),
            ['--database' => 'pgsql_admin']
        );
    }
}
