
<?php

// ETAPA 4 - Pruebas del contrato de la API
// Todas se ejecutan localmente, sin internet ni consultas a BD.

// 1. Validacion del SKU
it('rechaza un SKU que supera los 100 caracteres', function () {
    $sku = str_repeat('A', 101);

    $this->getJson('/api/v1/productos?sku=' . $sku)
        ->assertStatus(422)
        ->assertJsonPath('error.codigo', 'validacion')
        ->assertJsonPath('error.detalles.0.campo', 'sku');
});

// 2. Pagina invalida
it('rechaza una pagina igual a cero', function () {
    $this->getJson('/api/v1/productos?pagina=0')
        ->assertStatus(422)
        ->assertJsonPath('error.codigo', 'validacion')
        ->assertJsonPath('error.detalles.0.campo', 'pagina');
});

// 3. Cantidad por pagina fuera del rango
it('rechaza solicitar mas de 100 productos por pagina', function () {
    $this->getJson('/api/v1/productos?por_pagina=101')
        ->assertStatus(422)
        ->assertJsonPath('error.codigo', 'validacion')
        ->assertJsonPath('error.detalles.0.campo', 'por_pagina');
});

// 4. ID de pedido invalido
it('devuelve 404 para un ID de pedido no numerico', function () {
    $this->getJson('/api/v1/pedidos/abc')
        ->assertStatus(404)
        ->assertJsonPath('error.codigo', 'no_encontrado');
});

// 5. Sistema de bitacora invalido
it('rechaza un sistema de bitacora no permitido', function () {
    $this->getJson('/api/v1/bitacora?sistema=desconocido')
        ->assertStatus(422)
        ->assertJsonPath('error.codigo', 'validacion')
        ->assertJsonPath('error.detalles.0.campo', 'sistema');
});

// 6. Estado de bitacora invalido
it('rechaza un estado de bitacora no permitido', function () {
    $this->getJson('/api/v1/bitacora?estado=incorrecto')
        ->assertStatus(422)
        ->assertJsonPath('error.codigo', 'validacion')
        ->assertJsonPath('error.detalles.0.campo', 'estado');
});

// 7. Direccion de bitacora invalida
it('rechaza una direccion de bitacora no permitida', function () {
    $this->getJson('/api/v1/bitacora?direccion=desconocida')
        ->assertStatus(422)
        ->assertJsonPath('error.codigo', 'validacion')
        ->assertJsonPath('error.detalles.0.campo', 'direccion');
});
