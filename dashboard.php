<?php
require_once 'includes/auth.php';
auth::requireEmployee();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles_ticket.css">
    <link rel="stylesheet" href="css/styles.css">
    <title>Ticket de Venta</title>
</head>

<body style="background-color: #fff1f5">
    <div class="container" style="max-height: fit-content; padding-top: 2%;">
        <!-- Header -->
        <div class=" d-flex align-items-center mb-4">
            <div class="home-icon" onclick="window.location.href='includes/logout.php'">
                <svg viewBox="0 0 24 24">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </div>
            <div class="header labels">
                <h1>Ticket de Venta</h1>
                <div class="user-profile">
                    <img src="assets/photos/profile.png" alt="Usuario" class="user-avatar"
                        onclick="window.location.href='profile.php'">
                    <div class="user-info">
                        <p>Encargado panaderia y reposteria Spiral</p>
                        <h3>Usuario del Sistema</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content" style="padding-top: 2%;">
            <!-- Products Section -->
            <div class="products-section">
                <div>
                    <input type="text" class="form-control textboxes rounded-pill" placeholder="Buscar producto">
                </div>

                <!-- Product 1 - Dona -->
                <div class="product-item">
                    <div class="product-image">
                        <img src="assets/photos/donut.png" alt="Dona">
                    </div>
                    <div class="product-details">
                        <h3>Dona</h3>
                        <p class="location">Hu. Piezas de Pan</p>
                        <div class="price-info">
                            <span>Precio unitario: $3.33</span>
                            <span>Total: $9.99</span>
                        </div>
                    </div>
                    <div class="quantity-control">
                        <div class="quantity-box">X 3</div>
                        <div class="cancel-btn">Cancelar X</div>
                    </div>
                </div>

                <!-- Product 2 - Pan Blanco -->
                <div class="product-item">
                    <div class="product-image">
                        <img src="assets/photos/baguette.png" alt="Pan Blanco">
                    </div>
                    <div class="product-details">
                        <h3>Pan Blanco</h3>
                        <p class="location">Hu. Piezas de Pan</p>
                        <div class="price-info">
                            <span>Precio unitario: $5.55</span>
                            <span>Total: $5.55</span>
                        </div>
                    </div>
                    <div class="quantity-control">
                        <div class="quantity-box">X 2</div>
                        <div class="cancel-btn">Cancelar X</div>
                    </div>
                </div>

                <!-- Product 3 - Reposteria mousse de guayaba -->
                <div class="product-item">
                    <div class="product-image">
                        <img src="assets/photos/mousse.png" alt="Mousse">
                    </div>
                    <div class="product-details">
                        <h3>Reposteria mousse de guayaba</h3>
                        <p class="location">Hu. Piezas de Pan</p>
                        <div class="price-info">
                            <span>Precio unitario: $5.55</span>
                            <span>Total: $5.55</span>
                        </div>
                    </div>
                    <div class="quantity-control">
                        <div class="quantity-box">X 3</div>
                        <div class="cancel-btn">Cancelar X</div>
                    </div>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="labels summary-section">
                <h2>Resumen de la venta</h2>

                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>07new</td>
                            <td>1</td>
                            <td>$64</td>
                        </tr>
                        <tr>
                            <td>09new</td>
                            <td>3</td>
                            <td>$45</td>
                        </tr>
                        <tr>
                            <td>78buy</td>
                            <td>3</td>
                            <td>$89</td>
                        </tr>
                        <tr>
                            <td>98buy</td>
                            <td>7</td>
                            <td>$100</td>
                        </tr>
                    </tbody>
                </table>

                <div class="total-section">
                    <div class="labels total-row">
                        <h3>TOTAL:</h3>
                        <div class="labels total-amount">$307</div>
                    </div>
                    <button class="btn btn-primary w-100 botones labels">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
    <footer style="padding-top: 2%; color:#ffffff">
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