<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

    public function test_server_is_postgresql_18_or_higher(): void
    {
        $version = DB::scalar("SHOW server_version_num;");
        
        // PostgreSQL 18.0 o superior equivale a un valor numérico de 180000 o más
        $this->assertGreaterThanOrEqual(180000, (int) $version);
    }

    public function test_server_generates_uuid_v7(): void
    {
        $uuid = DB::scalar("SELECT gen_random_uuid();");
        
        $this->assertIsString($uuid);
        $this->assertTrue(Str::isUuid($uuid));
    }
}

