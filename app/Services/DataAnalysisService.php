<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;

class DataAnalysisService
{
    public function procesar($archivo, array $umbrales = [])
    {
        $hojas = Excel::toArray([], $archivo);

        if (empty($hojas) || empty($hojas[0])) {
            throw new \Exception('El archivo no contiene datos.');
        }

        $filas = $hojas[0];

        $filaEncabezado = $this->buscarEncabezado($filas);

        if ($filaEncabezado === null) {
            throw new \Exception(
                'No se pudo identificar la estructura del archivo.'
            );
        }

        $datos = [];

        // Empezamos después del encabezado.
        for ($i = $filaEncabezado + 1; $i < count($filas); $i++) {

            $fila = $filas[$i];

            // Ignorar filas vacías o incompletas
            if (
                !isset($fila[0]) ||
                !is_numeric($fila[0])
            ) {
                continue;
            }

            $datos[] = [
                'tiempo' => (float) ($fila[0] ?? 0),
                'velocidad' => (float) ($fila[1] ?? 0),
                'corriente' => (float) ($fila[2] ?? 0),
                'bus_dc' => (float) ($fila[3] ?? 0),
                'torque' => (float) ($fila[4] ?? 0),
                'voltaje_salida' => (float) ($fila[5] ?? 0),
                'temp_inversor' => (float) ($fila[6] ?? 0),
                'contador_ventilador' => (float) ($fila[7] ?? 0),
                'temp_ambiente_porcentaje' => (float) ($fila[8] ?? 0),
                'frecuencia' => (float) ($fila[9] ?? 0),
                'temp_ambiente' => (float) ($fila[10] ?? 0),
            ];
        }

        if (empty($datos)) {
            throw new \Exception(
                'El archivo fue reconocido, pero no contiene mediciones válidas.'
            );
        }

        return [
            'datos' => $datos,
            'kpis' => $this->calcularKpis($datos),
            'graficas' => $this->prepararGraficas($datos),
            'correlaciones' => $this->calcularCorrelaciones($datos),
            'semaforos' => $this->calcularSemaforos($datos, $umbrales),
            'eventos' => $this->detectarEventos($datos, $umbrales),
            'umbrales' => $umbrales,
        ];
    }

    private function buscarEncabezado(array $filas): ?int
    {
        foreach ($filas as $indice => $fila) {

            $contenido = strtolower(
                implode(' ', array_map(
                    fn ($valor) => trim((string) $valor),
                    $fila
                ))
            );

            if (
                str_contains($contenido, 'motor speed') &&
                str_contains($contenido, 'motor current') &&
                str_contains($contenido, 'dc voltage') &&
                str_contains($contenido, 'motor torque')
            ) {
                return $indice;
            }
        }

        return null;
    }

    private function calcularKpis(array $datos): array
    {
        return [
            'corriente' => $this->estadisticas($datos, 'corriente'),
            'bus_dc' => $this->estadisticas($datos, 'bus_dc'),
            'torque' => $this->estadisticas($datos, 'torque'),
            'temp_inversor' => $this->estadisticas($datos, 'temp_inversor'),
            'temp_ambiente' => $this->estadisticas($datos, 'temp_ambiente'),
            'velocidad' => $this->estadisticas($datos, 'velocidad'),
        ];
    }

    private function estadisticas(array $datos, string $campo): array
    {
        $valores = array_column($datos, $campo);

        return [
            'actual' => end($valores),
            'promedio' => array_sum($valores) / count($valores),
            'minimo' => min($valores),
            'maximo' => max($valores),
        ];
    }


    private function prepararGraficas(array $datos): array
    {
        $maxPuntos = 1500;
        $total = count($datos);

        // Solo reducimos puntos para dibujar.
        // Los KPI siguen usando TODAS las mediciones.
        $salto = max(1, (int) ceil($total / $maxPuntos));

        $graficas = [];

        $tiempoInicial = $datos[0]['tiempo'];

        foreach ($datos as $indice => $fila) {

            // Conservamos un punto cada N registros.
            if ($indice % $salto !== 0 && $indice !== $total - 1) {
                continue;
            }

            $graficas[] = [
                'tiempo' => round($fila['tiempo'] - $tiempoInicial, 2),
                'corriente' => $fila['corriente'],
                'bus_dc' => $fila['bus_dc'],
                'torque' => $fila['torque'],
                'temp_inversor' => $fila['temp_inversor'],
                'temp_ambiente' => $fila['temp_ambiente'],
            ];
        }

        return $graficas;
    }

    private function calcularCorrelaciones(array $datos): array
    {
        return [
            'corriente_torque' => [
                'r' => $this->pearson($datos, 'corriente', 'torque'),
                'interpretacion' => '',
            ],

            'corriente_temp_inversor' => [
                'r' => $this->pearson($datos, 'corriente', 'temp_inversor'),
                'interpretacion' => '',
            ],

            'bus_dc_temp_inversor' => [
                'r' => $this->pearson($datos, 'bus_dc', 'temp_inversor'),
                'interpretacion' => '',
            ],

            'temp_ambiente_temp_inversor' => [
                'r' => $this->pearson($datos, 'temp_ambiente', 'temp_inversor'),
                'interpretacion' => '',
            ],
        ];
    }


    private function pearson(
        array $datos,
        string $campoX,
        string $campoY
    ): float {

        $x = array_column($datos, $campoX);
        $y = array_column($datos, $campoY);

        $n = count($x);

        if ($n < 2) {
            return 0;
        }

        $promedioX = array_sum($x) / $n;
        $promedioY = array_sum($y) / $n;

        $numerador = 0;
        $sumaX = 0;
        $sumaY = 0;

        for ($i = 0; $i < $n; $i++) {

            $dx = $x[$i] - $promedioX;
            $dy = $y[$i] - $promedioY;

            $numerador += $dx * $dy;
            $sumaX += $dx ** 2;
            $sumaY += $dy ** 2;
        }

        $denominador = sqrt($sumaX * $sumaY);

        if ($denominador == 0) {
            return 0;
        }

        return round($numerador / $denominador, 3);
    }


    private function calcularSemaforos(array $datos, array $umbrales): array
    {
        $variables = [
            'corriente' => ['nombre' => 'Corriente', 'unidad' => 'A'],
            'bus_dc' => ['nombre' => 'Bus DC', 'unidad' => 'V'],
            'torque' => ['nombre' => 'Torque', 'unidad' => '%'],
            'temp_inversor' => ['nombre' => 'Temp. inversor', 'unidad' => '%'],
            'temp_ambiente' => ['nombre' => 'Temp. ambiente', 'unidad' => '°C'],
            'velocidad' => ['nombre' => 'Velocidad', 'unidad' => 'rpm'],
        ];

        $resultado = [];

        foreach ($variables as $campo => $meta) {
            $config = $umbrales[$campo] ?? [];
            $advertencia = $this->normalizarUmbral($config['advertencia'] ?? null);
            $critico = $this->normalizarUmbral($config['critico'] ?? null);

            $valores = array_column($datos, $campo);
            $actual = (float) end($valores);
            $maximo = max($valores);

            $estado = 'sin_configurar';

            if ($advertencia !== null && $critico !== null) {
                if ($actual >= $critico) {
                    $estado = 'critico';
                } elseif ($actual >= $advertencia) {
                    $estado = 'advertencia';
                } else {
                    $estado = 'normal';
                }
            }

            $resultado[$campo] = [
                'nombre' => $meta['nombre'],
                'unidad' => $meta['unidad'],
                'actual' => $actual,
                'maximo' => $maximo,
                'estado' => $estado,
                'advertencia' => $advertencia,
                'critico' => $critico,
            ];
        }

        return $resultado;
    }

    private function detectarEventos(array $datos, array $umbrales): array
    {
        $variables = [
            'corriente' => ['nombre' => 'Corriente', 'unidad' => 'A'],
            'bus_dc' => ['nombre' => 'Bus DC', 'unidad' => 'V'],
            'torque' => ['nombre' => 'Torque', 'unidad' => '%'],
            'temp_inversor' => ['nombre' => 'Temp. inversor', 'unidad' => '%'],
            'temp_ambiente' => ['nombre' => 'Temp. ambiente', 'unidad' => '°C'],
            'velocidad' => ['nombre' => 'Velocidad', 'unidad' => 'rpm'],
        ];

        $eventos = [];
        $tiempoInicial = $datos[0]['tiempo'];

        foreach ($variables as $campo => $meta) {
            $config = $umbrales[$campo] ?? [];
            $advertencia = $this->normalizarUmbral($config['advertencia'] ?? null);
            $critico = $this->normalizarUmbral($config['critico'] ?? null);

            if ($advertencia === null || $critico === null) {
                continue;
            }

            $eventoActual = null;

            foreach ($datos as $fila) {
                $valor = (float) $fila[$campo];
                $estado = $this->clasificarValor($valor, $advertencia, $critico);
                $tiempo = round($fila['tiempo'] - $tiempoInicial, 2);

                if ($estado === 'normal') {
                    if ($eventoActual !== null) {
                        $eventos[] = $this->cerrarEvento($eventoActual);
                        $eventoActual = null;
                    }
                    continue;
                }

                if ($eventoActual === null) {
                    $eventoActual = [
                        'variable' => $campo,
                        'nombre' => $meta['nombre'],
                        'unidad' => $meta['unidad'],
                        'estado' => $estado,
                        'inicio' => $tiempo,
                        'fin' => $tiempo,
                        'maximo' => $valor,
                    ];
                    continue;
                }

                // Si dentro del mismo episodio se alcanza crítico,
                // elevamos la severidad sin dividirlo en varios eventos.
                if ($estado === 'critico') {
                    $eventoActual['estado'] = 'critico';
                }

                $eventoActual['fin'] = $tiempo;
                $eventoActual['maximo'] = max($eventoActual['maximo'], $valor);
            }

            if ($eventoActual !== null) {
                $eventos[] = $this->cerrarEvento($eventoActual);
            }
        }

        usort($eventos, function ($a, $b) {
            if ($a['inicio'] === $b['inicio']) {
                return $a['estado'] === 'critico' ? -1 : 1;
            }
            return $a['inicio'] <=> $b['inicio'];
        });

        return $eventos;
    }

    private function cerrarEvento(array $evento): array
    {
        $evento['duracion'] = round(max(0, $evento['fin'] - $evento['inicio']), 2);
        return $evento;
    }

    private function clasificarValor(float $valor, float $advertencia, float $critico): string
    {
        if ($valor >= $critico) {
            return 'critico';
        }

        if ($valor >= $advertencia) {
            return 'advertencia';
        }

        return 'normal';
    }

    private function normalizarUmbral($valor): ?float
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        return is_numeric($valor) ? (float) $valor : null;
    }

}