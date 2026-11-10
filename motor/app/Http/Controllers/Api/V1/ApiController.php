<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class ApiController extends Controller
{
    protected function errorResponse(string $codigo, string $mensaje, array $detalles = [], int $httpStatus = 400): JsonResponse
    {
        return response()->json([
            'error' => [
                'codigo' => $codigo,
                'mensaje' => $mensaje,
                'detalles' => $detalles,
            ],
        ], $httpStatus);
    }

    protected function paginar(Request $request, $query, string $ruta, callable $transformador): JsonResponse
    {
        $detalles = [];

        // Validar pagina
        $paginaRaw = $request->query('pagina');
        $pagina = 1;
        if ($paginaRaw !== null) {
            if (!ctype_digit((string) $paginaRaw) || (int) $paginaRaw < 1 || (int) $paginaRaw > 1000000) {
                $detalles[] = [
                    'campo' => 'pagina',
                    'mensaje' => 'Debe ser un entero entre 1 y 1000000.',
                ];
            } else {
                $pagina = (int) $paginaRaw;
            }
        }

        // Validar por_pagina
        $porPaginaRaw = $request->query('por_pagina');
        $porPagina = 15;
        if ($porPaginaRaw !== null) {
            if (!ctype_digit((string) $porPaginaRaw) || (int) $porPaginaRaw < 1 || (int) $porPaginaRaw > 100) {
                $detalles[] = [
                    'campo' => 'por_pagina',
                    'mensaje' => 'Debe ser un entero entre 1 y 100.',
                ];
            } else {
                $porPagina = (int) $porPaginaRaw;
            }
        }

        if (!empty($detalles)) {
            return $this->errorResponse('validacion', 'Los datos de la petición no son válidos.', $detalles, 422);
        }

        $total = $query->count();
        $totalPaginas = (int) ceil($total / $porPagina);

        $items = $query->forPage($pagina, $porPagina)->get();
        $datos = $items->map($transformador)->values()->all();

        // Construir URLs de enlace
        $baseUrl = $request->url();
        $enlace = fn($p) => $baseUrl . '?' . http_build_query(array_merge($request->query(), ['pagina' => $p, 'por_pagina' => $porPagina]));

        return response()->json([
            'datos' => $datos,
            'meta' => [
                'pagina' => $pagina,
                'por_pagina' => $porPagina,
                'total' => $total,
                'total_paginas' => $totalPaginas,
            ],
            'enlaces' => [
                'anterior' => $pagina > 1 ? $enlace($pagina - 1) : null,
                'siguiente' => $pagina < $totalPaginas ? $enlace($pagina + 1) : null,
            ],
        ], 200);
    }
}
