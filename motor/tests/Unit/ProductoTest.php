
<?php

use Src\Catalogo\Productos\Domain\Entities\Producto;

// Crear un producto de prueba sin usar la base de datos.
function crearProductoParaUnitTest(
    ?int $precio = 6500,
    ?int $existencias = 8,
    ?string $valor = 'Toyota'
): Producto {
    return new Producto(
        id: 1,
        idTienda: 101,
        sku: 'FIL-001',
        nombre: 'Filtro de aceite',
        tipo: 'simple',
        precio: $precio,
        existencias: $existencias,
        categorias: ['Motor', 'Filtros'],
        campoPropioNombre: 'Marca',
        campoPropioValor: $valor,
        sincronizadoEn: '2026-11-23 12:00:00'
    );
}

// PRUEBA UNITARIA 1
it('convierte correctamente los datos de un producto a un arreglo', function () {
    $producto = crearProductoParaUnitTest();
    $datos = $producto->toArray();

    expect($datos['id'])->toBe(1)
        ->and($datos['id_tienda'])->toBe(101)
        ->and($datos['sku'])->toBe('FIL-001')
        ->and($datos['nombre'])->toBe('Filtro de aceite')
        ->and($datos['precio'])->toBe(6500);
});

// PRUEBA UNITARIA 2
it('conserva correctamente los valores nulos de un producto', function () {
    $producto = crearProductoParaUnitTest(
        precio: null,
        existencias: null,
        valor: null
    );

    $datos = $producto->toArray();

    expect($datos['precio'])->toBeNull()
        ->and($datos['existencias'])->toBeNull()
        ->and($datos['campo_propio']['valor'])->toBeNull();
});

// PRUEBA UNITARIA 3
it('mantiene las categorias y el campo personalizado del producto', function () {
    $producto = crearProductoParaUnitTest();
    $datos = $producto->toArray();

    expect($datos['categorias'])->toEqual(['Motor', 'Filtros'])
        ->and($datos['campo_propio'])->toEqual([
            'nombre' => 'Marca',
            'valor' => 'Toyota',
        ]);
});
