<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Bitacora;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BitacoraController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $detalles = [];

        $permitidos = [
            'sistema' => ['tienda', 'conector', 'ia', 'heredado'],
            'estado' => ['exito', 'fallo', 'duplicado', 'firma_invalida'],
            'direccion' => ['entrante', 'saliente'],
        ];

        foreach ($permitidos as $campo => $valores) {
            if ($request->has($campo)) {
                $valor = $request->query($campo);
                if (!in_array($valor, $valores, true)) {
                    $detalles[] = [
                        'campo' => $campo,
                        'mensaje' => 'Debe ser uno de: ' . implode(', ', $valores) . '.',
                    ];
                }
            }
        }

        foreach (['desde', 'hasta'] as $campo) {
            if ($request->has($campo)) {
                $valor = $request->query($campo);
                try {
                    Carbon::parse($valor);
                } catch (\Throwable $e) {
                    $detalles[] = [
                        'campo' => $campo,
                        'mensaje' => 'Debe ser una fecha y hora ISO 8601.',
                    ];
                }
            }
        }

        if (!empty($detalles)) {
            return $this->errorResponse('validacion', 'Los datos de la petición no son válidos.', $detalles, 422);
        }

        $query = Bitacora::query()->orderBy('fecha', 'desc')->orderBy('id', 'desc');

        foreach (['sistema', 'estado', 'direccion', 'correlacion'] as $campo) {
            if ($request->filled($campo)) {
                $query->where($campo, $request->query($campo));
            }
        }

        if ($request->filled('desde')) {
            $query->where('fecha', '>=', Carbon::parse($request->query('desde')));
        }

        if ($request->filled('hasta')) {
            $query->where('fecha', '<=', Carbon::parse($request->query('hasta')));
        }

        return $this->paginar($request, $query, '/bitacora', fn(Bitacora $b) => $b->toContractArray());
    }
}
