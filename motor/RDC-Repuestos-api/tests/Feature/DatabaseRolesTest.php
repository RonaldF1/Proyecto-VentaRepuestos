<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Tests\Support\RefreshDatabaseAsOwner;


class DatabaseRolesTest extends TestCase
{
    use RefreshDatabaseAsOwner;
    public function test_application_role_has_no_elevated_privileges(): void
    {
        $role = DB::selectOne("
            SELECT
                rolname,
                rolsuper,
                rolcreatedb,
                rolcreaterole,
                rolbypassrls
            FROM pg_roles
            WHERE rolname = current_user
        ");

        $this->assertNotNull($role);

        $this->assertSame('rdc_app_user', $role->rolname);

        $this->assertSame('0', (string) DB::scalar("
            SELECT CASE
                WHEN rolsuper
                  OR rolcreatedb
                  OR rolcreaterole
                  OR rolbypassrls
                THEN 1 ELSE 0 END
            FROM pg_roles
            WHERE rolname = current_user
        "));
    }

    public function test_application_cannot_create_tables(): void
    {
        $canCreate = DB::scalar("
            SELECT CASE
                WHEN has_schema_privilege(
                    current_user, 'public', 'CREATE'
                )
                THEN 1 ELSE 0 END
        ");

        $this->assertEquals(0, $canCreate);
    }

    public function test_application_does_not_own_database_tables(): void
    {
        // Confirmar que usamos la base de pruebas.
        $this->assertSame(
            'rdc_repuestos_api_testing',
            DB::connection()->getDatabaseName()
        );

        // Tablas pertenecientes al administrador.
        $ownerTables = DB::scalar("
            SELECT COUNT(*)
            FROM pg_tables
            WHERE schemaname = 'public'
              AND tableowner = 'rdc_api_user'
        ");

        // Tablas pertenecientes a la aplicación.
        $appTables = DB::scalar("
            SELECT COUNT(*)
            FROM pg_tables
            WHERE schemaname = 'public'
              AND tableowner = 'rdc_app_user'
        ");

        $this->assertGreaterThan(0, (int) $ownerTables);
        $this->assertSame(0, (int) $appTables);
    }

    public function test_application_can_read_and_write_cache_table(): void
    {
        // Asegurar que no se modifique la base principal.
        $this->assertSame(
            'rdc_repuestos_api_testing',
            DB::connection()->getDatabaseName()
        );

        // Verificar el usuario de la aplicación.
        $this->assertSame(
            'rdc_app_user',
            DB::scalar('SELECT current_user')
        );

        $key = 'role_check_' . bin2hex(random_bytes(8));

        DB::beginTransaction();

        try {
            // 1. INSERTAR
            $inserted = DB::table('cache')->insert([
                'key' => $key,
                'value' => 'prueba',
                'expiration' => time() + 300,
            ]);

            $this->assertTrue($inserted);

            // 2. CONSULTAR
            // Seleccionar la columna value explícitamente.
            $record = DB::selectOne(
                'SELECT "value" AS cache_value
                 FROM public.cache
                 WHERE "key" = ?',
                [$key]
            );

            $this->assertNotNull($record);

            $this->assertSame(
                'prueba',
                $record->cache_value
            );

            // 3. ACTUALIZAR
            $updated = DB::update(
                'UPDATE public.cache
                 SET "value" = ?
                 WHERE "key" = ?',
                ['actualizado', $key]
            );

            $this->assertSame(1, $updated);

            // Comprobar el valor actualizado.
            $record = DB::selectOne(
                'SELECT "value" AS cache_value,
                        ctid::text AS row_location
                 FROM public.cache
                 WHERE "key" = ?',
                [$key]
            );

            $this->assertNotNull($record);

            $this->assertSame(
                'actualizado',
                $record->cache_value,
                'Valor obtenido: ' . $record->cache_value .
                ' | Ubicación de fila: ' . $record->row_location
            );

            // 4. ELIMINAR
            $deleted = DB::delete(
                'DELETE FROM public.cache
                 WHERE "key" = ?',
                [$key]
            );

            $this->assertSame(1, $deleted);

            // Confirmar que no existe el registro.
            $remaining = DB::scalar(
                'SELECT COUNT(*)
                 FROM public.cache
                 WHERE "key" = ?',
                [$key]
            );

            $this->assertSame(0, (int) $remaining);

        } finally {
            // Deshacer los cambios de esta prueba.
            DB::rollBack();
        }
    }
}
