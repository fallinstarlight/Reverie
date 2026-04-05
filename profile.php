<?php
require_once 'includes/auth.php';
auth::requireEmployee();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/styles_perfil.css">
    <title>Perfil - Usuario</title>

</head>
<body style="background-color: #fff1f5">
    <div class="container">
        <!-- Header with Home Icon -->
        <div style="padding-top: 2%;">
            <div class="home-icon" onclick="window.location.href='dashboard.php'">
                <svg viewBox="0 0 24 24" width="24" height="24">
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" />
                </svg>
            </div>
        </div>

        <div class="main-content">
            <div class="labels profile-container">
                <!-- Profile Image -->
                <div class="profile-image-section">
                    <img src="assets/photos/profile.png" alt="Perfil" class="profile-image">
                </div>

                <!-- Profile Information -->
                <div class="profile-info-section">
                    <h1 class="profile-title">Perfil</h1>

                    <div class="info-row">
                        <span class="info-label">Usuario: Chester123</span>
                        <span class="info-value">Turno: Lunes y Miércoles</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Nombre: Chester</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Apellido: Bennington</span>
                    </div>

                    <div class="divider"></div>

                    <div class="sales-info">
                        <span class="sales-label">Ventas totales</span>
                        <span class="sales-amount">$14,000</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer style="padding-top: 10%; color:#ffffff">
        <div class="container" style="max-width: 100%; height: auto; min-height: 200px; background: linear-gradient(135deg, #e556a5 10%, #ff0d86 100%);">
           <br> Powered by Reverie<br>
            Copyright 2026<br>
            Luna Hidalgo Francisco Emmanuel<br>
            Arrieta Prado Isaaías<br>
            Tolentino Segovia Luis Fernando<br>
        </div>
    </footer>

    <script>
        // JavaScript sin funcionalidad, solo estructura
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Perfil de usuario cargado');
            
            // Referencias a elementos (sin funcionalidad)
            const homeIcon = document.querySelector('.home-icon');
            
            // Event listeners placeholder (sin funcionalidad)
            homeIcon.addEventListener('click', function() {
                // Placeholder para navegación a home
                console.log('Navegar a inicio');
            });
        });
    </script>
</body>
</html>