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

        // Advertencia y Crítico pueden configurarse de forma independiente.
        foreach ($umbrales as $variable => $config) {
            $advertencia = $config['advertencia'] ?? null;
            $critico = $config['critico'] ?? null;

            if (($advertencia === null || $advertencia === '') && ($critico === null || $critico === '')) {
                unset($umbrales[$variable]);
                continue;
            }

            if ($advertencia !== null && $advertencia !== '' && $critico !== null && $critico !== '') {
                $advertencia = (float) $advertencia;
                $critico = (float) $critico;

                if ($variable === 'bus_dc' && $critico >= $advertencia) {
                    return back()->withInput()->with('error', 'En Bus DC, el umbral crítico debe ser menor que el de advertencia.');
                }

                if ($variable !== 'bus_dc' && $critico <= $advertencia) {
                    return back()->withInput()->with('error', 'El umbral crítico debe ser mayor que el de advertencia.');
                }
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
                'indicadores' => $resultado['indicadores'],
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