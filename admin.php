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
                    
                    <div class="form-group">
                        <label>Alérgenos</label>
                        <div id="nuevoAlergenosContainer" class="alergenos-grid">
                            <!-- Se llenará con JS -->
                        </div>
                    </div>

                    <div style="display: flex; gap: 0.5rem;">
                        <button onclick="guardarNuevoPlato()" class="btn">Guardar</button>
                        <button onclick="cancelarNuevo()" class="btn btn-danger">Cancelar</button>
                    </div>
                </div>

                <div id="platosAdmin"></div>
            
                <hr>
                
                <!-- GESTIÓN DE MENÚ SEMANAL -->
                <div class="admin-panel" style="margin-top: 2rem;">
                    <div class="admin-header">
                        <h3 style="color: #2c5f2d;">📅 Gestión de Menú Semanal</h3>
                    </div>
                    
                    <div class="form-group">
                        <label>Seleccionar Semana (Lunes)</label>
                        <input type="date" id="semanaSelector" class="semana-input">
                        <button onclick="cargarSemana()" class="btn btn-small" style="margin-left: 0.5rem;">Cargar Semana</button>
                    </div>
                    
                    <div id="semanaInfo" style="margin: 1rem 0; padding: 1rem; background: white; border-radius: 5px; display: none;">
                        <strong>Semana seleccionada:</strong> <span id="semanaTexto"></span>
                    </div>
                    
                    <h4 style="margin-top: 1.5rem; color: #2c5f2d;">Platos Disponibles</h4>
                    <p style="color: #666; font-size: 0.9rem;">Selecciona los platos que estarán disponibles en esta semana:</p>
                    
                    <div id="platosDisponibles" class="platos-disponibles-grid"></div>
                    
                    <button onclick="guardarMenuSemanal()" class="btn" style="margin-top: 1rem;">💾 Guardar Menú de la Semana</button>
                </div>
                
                <hr>
                
                <div class="admin-panel" style="margin-top: 2rem;">
                    <div class="admin-header">
                        <h3 style="color: #2c5f2d;">Gestión de Reservas</h3>
                    </div>
                    <div id="reservasAdmin"></div>
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
        // CORRECCIÓN CLAVE: Pasamos 'true' a cargarMenu() para que use
        // './api/admin_platos.php' (el endpoint que trae todos los platos).
        await cargarMenu(true); 
        
        renderPlatosAdmin();
        await cargarReservas();
        renderReservasAdmin();
        
        // Inicializar selector de semana con el lunes actual
        setLunesActual();
    });
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>