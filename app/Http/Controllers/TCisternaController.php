<?php

namespace App\Http\Controllers;

use App\Models\TCisterna;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TCisternaController extends Controller
{
     public function index()
{
    $userId = auth()->id();

    $data = DB::table('vista_cisternas_por_usuario')
        ->where('user_id', $userId)
        ->get();

    if ($data->isEmpty()) {
        return response()->json([
            'user_id' => $userId,
            'cisternas' => []
        ]);
    }

    return response()->json([
        'user_id' => $data[0]->user_id,
        'user_name' => $data[0]->user_name,
        'user_email' => $data[0]->user_email,
        'user_role' => $data[0]->user_role,
        'cisternas' => $data->map(function ($c) {
            return [
                'sitio_id' => $c->sitio_id,
                'sitio_nombre' => $c->sitio_nombre,
                'sitio_descripcion' => $c->sitio_descripcion,
            ];
        })->values()
    ]);
}


    public function show($id)
    {
        $cisterna = TCisterna::find($id);
        if (!$cisterna) {
            return response()->json(['message' => 'No encontrada'], 404);
        }
        return response()->json($cisterna);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fecha' => 'required|date',
            'cisterna_id' => 'required|integer',
            'nivel' => 'required|numeric',
            'eth' => 'required|numeric',
        ]);

        $cisterna = TCisterna::create($data);

        return response()->json($cisterna, 201);
    }

    public function ultimoDato($cisterna_id)
    {
        $registro = TCisterna::where('cisterna_id', $cisterna_id)
            ->orderBy('fecha', 'desc')
            ->first();

        if (!$registro) {
            return response()->json(['message' => 'No hay datos'], 404);
        }

        return response()->json($registro);
    }

       public function ultimos7dias($cisterna_id)
    {
        $hace7dias = Carbon::now()->subDays(7);

        $registros = TCisterna::where('cisterna_id', $cisterna_id)
            ->where('fecha', '>=', $hace7dias)
            ->orderBy('fecha', 'asc')
            ->get();

        return response()->json($registros);
    }

    //    public function dia($cisterna_id)
    // {
    //     $dia = Carbon::now()->subDays(1);

    //     $registros = TCisterna::where('cisterna_id', $cisterna_id)
    //         ->where('fecha', '>=', $dia)
    //         ->orderBy('fecha', 'asc')
    //         ->get();

    //     return response()->json($registros);
    // }

    public function dia($cisterna_id)
{
    $inicioDia = Carbon::today();       // 00:00 del día actual
    $finDia = Carbon::now();            // hora actual

    $registros = TCisterna::where('cisterna_id', $cisterna_id)
        ->whereBetween('fecha', [$inicioDia, $finDia])
        ->orderBy('fecha', 'asc')
        ->get();

    if ($registros->isEmpty()) {
        return response()->json([
            'message' => 'No hay datos para el día de hoy',
            'cisterna_id' => $cisterna_id
        ], 404);
    }

    return response()->json($registros);
}


    //   public function historico($cisterna_id)
    // {
    //     $registros = TCisterna::where('cisterna_id', $cisterna_id)
    //         ->orderBy('fecha', 'asc')
    //         ->get();

    //     return response()->json($registros);
    // }

    public function historico($cisterna_id)
{
    $fechaInicio = Carbon::now()->subDays(30);  // hace 30 días
    $fechaFin = Carbon::now();                  // hora actual

    $registros = TCisterna::where('cisterna_id', $cisterna_id)
        ->whereBetween('fecha', [$fechaInicio, $fechaFin])
        ->orderBy('fecha', 'asc')
        ->get();

    if ($registros->isEmpty()) {
        return response()->json([
            'message' => 'No hay datos históricos en los últimos 30 días',
            'cisterna_id' => $cisterna_id
        ], 404);
    }

    return response()->json($registros);
}

public function dimensiones($id)
{
    $dimensiones = DB::table('dimencion_cisternas')->where('sitio_id', $id)
        ->get();

        return response()->json($dimensiones);
}



}