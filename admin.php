<?php 
// 1. Incluir el gestor de autenticación
require_once 'config/auth.php'; 

// 2. Determinar si el usuario ya está logeado
$is_authenticated = is_logged_in();
?>

<?php include 'includes/header.php'; ?>

    <div id="admin">
        <section>
            <h2 class="section-title">Panel de Administración</h2>
            
            <div id="adminLogin" class="admin-login <?php echo $is_authenticated ? 'hidden' : ''; ?>">
                <h3 style="margin-bottom: 1rem; color: #2c5f2d;">Acceso Administrador</h3>
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" id="adminPassword" placeholder="Ingrese contraseña">
                </div>
                <button onclick="login()" class="btn" style="width: 100%;">Iniciar Sesión</button>
                <p style="margin-top: 1rem; color: #666; font-size: 0.9rem;">Contraseña: admin123</p>
            </div>

            <div id="adminPanel" class="admin-panel <?php echo !$is_authenticated ? 'hidden' : ''; ?>">
                
                <div class="admin-header">
                    <h3 style="color: #2c5f2d;">Gestión del Menú del Día</h3>
                    <button onclick="logout()" class="btn btn-danger btn-small">Cerrar Sesión</button>
                </div>

                <button onclick="mostrarFormularioNuevo()" class="btn" style="margin-bottom: 1rem;">
                    ➕ Añadir Nuevo Plato
                </button>

                <div id="formNuevoPlato" class="edit-form hidden">
                    <h4 style="margin-bottom: 1rem; color: #2c5f2d;">Nuevo Plato</h4>
                    <div class="form-group">
                        <label>Tipo</label>
                        <select id="nuevoTipo">
                            <option value="primero">Primer Plato</option>
                            <option value="segundo">Segundo Plato</option>
                            <option value="postre">Postre</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nombre del Plato</label>
                        <input type="text" id="nuevoNombre" placeholder="Nombre del plato">
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <input type="text" id="nuevoDescripcion" placeholder="Descripción del plato">
                    </div>
                    <div class="form-group">
                        <label>URL de la Imagen</label>
                        <input type="text" id="nuevoImagen" placeholder="https://...">
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <button onclick="guardarNuevoPlato()" class="btn">Guardar</button>
                        <button onclick="cancelarNuevo()" class="btn btn-danger">Cancelar</button>
                    </div>
                </div>

                <div id="platosAdmin"></div>
            
                ---

                <div class="admin-panel" style="margin-top: 2rem;">
                    <div class="admin-header">
                        <h3 style="color: #2c5f2d;">Gestión de Reservas</h3>
                    </div>
                    <div id="reservasAdmin">
                        </div>
                </div>
            </div>
        </section>
    </div>

<?php 
    // Código para iniciar la carga de datos del admin DESPUÉS de cargar index.js
    if ($is_authenticated): 
?>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        // Cargar y renderizar los datos desde la BD solo si el panel está visible
        await cargarMenu();
        renderPlatosAdmin();
        await cargarReservas();
        renderReservasAdmin();
    });
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>