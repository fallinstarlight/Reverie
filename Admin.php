<?php
require_once 'includes/auth.php';
auth::requireAdmin();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reverie: Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">

</head>

<body style="background-color:#fff1f5;">

    <div class="container py-4" style="background-color: #fff1f5;">

        <div class="d-flex justify-content-between align-items-center mb-4" style="background-color: #fff1f5;">
            <img src="assets/photos/logo_nobg.png" height="80">

            <div class="d-flex align-items-center gap-3">
                <div class="home-icon" onclick="window.location.href='includes/logout.php'">
                    <svg viewBox="0 0 24 24">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </div>
            </div>
        </div>

        <div class="row" style="padding-bottom: 20pt;">

            <div class="col-lg-8">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="fw-bold text-pink">Resumen de ventas</h3>

                    <button class="btn botones">Hoy (cambiar)</button>
                </div>

                <div class="row g-3">

                    <div class="col-md-4">
                        <div class="card resumen-card text-center p-4" style="min-height: fit-content; height: 200px;">
                            <h1 class="fw-bold">23</h1>
                            <p>Productos vendidos</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card resumen-card text-center p-4" style="min-height: fit-content; height: 200px;">
                            <h3 class="fw-bold">$1275.00</h3>
                            <p></p>
                            <p>Dinero obtenido</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card resumen-card text-center p-3" style="min-height: fit-content; height: 200px; align-items: center;">
                            <img src="assets/photos/donut.png" class="img-fluid mb-2" style="max-height: auto; width: 100px">
                            <p>Producto más vendido</p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="d-grid gap-3">
                    <button class="btn botones py-3 fs-5">Lista de productos</button>
                    <button type="button" class="btn botones py-3 fs-5" onclick="window.location.href='employees-list.php'">Lista de empleados</button>
                    <button type="button" class="btn botones py-3 fs-5" onclick="window.location.href='sales-report.php'">Reportes</button>
                </div>
            </div>

        </div>
    </div>

    <!-- FOOTER -->
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