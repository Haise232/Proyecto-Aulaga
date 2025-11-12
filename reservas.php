<?php include 'includes/header.php'; ?>

    <div id="reservas">
        <section>
            <h2 class="section-title">Haz tu Reserva</h2>
            
            <?php
            // Lógica para mostrar mensajes de éxito/error después de enviar el formulario
            $status = $_GET['status'] ?? '';
            $message = $_GET['message'] ?? '';
            $color = '';

            if ($status === 'success') {
                $color = '#2c5f2d'; // Verde para éxito
            } elseif ($status === 'error' || $status === 'error_mail') {
                $color = '#d9534f'; // Rojo para error
            }

            if (!empty($message)) {
                // Muestra la caja de mensaje
                echo "
                    <div style='background-color: {$color}; color: white; padding: 1rem; border-radius: 5px; 
                        margin: 0 auto 1rem auto; max-width: 450px; 
                        text-align: center;'>
                        {$message}
                    </div>
                ";
            }
            ?>
            
            <div class="form-container">
                <form id="reservaForm" action="procesar_reserva.php" method="POST">
                    <div class="form-group">
                        <label for="nombre">Nombre completo</label>
                        <input type="text" name="nombre" id="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" name="telefono" id="telefono">
                    </div>
                    <div class="form-group">
                        <label for="fecha">Fecha</label>
                        <input type="date" name="fecha" id="fecha" required>
                    </div>
                    <div class="form-group">
                        <label for="hora">Hora</label>
                        <input type="time" name="hora" id="hora" required>
                    </div>
                    <div class="form-group">
                        <label for="personas">Número de personas</label>
                        <select name="personas" id="personas" required>
                            <option value="1">1 persona</option>
                            <option value="2" selected>2 personas</option>
                            <option value="3">3 personas</option>
                            <option value="4">4 personas</option>
                            <option value="5">5 personas</option>
                            <option value="6">6 personas</option>
                            <option value="7+">Más de 6 personas</option>
                        </select>
                    </div>
                    <button type="submit" class="btn" style="width: 100%;">Confirmar Reserva</button>
                </form>
            </div>
        </section>
    </div>

<?php include 'includes/footer.php'; ?>