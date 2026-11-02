<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocalizationTest extends TestCase
{
    public function test_application_locale_is_configured_to_spanish(): void
    {
        $this->assertEquals('es', config('app.locale'));
    }

    public function test_fallback_locale_is_configured_to_spanish(): void
    {
        $this->assertEquals('es', config('app.fallback_locale'));
    }

    public function test_translation_helper_returns_correct_spanish_validation_message(): void
    {
        $message = __('validation.required', ['attribute' => 'correo']);

        $this->assertEquals('El campo correo es obligatorio.', $message);
    }
}
