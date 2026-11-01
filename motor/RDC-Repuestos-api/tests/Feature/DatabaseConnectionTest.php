<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseConnectionTest extends TestCase
{
    public function test_default_connection_uses_postgresql(): void
    {
        $this->assertSame(
            'pgsql',
            DB::connection()->getDriverName()
        );
    }

    public function test_database_accepts_queries(): void
    {
        $resultado = DB::scalar('SELECT 1');

        $this->assertSame(1, (int) $resultado);
    }
}

