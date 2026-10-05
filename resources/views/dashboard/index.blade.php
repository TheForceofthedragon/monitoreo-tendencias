<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Monitor de Tendencias</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            color: #1f2937;
        }

        .header {
            background: #111827;
            color: white;
            padding: 22px 40px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header p {
            margin: 6px 0 0;
            color: #cbd5e1;
        }

        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 4px 15px rgba(0,0,0,.08);
        }

        .upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 10px;
            padding: 45px;
            text-align: center;
        }

        .upload-area h2 {
            margin-top: 0;
        }

        input[type="file"] {
            margin: 20px 0;
        }

        button {
            background: #2563eb;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 7px;
            font-size: 15px;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .formats {
            margin-top: 15px;
            color: #64748b;
            font-size: 13px;
        }

        .kpi-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
        }

        .kpi-title {
            display: block;
            color: #64748b;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .kpi-value {
            font-size: 30px;
            font-weight: bold;
            color: #111827;
        }

        .kpi-value small {
            font-size: 15px;
            color: #64748b;
        }

        .kpi-details {
            margin-top: 12px;
            font-size: 12px;
            color: #64748b;
        }


        .trend-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            flex-wrap: wrap;
        }

        .trend-header p {
            color: #64748b;
            margin: 8px 0 0;
        }

        .trend-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .trend-btn {
            background: #e2e8f0;
            color: #334155;
            padding: 9px 15px;
            border-radius: 7px;
            font-size: 13px;
        }

        .trend-btn:hover {
            background: #cbd5e1;
        }

        .trend-btn.active {
            background: #2563eb;
            color: white;
        }

        .chart-container {
            position: relative;
            height: 430px;
            margin-top: 30px;
        }


        .correlation-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            flex-wrap: wrap;
        }
        .correlation-header p { color: #64748b; margin: 8px 0 0; }
        .correlation-result {
            min-width: 190px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 18px;
            text-align: center;
        }
        .correlation-result span {
            display: block;
            color: #64748b;
            font-size: 12px;
        }
        .correlation-result strong {
            display: block;
            font-size: 30px;
            color: #111827;
            margin: 4px 0;
        }
        .correlation-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 25px;
        }
        .correlation-btn {
            background: #e2e8f0;
            color: #334155;
            padding: 9px 15px;
            border-radius: 7px;
            font-size: 13px;
        }
        .correlation-btn:hover { background: #cbd5e1; }
        .correlation-btn.active { background: #2563eb; color: white; }


        .back-bar {
            display:flex; justify-content:space-between; align-items:center;
            gap:16px; margin-bottom:22px; flex-wrap:wrap;
        }
        .back-btn {
            display:inline-flex; align-items:center; gap:8px;
            text-decoration:none; background:#fff; color:#334155;
            border:1px solid #cbd5e1; padding:10px 16px;
            border-radius:8px; font-weight:600;
        }
        .back-btn:hover { background:#f8fafc; }
        .loaded-file { color:#64748b; font-size:14px; }

        .progress-wrap {
            display:none; margin:22px auto 0; max-width:520px; text-align:left;
        }
        .progress-info {
            display:flex; justify-content:space-between;
            color:#475569; font-size:13px; margin-bottom:8px;
        }
        .progress-track {
            width:100%; height:10px; background:#e2e8f0;
            border-radius:999px; overflow:hidden;
        }
        .progress-bar {
            width:0%; height:100%; background:#22c55e;
            border-radius:999px; transition:width .25s ease;
        }
        .selected-file {
            margin-top:10px; color:#64748b; font-size:13px;
            word-break:break-all;
        }
        .analyze-btn:disabled { opacity:.7; cursor:not-allowed; }

        .threshold-box { max-width:760px; margin:26px auto 0; text-align:left; border:1px solid #e2e8f0; border-radius:10px; background:#f8fafc; overflow:hidden; }
        .threshold-box summary { cursor:pointer; padding:16px 18px; font-weight:700; color:#1f2937; user-select:none; }
        .threshold-content { padding:0 18px 18px; }
        .threshold-help { color:#64748b; font-size:13px; margin:0 0 15px; }
        .threshold-grid { display:grid; grid-template-columns:minmax(170px,1.4fr) 1fr 1fr; gap:10px 12px; align-items:center; }
        .threshold-grid .th { font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase; }
        .threshold-grid .variable-name { font-size:13px; color:#334155; font-weight:600; }
        .threshold-grid input { width:100%; padding:9px 10px; border:1px solid #cbd5e1; border-radius:7px; background:white; color:#111827; }
        .threshold-note { margin-top:14px; font-size:12px; color:#64748b; }
        .status-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:14px; margin-top:22px; }
        .status-card { border:1px solid #e2e8f0; border-radius:10px; padding:16px; background:#f8fafc; }
        .status-card .name { color:#64748b; font-size:13px; margin-bottom:9px; }
        .status-pill { display:inline-flex; align-items:center; gap:7px; padding:7px 10px; border-radius:999px; font-size:12px; font-weight:700; }
        .status-normal { background:#dcfce7; color:#166534; }
        .status-warning { background:#fef3c7; color:#92400e; }
        .status-critical { background:#fee2e2; color:#991b1b; }
        .status-none { background:#e2e8f0; color:#475569; }
        .event-table-wrap { overflow-x:auto; margin-top:20px; }
        .event-table { width:100%; border-collapse:collapse; font-size:13px; }
        .event-table th,.event-table td { padding:11px 12px; border-bottom:1px solid #e2e8f0; text-align:left; }
        .event-table th { color:#64748b; background:#f8fafc; }
        @media(max-width:650px){ .threshold-grid{grid-template-columns:1fr 1fr;} .threshold-grid .variable-name{grid-column:1/-1;margin-top:7px;} .threshold-grid .variable-header{display:none;} }


        html { scroll-behavior: smooth; }
        .section-anchor { scroll-margin-top: 105px; }

        .dashboard-nav {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255,255,255,.96);
            border: 1px solid #e2e8f0;
            box-shadow: 0 8px 24px rgba(15,23,42,.07);
            border-radius: 12px;
            padding: 10px 14px;
            margin: 0 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            backdrop-filter: blur(8px);
        }
        .dashboard-nav-links { display:flex; gap:7px; flex-wrap:wrap; }
        .dashboard-nav a {
            text-decoration:none; color:#475569; font-weight:600; font-size:13px;
            padding:9px 12px; border-radius:8px; transition:.2s ease;
        }
        .dashboard-nav a:hover { background:#eff6ff; color:#2563eb; }
        .dashboard-nav-file { color:#64748b; font-size:12px; white-space:nowrap; }

        .executive-grid {
            display:grid; grid-template-columns:repeat(3,1fr);
            gap:14px; margin:20px 0 8px;
        }
        .executive-card {
            padding:18px; border:1px solid #e2e8f0; border-radius:11px;
            background:#f8fafc;
        }
        .executive-card .executive-value {
            display:block; font-size:28px; font-weight:800; color:#0f172a; line-height:1.1;
        }
        .executive-card .executive-label {
            display:block; margin-top:6px; color:#64748b; font-size:13px;
        }
        .executive-card.critical-summary { background:#fff7f7; }
        .executive-card.warning-summary { background:#fffbeb; }

        .section-heading {
            display:flex; align-items:flex-start; justify-content:space-between;
            gap:16px; margin-bottom:4px;
        }
        .section-heading h2 { margin:0; }
        .section-kicker {
            display:inline-block; margin-bottom:5px; color:#2563eb;
            font-size:11px; font-weight:800; letter-spacing:.08em; text-transform:uppercase;
        }

        .status-card { transition:transform .18s ease, box-shadow .18s ease; }
        .status-card:hover { transform:translateY(-2px); box-shadow:0 7px 18px rgba(15,23,42,.06); }
        .status-pill { text-transform:none; }
        .status-dot {
            width:9px; height:9px; border-radius:50%; display:inline-block; flex:0 0 9px;
            background:currentColor;
        }

        .event-level {
            display:inline-flex; align-items:center; padding:5px 9px;
            border-radius:999px; font-size:12px; font-weight:700;
        }
        .event-level.critico { background:#fee2e2; color:#991b1b; }
        .event-level.advertencia { background:#fef3c7; color:#92400e; }

        @media (max-width: 850px) {
            .dashboard-nav { align-items:flex-start; flex-direction:column; }
            .dashboard-nav-file { white-space:normal; }
            .executive-grid { grid-template-columns:1fr; }
        }


        .page-shell { max-width:1240px; margin:0 auto; }
        .dashboard-nav {
            top: 12px;
            border-radius:14px;
            padding:9px 12px;
            box-shadow:0 10px 30px rgba(15,23,42,.08);
        }
        .dashboard-nav-links { gap:4px; }
        .dashboard-nav a {
            padding:10px 14px;
            border-radius:9px;
        }
        .dashboard-nav a.active-section {
            background:#2563eb;
            color:#fff;
            box-shadow:0 5px 12px rgba(37,99,235,.22);
        }
        .dashboard-nav-file {
            max-width:260px;
            overflow:hidden;
            text-overflow:ellipsis;
        }
        .analysis-hero {
            display:flex;
            justify-content:space-between;
            align-items:flex-end;
            gap:24px;
            margin-bottom:22px;
        }
        .analysis-hero h2 { font-size:27px; margin:0 0 7px; }
        .analysis-hero p { margin:0; color:#64748b; }
        .analysis-file-badge {
            padding:10px 13px;
            background:#eff6ff;
            border:1px solid #dbeafe;
            color:#1d4ed8;
            border-radius:10px;
            font-size:12px;
            font-weight:700;
        }
        .executive-grid {
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:14px;
            margin:22px 0 28px;
        }
        .executive-card {
            position:relative;
            overflow:hidden;
            min-height:92px;
            padding:18px 20px;
            border:1px solid #e2e8f0;
            border-radius:12px;
            background:#f8fafc;
        }
        .executive-card::before {
            content:"";
            position:absolute;
            left:0; top:0; bottom:0;
            width:4px;
            background:#94a3b8;
        }
        .executive-card.critical-summary::before { background:#ef4444; }
        .executive-card.warning-summary::before { background:#f59e0b; }
        .executive-value { display:block; font-size:30px; line-height:1; font-weight:800; color:#0f172a; }
        .executive-label { display:block; margin-top:8px; color:#64748b; font-size:13px; }
        .section-anchor { scroll-margin-top:92px; }
        .card { border:1px solid rgba(226,232,240,.85); }
        .kpi-card { transition:.18s ease; }
        .kpi-card:hover { transform:translateY(-2px); box-shadow:0 8px 22px rgba(15,23,42,.06); }
        .back-bar { margin-bottom:18px; }
        .back-btn { transition:.18s ease; }
        .back-btn:hover { border-color:#93c5fd; background:#eff6ff; color:#1d4ed8; }
        @media(max-width:800px) {
            .analysis-hero { align-items:flex-start; flex-direction:column; }
            .executive-grid { grid-template-columns:1fr; }
            .dashboard-nav-file { display:none; }
        }


        /* ===== Acabado visual V1 ===== */
        header {
            position:relative;
            overflow:hidden;
            border-bottom:1px solid rgba(255,255,255,.08);
        }
        header::after {
            content:"";
            position:absolute;
            right:-90px; top:-150px;
            width:360px; height:360px;
            border-radius:50%;
            background:rgba(59,130,246,.10);
            pointer-events:none;
        }
        header h1 { letter-spacing:-.025em; }
        header p { opacity:.82; }

        .kpi-card { position:relative; overflow:hidden; background:linear-gradient(180deg,#fff,#f8fafc); }
        .kpi-card::after {
            content:"";
            position:absolute; left:0; top:0; bottom:0;
            width:3px; background:#cbd5e1;
        }
        .kpi-card:nth-child(1)::after { background:#3b82f6; }
        .kpi-card:nth-child(2)::after { background:#6366f1; }
        .kpi-card:nth-child(3)::after { background:#8b5cf6; }
        .kpi-card:nth-child(4)::after { background:#f59e0b; }
        .kpi-card:nth-child(5)::after { background:#10b981; }
        .kpi-card:nth-child(6)::after { background:#0ea5e9; }

        .trend-header h2, .correlation-header h2 { letter-spacing:-.02em; }
        .chart-box {
            border:1px solid #eef2f7;
            border-radius:12px;
            padding:12px 10px 4px;
            background:linear-gradient(180deg,#ffffff,#fbfdff);
        }
        .chart-buttons button, .correlation-buttons button {
            transition:all .18s ease;
            border:1px solid transparent;
        }
        .chart-buttons button:hover, .correlation-buttons button:hover {
            transform:translateY(-1px);
            border-color:#bfdbfe;
        }
        .chart-buttons button.active, .correlation-buttons button.active {
            box-shadow:0 5px 12px rgba(37,99,235,.18);
        }
        .pearson-box { box-shadow:inset 0 0 0 1px rgba(226,232,240,.25); }

        .section-kicker { letter-spacing:.1em; }
        .card {
            box-shadow:0 10px 30px rgba(15,23,42,.055);
        }

        @media(max-width:1100px) {
            .container { width:94%; }
            .kpi-grid { grid-template-columns:repeat(3,1fr); }
            .trend-header, .correlation-header { align-items:flex-start; flex-direction:column; }
            .chart-buttons, .correlation-buttons { justify-content:flex-start; width:100%; }
        }
        @media(max-width:760px) {
            header { padding:22px 20px; }
            header h1 { font-size:24px; }
            .container { width:96%; padding:24px 0 45px; }
            .card { padding:24px 20px; border-radius:12px; }
            .back-bar { align-items:flex-start; flex-direction:column; gap:12px; }
            .loaded-file { text-align:left; }
            .dashboard-nav { top:6px; overflow-x:auto; flex-wrap:nowrap; }
            .dashboard-nav-links { flex-wrap:nowrap; min-width:max-content; }
            .dashboard-nav a { white-space:nowrap; padding:9px 11px; }
            .kpi-grid { grid-template-columns:repeat(2,1fr); }
            .kpi-value { font-size:27px; }
            .chart-buttons, .correlation-buttons { overflow-x:auto; flex-wrap:nowrap; padding-bottom:5px; }
            .chart-buttons button, .correlation-buttons button { white-space:nowrap; }
            .chart-box { height:390px; padding:8px 4px 2px; }
            .correlation-card .chart-box { height:410px; }
            .pearson-box { min-width:190px; }
        }
        @media(max-width:520px) {
            .kpi-grid { grid-template-columns:1fr; }
            .analysis-hero h2 { font-size:24px; }
            .executive-value { font-size:27px; }
            .chart-box, .correlation-card .chart-box { height:340px; }
            .event-table th,.event-table td { padding:9px 8px; font-size:12px; }
        }

        .event-pagination{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-top:18px;padding-top:16px;border-top:1px solid #e2e8f0}
        .event-pagination-info{color:#64748b;font-size:13px}
        .event-pagination-controls{display:flex;align-items:center;gap:8px}
        .event-page-btn{background:#fff;color:#334155;border:1px solid #cbd5e1;padding:8px 12px;border-radius:8px;font-size:13px;font-weight:600}
        .event-page-btn:hover:not(:disabled){background:#eff6ff;border-color:#93c5fd;color:#1d4ed8}
        .event-page-btn:disabled{opacity:.42;cursor:not-allowed;background:#f8fafc}
        .event-page-indicator{min-width:72px;text-align:center;color:#475569;font-size:13px;font-weight:700}
        @media(max-width:650px){.event-pagination{flex-direction:column;align-items:stretch}.event-pagination-controls{justify-content:space-between}}
    </style>
</head>


<body>

    <div class="header">
        <h1>Monitor de Tendencias</h1>
        <p>Análisis de variables, tendencias y correlaciones</p>
    </div>

    <div class="container">
        @if(session('success'))
            <div style="
                background:#dcfce7;
                color:#166534;
                padding:15px;
                border-radius:8px;
                margin-bottom:20px;
            ">
                {{ session('success') }}
            </div>
        @endif

        @if(!isset($kpis))
        <div class="card">

            <div class="upload-area">

                <h2>Cargar archivo de datos</h2>

                <p>
                    Selecciona el archivo generado por el sistema de monitoreo.
                </p>

                <form id="uploadForm"
                      method="POST"
                      action="{{ route('dashboard.upload') }}"
                      enctype="multipart/form-data">

                    @csrf

                    <input
                        type="file"
                        id="archivoInput"
                        name="archivo"
                        accept=".xlsx,.xls,.csv"
                        required
                    >

                    <br>

                    <details class="threshold-box">
                        <summary>⚙ Configuración de umbrales (opcional)</summary>
                        <div class="threshold-content">
                            <p class="threshold-help">Define los límites para generar los semáforos y eventos. Puedes dejar cualquier variable sin configurar.</p>
                            <div class="threshold-grid">
                                <div class="th variable-header">Variable</div><div class="th">Advertencia</div><div class="th">Crítico</div>
                                <div class="variable-name">Corriente (A)</div><input type="number" step="any" name="umbrales[corriente][advertencia]" placeholder="Advertencia"><input type="number" step="any" name="umbrales[corriente][critico]" placeholder="Crítico">
                                <div class="variable-name">Bus DC (V)</div><input type="number" step="any" name="umbrales[bus_dc][advertencia]" placeholder="Advertencia"><input type="number" step="any" name="umbrales[bus_dc][critico]" placeholder="Crítico">
                                <div class="variable-name">Torque (%)</div><input type="number" step="any" name="umbrales[torque][advertencia]" placeholder="Advertencia"><input type="number" step="any" name="umbrales[torque][critico]" placeholder="Crítico">
                                <div class="variable-name">Temp. inversor (%)</div><input type="number" step="any" name="umbrales[temp_inversor][advertencia]" placeholder="Advertencia"><input type="number" step="any" name="umbrales[temp_inversor][critico]" placeholder="Crítico">
                                <div class="variable-name">Temp. ambiente (°C)</div><input type="number" step="any" name="umbrales[temp_ambiente][advertencia]" placeholder="Advertencia"><input type="number" step="any" name="umbrales[temp_ambiente][critico]" placeholder="Crítico">
                                <div class="variable-name">Velocidad (rpm)</div><input type="number" step="any" name="umbrales[velocidad][advertencia]" placeholder="Advertencia"><input type="number" step="any" name="umbrales[velocidad][critico]" placeholder="Crítico">
                            </div>
                            <div class="threshold-note">Menor a Advertencia = Normal · Desde Advertencia = Advertencia · Desde Crítico = Crítico.</div>
                        </div>
                    </details>

                    <br>

                    <button id="analyzeBtn" class="analyze-btn" type="submit">
                        Analizar archivo
                    </button>

                    <div id="progressWrap" class="progress-wrap">
                        <div class="progress-info">
                            <span id="progressText">Preparando archivo...</span>
                            <span id="progressPercent">0%</span>
                        </div>
                        <div class="progress-track">
                            <div id="progressBar" class="progress-bar"></div>
                        </div>
                        <div id="selectedFile" class="selected-file"></div>
                    </div>

                </form>

                <div class="formats">
                    Formatos permitidos: XLSX, XLS y CSV
                </div>

            </div>

        </div>
        @endif


        @if(isset($kpis))

            <div class="back-bar">
                <a href="{{ url('/') }}" class="back-btn">
                    ← Analizar otro archivo
                </a>
                <div class="loaded-file">
                    Archivo actual: <strong>{{ $nombreArchivo }}</strong>
                </div>
            </div>

            @php
                $criticosResumen = collect($eventos ?? [])->where('estado', 'critico')->count();
                $advertenciasResumen = collect($eventos ?? [])->where('estado', 'advertencia')->count();
            @endphp

            <nav class="dashboard-nav" aria-label="Navegación del análisis">
                <div class="dashboard-nav-links">
                    <a href="#resumen">Resumen</a>
                    <a href="#semaforos">Semáforos</a>
                    <a href="#eventos">Eventos</a>
                    <a href="#tendencias">Tendencias</a>
                    <a href="#correlaciones">Correlaciones</a>
                </div>
                <div class="dashboard-nav-file">{{ $nombreArchivo }}</div>
            </nav>

            <div class="card section-anchor" id="resumen" style="margin-top:30px;">

                <div class="analysis-hero">
                    <div>
                        <span class="section-kicker">Resumen ejecutivo</span>
                        <h2>Resultados del análisis</h2>
                        <p>Vista general de las variables procesadas y eventos detectados.</p>
                    </div>
                    <div class="analysis-file-badge">{{ $nombreArchivo }}</div>
                </div>

                <div class="executive-grid">
                    <div class="executive-card">
                        <span class="executive-value">{{ number_format(count($datos)) }}</span>
                        <span class="executive-label">Mediciones procesadas</span>
                    </div>
                    <div class="executive-card critical-summary">
                        <span class="executive-value">{{ $criticosResumen }}</span>
                        <span class="executive-label">Eventos críticos</span>
                    </div>
                    <div class="executive-card warning-summary">
                        <span class="executive-value">{{ $advertenciasResumen }}</span>
                        <span class="executive-label">Advertencias detectadas</span>
                    </div>
                </div>

                <div style="
                    display:grid;
                    grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));
                    gap:20px;
                    margin-top:30px;
                ">

                    {{-- CORRIENTE --}}
                    <div class="kpi-card">
                        <span class="kpi-title">Corriente</span>

                        <div class="kpi-value">
                            {{ number_format($kpis['corriente']['actual'], 2) }}
                            <small>A</small>
                        </div>

                        <div class="kpi-details">
                            Prom:
                            {{ number_format($kpis['corriente']['promedio'], 2) }}
                            |
                            Min:
                            {{ number_format($kpis['corriente']['minimo'], 2) }}
                            |
                            Max:
                            {{ number_format($kpis['corriente']['maximo'], 2) }}
                        </div>
                    </div>

                    {{-- BUS DC --}}
                    <div class="kpi-card">
                        <span class="kpi-title">Bus DC</span>

                        <div class="kpi-value">
                            {{ number_format($kpis['bus_dc']['actual'], 2) }}
                            <small>V</small>
                        </div>

                        <div class="kpi-details">
                            Prom:
                            {{ number_format($kpis['bus_dc']['promedio'], 2) }}
                            |
                            Min:
                            {{ number_format($kpis['bus_dc']['minimo'], 2) }}
                            |
                            Max:
                            {{ number_format($kpis['bus_dc']['maximo'], 2) }}
                        </div>
                    </div>

                    {{-- TORQUE --}}
                    <div class="kpi-card">
                        <span class="kpi-title">Torque</span>

                        <div class="kpi-value">
                            {{ number_format($kpis['torque']['actual'], 2) }}
                            <small>%</small>
                        </div>

                        <div class="kpi-details">
                            Prom:
                            {{ number_format($kpis['torque']['promedio'], 2) }}
                            |
                            Min:
                            {{ number_format($kpis['torque']['minimo'], 2) }}
                            |
                            Max:
                            {{ number_format($kpis['torque']['maximo'], 2) }}
                        </div>
                    </div>

                    {{-- TEMPERATURA INVERSOR --}}
                    <div class="kpi-card">
                        <span class="kpi-title">Temp. inversor</span>

                        <div class="kpi-value">
                            {{ number_format($kpis['temp_inversor']['actual'], 2) }}
                            <small>%</small>
                        </div>

                        <div class="kpi-details">
                            Prom:
                            {{ number_format($kpis['temp_inversor']['promedio'], 2) }}
                            |
                            Min:
                            {{ number_format($kpis['temp_inversor']['minimo'], 2) }}
                            |
                            Max:
                            {{ number_format($kpis['temp_inversor']['maximo'], 2) }}
                        </div>
                    </div>

                    {{-- TEMPERATURA AMBIENTE --}}
                    <div class="kpi-card">
                        <span class="kpi-title">Temp. ambiente</span>

                        <div class="kpi-value">
                            {{ number_format($kpis['temp_ambiente']['actual'], 2) }}
                            <small>°C</small>
                        </div>

                        <div class="kpi-details">
                            Prom:
                            {{ number_format($kpis['temp_ambiente']['promedio'], 2) }}
                            |
                            Min:
                            {{ number_format($kpis['temp_ambiente']['minimo'], 2) }}
                            |
                            Max:
                            {{ number_format($kpis['temp_ambiente']['maximo'], 2) }}
                        </div>
                    </div>

                    {{-- VELOCIDAD --}}
                    <div class="kpi-card">
                        <span class="kpi-title">Velocidad</span>

                        <div class="kpi-value">
                            {{ number_format($kpis['velocidad']['actual'], 2) }}
                            <small>rpm</small>
                        </div>

                        <div class="kpi-details">
                            Prom:
                            {{ number_format($kpis['velocidad']['promedio'], 2) }}
                            |
                            Min:
                            {{ number_format($kpis['velocidad']['minimo'], 2) }}
                            |
                            Max:
                            {{ number_format($kpis['velocidad']['maximo'], 2) }}
                        </div>
                    </div>


                </div>

            </div>

            @if(isset($semaforos))
            <div class="card section-anchor" id="semaforos" style="margin-top:30px;">
                <span class="section-kicker">Estado</span>
                <h2 style="margin:0;">Semáforos</h2>
                <p style="color:#64748b;margin:8px 0 0;">Estado según los umbrales configurados.</p>
                <div class="status-grid">
                    @foreach($semaforos as $clave => $estado)
                        @php
                            $nombres=['corriente'=>'Corriente','bus_dc'=>'Bus DC','torque'=>'Torque','temp_inversor'=>'Temp. inversor','temp_ambiente'=>'Temp. ambiente','velocidad'=>'Velocidad'];
                            $texto=is_array($estado)?($estado['estado']??$estado['status']??'Sin configurar'):$estado;
                            $e=mb_strtolower((string)$texto);
                            if(str_contains($e,'crít')||str_contains($e,'crit')){$clase='status-critical';$icono='🔴';}
                            elseif(str_contains($e,'advert')){$clase='status-warning';$icono='🟡';}
                            elseif(str_contains($e,'normal')){$clase='status-normal';$icono='🟢';}
                            else{$clase='status-none';$icono='⚪';}
                        @endphp
                        <div class="status-card">
                            <div class="name">{{ $nombres[$clave] ?? ucfirst(str_replace('_',' ',$clave)) }}</div>
                            <span class="status-pill {{ $clase }}">
                                <span class="status-dot"></span>
                                {{ $texto === 'sin_configurar' ? 'Sin configurar' : ucfirst(str_replace('_', ' ', $texto)) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if(isset($eventos))
            <div class="card section-anchor" id="eventos" style="margin-top:30px;">
                <span class="section-kicker">Alertas</span>
                <h2 style="margin:0;">Eventos detectados</h2>
                <p style="color:#64748b;margin:8px 0 0;">Periodos que alcanzaron Advertencia o Crítico.</p>
                @if(count($eventos))
                    <div class="event-table-wrap"><table class="event-table">
                        <thead><tr><th>Variable</th><th>Nivel</th><th>Inicio</th><th>Fin</th><th>Duración</th><th>Máximo</th></tr></thead>
                        <tbody>
                        @foreach($eventos as $evento)
                            <tr class="event-row">
                                <td>{{ $evento['nombre'] ?? $evento['variable'] ?? '-' }}</td>
                                @php $nivelEvento = $evento['estado'] ?? $evento['nivel'] ?? '-'; @endphp
                                <td>
                                    <span class="event-level {{ $nivelEvento }}">
                                        {{ ucfirst($nivelEvento) }}
                                    </span>
                                </td>
                                <td>{{ isset($evento['inicio']) ? number_format($evento['inicio'],2).' s' : '-' }}</td>
                                <td>{{ isset($evento['fin']) ? number_format($evento['fin'],2).' s' : '-' }}</td>
                                <td>{{ isset($evento['duracion']) ? number_format($evento['duracion'],2).' s' : '-' }}</td>
                                <td>{{ isset($evento['maximo']) ? number_format($evento['maximo'],2).' '.($evento['unidad']??'') : '-' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table></div>
                    <div class="event-pagination" id="eventPagination">
                        <div class="event-pagination-info" id="eventPaginationInfo"></div>
                        <div class="event-pagination-controls">
                            <button type="button" class="event-page-btn" id="eventPrevBtn">‹ Anterior</button>
                            <span class="event-page-indicator" id="eventPageIndicator"></span>
                            <button type="button" class="event-page-btn" id="eventNextBtn">Siguiente ›</button>
                        </div>
                    </div>
                @else
                    <div style="margin-top:18px;padding:15px;background:#dcfce7;color:#166534;border-radius:8px;">No se detectaron eventos de advertencia o críticos.</div>
                @endif
            </div>
            @endif

            <div class="card section-anchor" id="tendencias" style="margin-top:30px;">

                        <div class="trend-header">
                            <div>
                                <h2 style="margin:0;">Tendencias</h2>
                                <p>
                                    Evolución de las variables durante el periodo analizado.
                                </p>
                            </div>

                            <div class="trend-buttons">
                                <button type="button"
                                        class="trend-btn active"
                                        data-variable="corriente">
                                    Corriente
                                </button>

                                <button type="button"
                                        class="trend-btn"
                                        data-variable="bus_dc">
                                    Bus DC
                                </button>

                                <button type="button"
                                        class="trend-btn"
                                        data-variable="torque">
                                    Torque
                                </button>

                                <button type="button"
                                        class="trend-btn"
                                        data-variable="temp_inversor">
                                    Temp. inversor
                                </button>

                                <button type="button"
                                        class="trend-btn"
                                        data-variable="temp_ambiente">
                                    Temp. ambiente
                                </button>
                            </div>
                        </div>

                        <div class="chart-container">
                            <canvas id="trendChart"></canvas>
                        </div>

                    </div>


            {{-- CORRELACIONES --}}
            <div class="card section-anchor" id="correlaciones" style="margin-top:30px;">
                <div class="correlation-header">
                    <div>
                        <span class="section-kicker">Relación entre variables</span>
                        <h2 style="margin:0;">Correlaciones</h2>
                        <p>Relación entre las principales variables del sistema.</p>
                    </div>

                    <div class="correlation-result">
                        <span>Coeficiente de Pearson</span>
                        <strong id="correlationValue">
                            {{ number_format($correlaciones['corriente_torque']['r'], 3) }}
                        </strong>
                        <span id="correlationText"></span>
                    </div>
                </div>

                <div class="correlation-buttons">
                    <button type="button" class="correlation-btn active" data-correlation="corriente_torque">
                        Corriente vs Torque
                    </button>
                    <button type="button" class="correlation-btn" data-correlation="corriente_temp_inversor">
                        Corriente vs Temp. inversor
                    </button>
                    <button type="button" class="correlation-btn" data-correlation="bus_dc_temp_inversor">
                        Bus DC vs Temp. inversor
                    </button>
                    <button type="button" class="correlation-btn" data-correlation="temp_ambiente_temp_inversor">
                        Temp. ambiente vs Temp. inversor
                    </button>
                </div>

                <div class="chart-container">
                    <canvas id="correlationChart"></canvas>
                </div>
            </div>

        @endif
 

    </div>


    @if(!isset($kpis))
    <script>
        const uploadForm = document.getElementById('uploadForm');
        const archivoInput = document.getElementById('archivoInput');
        const analyzeBtn = document.getElementById('analyzeBtn');
        const progressWrap = document.getElementById('progressWrap');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const progressPercent = document.getElementById('progressPercent');
        const selectedFile = document.getElementById('selectedFile');

        let progressTimer = null;

        archivoInput.addEventListener('change', function () {
            if (!this.files.length) return;

            selectedFile.textContent = '✓ Archivo seleccionado: ' + this.files[0].name;
            progressWrap.style.display = 'block';
            progressBar.parentElement.style.display = 'none';
            progressPercent.style.display = 'none';
            progressText.textContent = 'Archivo listo para analizar';
        });

        uploadForm.addEventListener('submit', function () {
            if (!archivoInput.files.length) return;

            progressBar.parentElement.style.display = 'block';
            progressPercent.style.display = 'inline';

            analyzeBtn.disabled = true;
            analyzeBtn.textContent = 'Analizando...';
            progressWrap.style.display = 'block';

            let progreso = 12;
            progressBar.style.width = progreso + '%';
            progressPercent.textContent = progreso + '%';
            progressText.textContent = 'Cargando y procesando datos...';

            progressTimer = setInterval(function () {
                // Es una barra visual de espera: el procesamiento ocurre en el servidor.
                if (progreso < 90) {
                    progreso += progreso < 55 ? 7 : 3;
                    progreso = Math.min(progreso, 90);
                    progressBar.style.width = progreso + '%';
                    progressPercent.textContent = progreso + '%';

                    if (progreso >= 55) {
                        progressText.textContent = 'Analizando mediciones...';
                    }
                }
            }, 280);
        });
    </script>
    @endif

    @if(isset($graficas))

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>

        const datosGrafica = @json($graficas);

        const configuracionVariables = {
            corriente: {
                titulo: 'Corriente del motor',
                unidad: 'A'
            },

            bus_dc: {
                titulo: 'Bus DC',
                unidad: 'V'
            },

            torque: {
                titulo: 'Torque del motor',
                unidad: '%'
            },

            temp_inversor: {
                titulo: 'Temperatura del inversor',
                unidad: '%'
            },

            temp_ambiente: {
                titulo: 'Temperatura ambiente',
                unidad: '°C'
            }
        };

        const ctx = document
            .getElementById('trendChart')
            .getContext('2d');

        let variableActual = 'corriente';

        const chart = new Chart(ctx, {

            type: 'line',

            data: {
                datasets: [{
                    label: 'Corriente del motor',
                    data: datosGrafica.map(item => ({
                        x: item.tiempo,
                        y: item.corriente
                    })),
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    tension: 0.1
                }]
            },

            options: {

                responsive: true,
                maintainAspectRatio: false,

                interaction: {
                    mode: 'nearest',
                    intersect: false
                },

                parsing: false,

                animation: false,

                scales: {

                    x: {
                        type: 'linear',

                        title: {
                            display: true,
                            text: 'Tiempo transcurrido (s)'
                        }
                    },

                    y: {
                        title: {
                            display: true,
                            text: 'Corriente (A)'
                        }
                    }

                },

                plugins: {

                    legend: {
                        display: false
                    },

                    tooltip: {

                        callbacks: {

                            title: function(context) {

                                const segundos =
                                    context[0].parsed.x;

                                return formatearTiempo(segundos);
                            },

                            label: function(context) {

                                const config =
                                    configuracionVariables[variableActual];

                                return (
                                    config.titulo +
                                    ': ' +
                                    context.parsed.y.toFixed(2) +
                                    ' ' +
                                    config.unidad
                                );
                            }

                        }

                    }

                }

            }

        });


        function cambiarVariable(variable) {

            variableActual = variable;

            const config =
                configuracionVariables[variable];

            chart.data.datasets[0].label =
                config.titulo;

            chart.data.datasets[0].data =
                datosGrafica.map(item => ({
                    x: item.tiempo,
                    y: item[variable]
                }));

            chart.options.scales.y.title.text =
                config.titulo + ' (' + config.unidad + ')';

            chart.update();
        }


        function formatearTiempo(segundos) {

            segundos = Math.max(0, Math.round(segundos));

            const horas =
                Math.floor(segundos / 3600);

            const minutos =
                Math.floor((segundos % 3600) / 60);

            const segundosRestantes =
                segundos % 60;

            return [
                horas.toString().padStart(2, '0'),
                minutos.toString().padStart(2, '0'),
                segundosRestantes.toString().padStart(2, '0')
            ].join(':');
        }


        document
            .querySelectorAll('.trend-btn')
            .forEach(boton => {

                boton.addEventListener('click', function() {

                    document
                        .querySelectorAll('.trend-btn')
                        .forEach(btn =>
                            btn.classList.remove('active')
                        );

                    this.classList.add('active');

                    cambiarVariable(
                        this.dataset.variable
                    );

                });

            });


        const datosCorrelaciones = @json($correlaciones);

        const configuracionCorrelaciones = {
            corriente_torque: { x:'corriente', y:'torque', tituloX:'Corriente', unidadX:'A', tituloY:'Torque', unidadY:'%' },
            corriente_temp_inversor: { x:'corriente', y:'temp_inversor', tituloX:'Corriente', unidadX:'A', tituloY:'Temperatura inversor', unidadY:'%' },
            bus_dc_temp_inversor: { x:'bus_dc', y:'temp_inversor', tituloX:'Bus DC', unidadX:'V', tituloY:'Temperatura inversor', unidadY:'%' },
            temp_ambiente_temp_inversor: { x:'temp_ambiente', y:'temp_inversor', tituloX:'Temperatura ambiente', unidadX:'°C', tituloY:'Temperatura inversor', unidadY:'%' }
        };

        let correlacionActual = 'corriente_torque';
        const correlationCtx = document.getElementById('correlationChart').getContext('2d');

        const correlationChart = new Chart(correlationCtx, {
            type: 'scatter',
            data: {
                datasets: [{
                    data: crearDatosCorrelacion(correlacionActual),
                    pointRadius: 3,
                    pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const c = configuracionCorrelaciones[correlacionActual];
                                return c.tituloX + ': ' + context.parsed.x.toFixed(2) + ' ' + c.unidadX +
                                    ' | ' + c.tituloY + ': ' + context.parsed.y.toFixed(2) + ' ' + c.unidadY;
                            }
                        }
                    }
                },
                scales: {
                    x: { title: { display: true, text: 'Corriente (A)' } },
                    y: { title: { display: true, text: 'Torque (%)' } }
                }
            }
        });

        function crearDatosCorrelacion(nombre) {
            const c = configuracionCorrelaciones[nombre];
            return datosGrafica.map(item => ({ x: item[c.x], y: item[c.y] }));
        }

        function interpretarCorrelacion(r) {
            const valor = Math.abs(r);
            let intensidad;
            if (valor >= 0.8) intensidad = 'Muy fuerte';
            else if (valor >= 0.6) intensidad = 'Fuerte';
            else if (valor >= 0.4) intensidad = 'Moderada';
            else if (valor >= 0.2) intensidad = 'Débil';
            else intensidad = 'Muy débil';

            if (r > 0) return intensidad + ' positiva';
            if (r < 0) return intensidad + ' negativa';
            return 'Sin correlación lineal';
        }

        function cambiarCorrelacion(nombre) {
            correlacionActual = nombre;
            const c = configuracionCorrelaciones[nombre];

            correlationChart.data.datasets[0].data = crearDatosCorrelacion(nombre);
            correlationChart.options.scales.x.title.text = c.tituloX + ' (' + c.unidadX + ')';
            correlationChart.options.scales.y.title.text = c.tituloY + ' (' + c.unidadY + ')';
            correlationChart.update();

            const r = datosCorrelaciones[nombre].r;
            document.getElementById('correlationValue').textContent = Number(r).toFixed(3);
            document.getElementById('correlationText').textContent = interpretarCorrelacion(r);
        }

        document.querySelectorAll('.correlation-btn').forEach(boton => {
            boton.addEventListener('click', function() {
                document.querySelectorAll('.correlation-btn').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                cambiarCorrelacion(this.dataset.correlation);
            });
        });

        cambiarCorrelacion('corriente_torque');

    

        // Eventos: 10 registros por página.
        const eventRows = Array.from(document.querySelectorAll('#eventos .event-row'));
        const eventPagination = document.getElementById('eventPagination');
        const eventPrevBtn = document.getElementById('eventPrevBtn');
        const eventNextBtn = document.getElementById('eventNextBtn');
        const eventPageIndicator = document.getElementById('eventPageIndicator');
        const eventPaginationInfo = document.getElementById('eventPaginationInfo');

        if (eventRows.length && eventPagination) {
            const eventsPerPage = 10;
            const totalEventPages = Math.ceil(eventRows.length / eventsPerPage);
            let currentEventPage = 1;

            function renderEventPage() {
                const start = (currentEventPage - 1) * eventsPerPage;
                const end = Math.min(start + eventsPerPage, eventRows.length);

                eventRows.forEach((row, index) => {
                    row.style.display = (index >= start && index < end) ? '' : 'none';
                });

                eventPaginationInfo.textContent = `Mostrando ${start + 1}–${end} de ${eventRows.length} eventos`;
                eventPageIndicator.textContent = `${currentEventPage} / ${totalEventPages}`;
                eventPrevBtn.disabled = currentEventPage === 1;
                eventNextBtn.disabled = currentEventPage === totalEventPages;
                eventPagination.style.display = totalEventPages > 1 ? 'flex' : 'none';
            }

            eventPrevBtn.addEventListener('click', () => {
                if (currentEventPage > 1) {
                    currentEventPage--;
                    renderEventPage();
                    document.getElementById('eventos').scrollIntoView({behavior:'smooth',block:'start'});
                }
            });

            eventNextBtn.addEventListener('click', () => {
                if (currentEventPage < totalEventPages) {
                    currentEventPage++;
                    renderEventPage();
                    document.getElementById('eventos').scrollIntoView({behavior:'smooth',block:'start'});
                }
            });

            renderEventPage();
        }

        // Navegación del dashboard: desplazamiento + sección activa.
        const dashboardLinks = Array.from(document.querySelectorAll('.dashboard-nav a[href^="#"]'));
        const dashboardSections = dashboardLinks
            .map(link => document.querySelector(link.getAttribute('href')))
            .filter(Boolean);

        dashboardLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (!target) return;
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                dashboardLinks.forEach(a => a.classList.remove('active-section'));
                this.classList.add('active-section');
            });
        });

        if (dashboardSections.length) {
            const sectionObserver = new IntersectionObserver((entries) => {
                const visible = entries
                    .filter(entry => entry.isIntersecting)
                    .sort((a,b) => b.intersectionRatio - a.intersectionRatio)[0];

                if (!visible) return;
                dashboardLinks.forEach(link => {
                    link.classList.toggle(
                        'active-section',
                        link.getAttribute('href') === '#' + visible.target.id
                    );
                });
            }, { rootMargin: '-18% 0px -65% 0px', threshold: [0, .15, .35] });

            dashboardSections.forEach(section => sectionObserver.observe(section));
        }

</script>

    @endif
</body>
</html>