<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class TimeTest extends TestCase
{
    private const TIMEZONE = 'America/Costa_Rica';

    public function test_application_uses_costa_rica_timezone(): void
    {
        $this->assertEquals(self::TIMEZONE, config('app.timezone'));
        $this->assertEquals(self::TIMEZONE, date_default_timezone_get());
    }

    public function test_database_accepts_timezone_query(): void
    {
        $timezone = DB::scalar("SHOW timezone;");
        $this->assertNotEmpty($timezone);
    }
}
