<?php
require_once 'includes/auth.php';
auth::requireAdmin();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles_reporte_ventas.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body style="background-color: #fff1f5;">

    <div class="d-flex align-items-center mb-4" style="padding-top: 3%;">
        <div class="home-icon" onclick="window.location.href='Admin.php'">
            <svg viewBox="0 0 24 24" width="24" height="24">
                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" />
            </svg>
        </div>
        <h3 class="header labels">Reporte de Ventas</h3>
    </div>
    <div class="container py-4 align-items-cente">
        <div>
            <div class="d-flex gap-3 mb-4">
                <select class="form-select filtro botones">
                    <option>Periodo: MES</option>
                </select>

                <select class="form-select filtro botones">
                    <option>Agrupar por: PRODUCTO</option>
                </select>

                <select class="form-select filtro botones">
                    <option>Métrica: PRODUCTO</option>
                </select>
            </div>

            <div class="tabla-container p-4"">
                <table class=" table tabla-ventas mb-0">
                <thead>
                    <tr>
                        <th>MES</th>
                        <th>VENTAS</th>
                        <th>CRECIMIENTO</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Enero</td>
                        <td>$7,600</td>
                        <td>15%</td>
                    </tr>
                    <tr>
                        <td>Febrero</td>
                        <td>$7,800</td>
                        <td>10%</td>
                    </tr>
                    <tr>
                        <td>Marzo</td>
                        <td>$5,000</td>
                        <td>-20%</td>
                    </tr>
                    <tr>
                        <td>Abril</td>
                        <td>$4,500</td>
                        <td>-10%</td>
                    </tr>
                </tbody>
                </table>
            </div>
        </div>

        <div style="padding-top: 3%;">
            <div class="resumen-card p-4 text-center">
                <h5 class="mb-4">Resumen de la venta</h5>
                <div class="grafico d-flex align-items-end justify-content-around mb-4">
                    <div class="barra" style="height:80px"></div>
                    <div class="barra" style="height:100px"></div>
                    <div class="barra" style="height:60px"></div>
                    <div class="barra" style="height:20px"></div>
                    <div class="barra" style="height:60px"></div>
                    <div class="barra" style="height:80px"></div>
                </div>

                <div class="d-flex justify-content-center gap-4">
                    <i class="bi bi-zoom-in icono-zoom"></i>
                    <i class="bi bi-zoom-out icono-zoom"></i>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Footer -->
    <footer style="padding-top: 10%; color:#ffffff">
        <div class="container" style="max-width: 100%; height: auto; min-height: 200px; background: linear-gradient(135deg, #e556a5 10%, #ff0d86 100%);">
            <br> Powered by Reverie<br>
            Copyright 2026<br>
            Luna Hidalgo Francisco Emmanuel<br>
            Arrieta Prado Isaaías<br>
            Tolentino Segovia Luis Fernando<br>
        </div>
    </footer>

</body>

</html>