<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DataAnalysisService;


class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }

    public function upload(
        Request $request,
        DataAnalysisService $analizador
    ) {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:20480',

            'umbrales.*.advertencia' => 'nullable|numeric',
            'umbrales.*.critico' => 'nullable|numeric',
        ]);

        $umbrales = $request->input(
            'umbrales',
            session('umbrales_monitoreo', [])
        );

        // Solo guardamos configuraciones completas y coherentes.
        foreach ($umbrales as $variable => $config) {
            $advertencia = $config['advertencia'] ?? null;
            $critico = $config['critico'] ?? null;

            if ($advertencia === '' && $critico === '') {
                unset($umbrales[$variable]);
                continue;
            }

            if (
                $advertencia !== null &&
                $advertencia !== '' &&
                $critico !== null &&
                $critico !== '' &&
                (float) $critico <= (float) $advertencia
            ) {
                return back()
                    ->withInput()
                    ->with('error', 'El umbral crítico debe ser mayor que el de advertencia.');
            }
        }

        session(['umbrales_monitoreo' => $umbrales]);

        try {

            $resultado = $analizador->procesar(
                $request->file('archivo'),
                $umbrales
            );

            return view('dashboard.index', [
                'datos' => $resultado['datos'],
                'kpis' => $resultado['kpis'],
                'graficas' => $resultado['graficas'],
                'correlaciones' => $resultado['correlaciones'],
                'semaforos' => $resultado['semaforos'],
                'eventos' => $resultado['eventos'],
                'umbrales' => $resultado['umbrales'],
                'nombreArchivo' =>
                    $request->file('archivo')->getClientOriginalName(),
            ]);

        } catch (\Exception $e) {

            return back()->with(
                'error',
                $e->getMessage()
            );
        }
    }
}