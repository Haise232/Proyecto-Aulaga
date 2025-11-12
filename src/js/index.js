let menuDelDia = [];
let editingId = null;
let reservasEfectuadas = [];

document.addEventListener('DOMContentLoaded', async () => {
    
    // Solo intentamos cargar datos del menú si estamos en la página pública
    if (document.getElementById('menuContainer')) {
        await cargarMenu();
        renderMenu();
    }
    // La carga del admin ahora se dispara mediante el script inline en admin.php 
    // después de que PHP verifica la sesión.
});

// =================================================================
// GESTIÓN DE DATOS (Interacción con la API PHP)
// =================================================================

async function cargarMenu() {
    try {
        const response = await fetch('./api/menu.php'); 
        if (!response.ok) {
            throw new Error(`Error ${response.status} en la API al cargar el menú.`);
        }
        menuDelDia = await response.json();
    } catch (error) {
        console.error('Error cargando menú desde BD:', error);
        menuDelDia = [];
    }
}

async function cargarReservas() {
    try {
        // Este endpoint está protegido y solo funciona si el usuario está logeado.
        const response = await fetch('./api/reservas.php'); 
        if (!response.ok) {
            // Si la respuesta es 401, el usuario no está logeado.
            if (response.status !== 401) {
                throw new Error(`Error ${response.status} en la API al cargar reservas.`);
            }
        }
        reservasEfectuadas = await response.json();
    } catch (error) {
        console.error('Error cargando reservas desde BD:', error);
        reservasEfectuadas = []; 
    }
}

// =================================================================
// CRUD - PLATO
// =================================================================

async function guardarNuevoPlato() {
    const nombre = document.getElementById('nuevoNombre').value;
    const descripcion = document.getElementById('nuevoDescripcion').value;
    const imagen = document.getElementById('nuevoImagen').value;
    const tipo = document.getElementById('nuevoTipo').value;

    if (!nombre || !descripcion || !imagen) {
        alert('Por favor completa todos los campos.');
        return;
    }

    try {
        const response = await fetch('./api/crear_plato.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                tipo: tipo,
                nombre: nombre,
                descripcion: descripcion,
                imagen: imagen
            }) 
        });

        const result = await response.json();

        if (response.ok && result.success) {
            alert(result.success);
            cancelarNuevo();
            await cargarMenu();
            renderPlatosAdmin();
        } else {
            alert('Error al añadir el plato: ' + (result.error || response.statusText));
        }

    } catch (error) {
        console.error('Error de conexión o fetch:', error);
        alert('Error de red al intentar añadir el plato.');
    }
}


async function guardarEdicion(id) {
    const plato = menuDelDia.find(p => p.id == id);
    if (!plato) return;

    const tipo = document.getElementById('editTipo').value;
    const nombre = document.getElementById('editNombre').value;
    const descripcion = document.getElementById('editDescripcion').value;
    const imagen = document.getElementById('editImagen').value;
    const activo = document.getElementById('editActivo') ? document.getElementById('editActivo').value : 1;

    if (!nombre || !descripcion || !imagen) {
        alert('Por favor complete todos los campos.');
        return;
    }

    try {
        const response = await fetch('./api/editar_plato.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                id: id,
                tipo: tipo,
                nombre: nombre,
                descripcion: descripcion,
                imagen: imagen,
                activo: parseInt(activo)
            }) 
        });

        const result = await response.json();

        if (response.ok && result.success) {
            alert(result.success);
            
            editingId = null;
            await cargarMenu();       
            renderPlatosAdmin();      
        } else {
            alert('Error al actualizar el plato: ' + (result.error || response.statusText));
        }

    } catch (error) {
        console.error('Error de conexión o fetch:', error);
        alert('Error de red al intentar actualizar el plato.');
    }
}


async function eliminarPlato(id) {
    if (!confirm('¿Estás seguro de eliminar este plato? Esta acción es permanente.')) {
        return;
    }

    try {
        const response = await fetch('./api/eliminar_plato.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id })
        });

        const result = await response.json();

        if (response.ok && result.success) {
            alert(result.success);
            await cargarMenu();       
            renderPlatosAdmin();      
        } else {
            alert('Error al eliminar el plato: ' + (result.error || response.statusText));
        }

    } catch (error) {
        console.error('Error de conexión o fetch:', error);
        alert('Error de red al intentar eliminar el plato.');
    }
}


// =================================================================
// CRUD - RESERVA
// =================================================================

async function eliminarReserva(id) {
    if (!confirm('¿Estás seguro de eliminar esta reserva? Esta acción es permanente y actualiza la base de datos.')) {
        return;
    }

    try {
        const response = await fetch('./api/eliminar_reserva.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id }) // Envía el ID de la reserva
        });

        const result = await response.json();

        if (response.ok && result.success) {
            alert(result.success);
            
            await cargarReservas();   // Volver a cargar las reservas desde la BD
            renderReservasAdmin();    // Redibujar la lista de reservas
        } else {
            alert('Error al eliminar la reserva: ' + (result.error || response.statusText));
        }

    } catch (error) {
        console.error('Error de conexión o fetch:', error);
        alert('Error de red al intentar eliminar la reserva.');
    }
}

function enviarReserva(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData);
    
    // Aquí puedes añadir la lógica de fetch para enviar la reserva al endpoint PHP (Ej: api/guardar_reserva.php)
    
    alert(`📢 Enviando su reserva al servidor...\n\nNombre: ${data.nombre}\nFecha: ${data.fecha} a las ${data.hora}`);
    
    event.target.submit(); 
}

// =================================================================
// LÓGICA DEL PANEL DE ADMINISTRACIÓN (Login/Logout Sólido)
// =================================================================

async function login() {
    const passwordInput = document.getElementById('adminPassword');
    const password = passwordInput.value;

    try {
        const response = await fetch('./api/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ password: password }) 
        });

        const result = await response.json();

        if (response.ok && result.success) {
            // Recargar la página para que PHP detecte la sesión
            window.location.reload(); 
            
        } else {
            // Si el login falla por 401, el error viene del servidor PHP (Contraseña incorrecta)
            alert(result.error || 'Error de conexión al servidor.');
            passwordInput.value = ''; // Limpiar campo en caso de error
        }

    } catch (error) {
        console.error('Error de red:', error);
        alert('No se pudo conectar con el servidor para iniciar sesión.');
    }
}

function logout() {
    // Redirigir al endpoint que destruye la sesión y luego redirige a admin.php
    window.location.href = './api/logout.php';
}

// =================================================================
// RENDERING & UI
// =================================================================

function renderMenu() {
    const container = document.getElementById('menuContainer');
    if (!container) return;

    const tipos = {'primero': 'Primeros Platos', 'segundo': 'Segundos Platos', 'postre': 'Postres'};
    let html = '';
    
    for (let tipo in tipos) {
        const platos = menuDelDia.filter(p => p.tipo === tipo);
        if (platos.length > 0) {
            platos.forEach(plato => {
                html += `
                    <div class="plato-card">
                        <img src="${plato.imagen}" alt="${plato.nombre}">
                        <div class="plato-info">
                            <div class="plato-tipo">${tipos[tipo]}</div>
                            <h3 class="plato-nombre">${plato.nombre}</h3>
                            <p class="plato-descripcion">${plato.descripcion}</p>
                        </div>
                    </div>
                `;
            });
        }
    }
    container.innerHTML = html || '<p style="text-align: center; color: #666;">No hay platos disponibles</p>';
}

function renderPlatosAdmin() {
    const container = document.getElementById('platosAdmin');
    if (!container) return;

    let html = '';
    
    // Iteramos sobre todos los platos, ASUMIENDO que menuDelDia contiene todos los platos
    menuDelDia.forEach(plato => {
        
        // Lógica de estado CORREGIDA:
        const esActivo = plato.activo == 1;
        
        const estadoBadge = esActivo 
            ? '<span style="background: #4CAF50; color: white; padding: 0.2rem 0.5rem; border-radius: 3px; font-size: 0.8rem; margin-left: 0.5rem;">ACTIVO</span>' // 🟢 Si es 1, está ACTIVO
            : '<span style="background: #999; color: white; padding: 0.2rem 0.5rem; border-radius: 3px; font-size: 0.8rem; margin-left: 0.5rem;">INACTIVO</span>'; // ⚪ Si es 0, está INACTIVO
        
        
        // Comprobamos si el plato actual está en modo edición
        if (editingId == plato.id) {
            // --- CÓDIGO DEL FORMULARIO DE EDICIÓN ---
            html += `
                <div class="edit-form">
                    <h4 style="margin-bottom: 1rem; color: #2c5f2d;">Editar Plato</h4>
                    <div class="form-group">
                        <label>Tipo</label>
                        <select id="editTipo">
                            <option value="primero" ${plato.tipo === 'primero' ? 'selected' : ''}>Primer Plato</option>
                            <option value="segundo" ${plato.tipo === 'segundo' ? 'selected' : ''}>Segundo Plato</option>
                            <option value="postre" ${plato.tipo === 'postre' ? 'selected' : ''}>Postre</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" id="editNombre" value="${plato.nombre}">
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <input type="text" id="editDescripcion" value="${plato.descripcion}">
                    </div>
                    <div class="form-group">
                        <label>URL Imagen</label>
                        <input type="text" id="editImagen" value="${plato.imagen}">
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select id="editActivo">
                            <option value="1" ${plato.activo == 1 ? 'selected' : ''}>Activo (visible en menú)</option>
                            <option value="0" ${plato.activo == 0 ? 'selected' : ''}>Inactivo (oculto)</option>
                        </select>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <button onclick="guardarEdicion('${plato.id}')" class="btn btn-small">Guardar</button>
                        <button onclick="cancelarEdicion()" class="btn btn-danger btn-small">Cancelar</button>
                    </div>
                </div>
            `;
        } else {
            // --- CÓDIGO DE LA TARJETA DE VISUALIZACIÓN ---
            
            html += `
                <div class="plato-admin">
                    <img src="${plato.imagen}" alt="${plato.nombre}">
                    <div class="plato-admin-info">
                        <div class="plato-tipo">${plato.tipo.toUpperCase()}${estadoBadge}</div>
                        <h4>${plato.nombre}</h4>
                        <p style="color: #666;">${plato.descripcion}</p>
                    </div>
                    <div class="plato-admin-actions">
                        <button onclick="editarPlato('${plato.id}')" class="btn btn-small">✏️ Editar</button>
                        <button onclick="eliminarPlato('${plato.id}')" class="btn btn-danger btn-small">🗑️ Eliminar</button>
                    </div>
                </div>
            `;
        }
    });

    container.innerHTML = html;
}

function renderReservasAdmin() {
    const container = document.getElementById('reservasAdmin');
    if (!container) return;

    if (reservasEfectuadas.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #666;">Aún no hay reservas efectuadas.</p>';
        return;
    }

    let html = '<h3>Reservas Recientes</h3>';
    
    reservasEfectuadas.forEach(reserva => {
        const fechaCreacion = new Date(reserva.fecha_creacion).toLocaleString('es-ES', { dateStyle: 'short', timeStyle: 'short' });
        
        html += `
            <div class="reserva-card">
                <div class="reserva-info">
                    <strong>Fecha Reserva:</strong> ${reserva.fecha} a las ${reserva.hora.substring(0, 5)}<br>
                    <strong>Personas:</strong> ${reserva.personas}<br>
                    <strong>Nombre:</strong> ${reserva.nombre}<br>
                    <strong>Contacto:</strong> ${reserva.email} | ${reserva.telefono}<br>
                    <small>Creada: ${fechaCreacion}</small>
                </div>
                <button onclick="eliminarReserva('${reserva.id}')" class="btn btn-danger btn-small">Eliminar</button>
            </div>
        `;
    });

    container.innerHTML = html;
}

// =================================================================
// LÓGICA DE INTERFAZ DEL ADMIN (AUXILIARES)
// =================================================================

function editarPlato(id) {
    editingId = id;
    renderPlatosAdmin();
}

function cancelarEdicion() {
    editingId = null;
    renderPlatosAdmin();
}

function mostrarFormularioNuevo() {
    document.getElementById('formNuevoPlato').classList.remove('hidden');
}

function cancelarNuevo() {
    document.getElementById('formNuevoPlato').classList.add('hidden');
    document.getElementById('nuevoNombre').value = '';
    document.getElementById('nuevoDescripcion').value = '';
    document.getElementById('nuevoImagen').value = '';
}


// =================================================================
// HEADER INTELIGENTE
// =================================================================

let prevScrollPos = window.pageYOffset;
const header = document.querySelector('header');
const headerHeight = header ? header.offsetHeight : 0; 

window.onscroll = function() {
    if (!header) return;
    const currentScrollPos = window.pageYOffset;

    if (currentScrollPos <= 50) { 
        header.classList.remove('header-hidden');
    } 
    else if (prevScrollPos > currentScrollPos) {
        header.classList.remove('header-hidden');
    } 
    else if (prevScrollPos < currentScrollPos && currentScrollPos > headerHeight) {
        header.classList.add('header-hidden');
    }
    
    prevScrollPos = currentScrollPos;
}