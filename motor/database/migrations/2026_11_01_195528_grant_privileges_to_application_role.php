<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql' || !config('database.connections.pgsql_admin')) {
            return;
        }

        $db = DB::connection('pgsql_admin');

        // Permitir el uso del esquema.
        $db->statement(
            'GRANT USAGE ON SCHEMA public TO rdc_app_user'
        );

        // Permisos sobre las tablas existentes.
        $db->statement(
            'GRANT SELECT, INSERT, UPDATE, DELETE
             ON ALL TABLES IN SCHEMA public
             TO rdc_app_user'
        );

        // Permisos sobre las secuencias existentes.
        $db->statement(
            'GRANT USAGE, SELECT
             ON ALL SEQUENCES IN SCHEMA public
             TO rdc_app_user'
        );

        // Permisos para las tablas que se creen después.
        $db->statement(
            'ALTER DEFAULT PRIVILEGES
             FOR ROLE rdc_api_user
             IN SCHEMA public
             GRANT SELECT, INSERT, UPDATE, DELETE
             ON TABLES TO rdc_app_user'
        );

        // Permisos para las secuencias futuras.
        $db->statement(
            'ALTER DEFAULT PRIVILEGES
             FOR ROLE rdc_api_user
             IN SCHEMA public
             GRANT USAGE, SELECT
             ON SEQUENCES TO rdc_app_user'
        );
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql' || !config('database.connections.pgsql_admin')) {
            return;
        }

        $db = DB::connection('pgsql_admin');

        $db->statement(
            'ALTER DEFAULT PRIVILEGES
             FOR ROLE rdc_api_user
             IN SCHEMA public
             REVOKE SELECT, INSERT, UPDATE, DELETE
             ON TABLES FROM rdc_app_user'
        );

        $db->statement(
            'ALTER DEFAULT PRIVILEGES
             FOR ROLE rdc_api_user
             IN SCHEMA public
             REVOKE USAGE, SELECT
             ON SEQUENCES FROM rdc_app_user'
        );

        $db->statement(
            'REVOKE SELECT, INSERT, UPDATE, DELETE
             ON ALL TABLES IN SCHEMA public
             FROM rdc_app_user'
        );

        $db->statement(
            'REVOKE USAGE, SELECT
             ON ALL SEQUENCES IN SCHEMA public
             FROM rdc_app_user'
        );

        $db->statement(
            'REVOKE USAGE ON SCHEMA public
             FROM rdc_app_user'
        );
    }
};
