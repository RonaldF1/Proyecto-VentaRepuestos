<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_health_endpoint_responds_correctly(): void
    {
        $response = $this->get('/up');

        $response->assertOk();
    }
}

