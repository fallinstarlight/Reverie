<?php
require_once 'includes/auth.php';
auth::requireAdmin();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Lista de empleados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles_lista_empleados.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

    <div class="container py-4">

        <div class="d-flex align-items-center mb-4">
            <div class="home-icon" onclick="window.location.href='Admin.php'">
                <svg viewBox="0 0 24 24" width="24" height="24">
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" />
                </svg>
            </div>
            <h3 class="header labels">Lista de empleados</h3>
        </div>

        <div class="row mb-4 justify-content-center">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" class="form-control rounded-pill textboxes" placeholder="Buscar empleado">
                    <span class="input-group-text bg-white border-0 botones">
                        <i class="bi bi-search"></i>
                    </span>
                </div>
            </div>

            <div class="col-md-3">
                <button class="btn btn-orden w-100 botones">
                    <i class="bi bi-list"></i> Ordenar por: ventas totales
                </button>
            </div>
        </div>

        <div class="card empleado-card mb-3">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <img src="assets/photos/profile.png" class="foto me-3">
                    <div>
                        <h5 class="labels">Chester Bennington</h5>
                        <p class="mb-1 labels-small"><strong>Turnos:</strong> Lunes, miércoles</p>
                        <p class="mb-1 labels-small">40 turnos completados</p>
                        <p class="mb-1 labels-small">37 ventas último turno</p>
                        <p class="mb-0 labels-small">943 ventas totales</p>
                    </div>
                </div>
                <i class="bi bi-pencil-square editar"></i>
            </div>
        </div>

        <!-- Empleado 2 -->
        <div class="card empleado-card mb-3">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <img src="assets/photos/roger.jpg" class="foto me-3">
                    <div>
                        <h5 class="labels">Roger Waters</h5>
                        <p class="mb-1 labels-small"><strong>Turnos:</strong> Martes, viernes</p>
                        <p class="mb-1 labels-small">34 turnos completados</p>
                        <p class="mb-1 labels-small">38 ventas último turno</p>
                        <p class="mb-0 labels-small">764 ventas totales</p>
                    </div>
                </div>
                <i class="bi bi-pencil-square editar"></i>
            </div>
        </div>

        <!-- Empleado 3 -->
        <div class="card empleado-card mb-3">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <img src="assets/photos/avril.avif" class="foto me-3">
                    <div>
                        <h5 class="labels">Avril Lavigne</h5>
                        <p class="mb-1 labels-small"><strong>Turnos:</strong> Miércoles, sábado</p>
                        <p class="mb-1 labels-small">27 turnos completados</p>
                        <p class="mb-1 labels-small">24 ventas último turno</p>
                        <p class="mb-0 labels-small">567 ventas totales</p>
                    </div>
                </div>
                <i class="bi bi-pencil-square editar"></i>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <footer style="padding-top: 10%; color:#ffffff">
        <div class="container"
            style="max-width: 100%; height: auto; min-height: 200px; background: linear-gradient(135deg, #e556a5 10%, #ff0d86 100%);">
            <br> Powered by Reverie<br>
            Copyright 2026<br>
            Luna Hidalgo Francisco Emmanuel<br>
            Arrieta Prado Isaaías<br>
            Tolentino Segovia Luis Fernando<br>
        </div>
    </footer>

</body>

</html>