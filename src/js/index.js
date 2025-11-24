let menuDelDia = [];
let editingId = null;
let reservasEfectuadas = [];

// Constantes de Alérgenos
const ALERGENOS = {
    "gluten": { label: "Gluten", icon: "🌾" },
    "crustaceos": { label: "Crustáceos", icon: "🦀" },
    "huevos": { label: "Huevos", icon: "🥚" },
    "pescado": { label: "Pescado", icon: "🐟" },
    "cacahuetes": { label: "Cacahuetes", icon: "🥜" },
    "soja": { label: "Soja", icon: "🫘" },
    "lacteos": { label: "Lácteos", icon: "🥛" },
    "frutos_cascara": { label: "Frutos de cáscara", icon: "🌰" },
    "apio": { label: "Apio", icon: "🌿" },
    "mostaza": { label: "Mostaza", icon: "🌭" },
    "sesamo": { label: "Sésamo", icon: "🌱" },
    "sulfitos": { label: "Sulfitos", icon: "🍷" },
    "altramuces": { label: "Altramuces", icon: "🌼" },
    "moluscos": { label: "Moluscos", icon: "🐙" }
};

document.addEventListener('DOMContentLoaded', async () => {

    // Solo intentamos cargar datos del menú si estamos en la página pública
    if (document.getElementById('menuContainer')) {
        // En la página pública, llamamos sin argumento (usa el endpoint filtrado)
        await cargarMenu();
        renderMenu();
    }
    // La carga del admin se dispara mediante el script inline en admin.php 
    // y llamará a cargarMenu(true)
});

// =================================================================
// GESTIÓN DE DATOS (Interacción con la API PHP)
// =================================================================

// FUNCIÓN CORREGIDA: Acepta un parámetro para elegir el endpoint (público o admin)
async function cargarMenu(esAdmin = false) {
    // Si esAdmin es true, usa el endpoint admin_platos.php (SIN filtro WHERE).
    const endpoint = esAdmin ? './api/admin_platos.php' : './api/menu.php';

    try {
        const response = await fetch(endpoint);
        if (!response.ok) {
            throw new Error(`Error ${response.status} en la API al cargar el menú desde ${endpoint}.`);
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

    // Recoger alérgenos seleccionados
    const alergenosSeleccionados = [];
    document.querySelectorAll('input[name="nuevoAlergenos"]:checked').forEach(checkbox => {
        alergenosSeleccionados.push(checkbox.value);
    });

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
                imagen: imagen,
                alergenos: alergenosSeleccionados
            })
        });

        const result = await response.json();

        if (response.ok && result.success) {
            alert(result.success);
            cancelarNuevo();
            // Después de crear un plato, recargamos la lista con la versión de ADMIN
            await cargarMenu(true);
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
    // Se asegura de obtener el valor de 'activo'
    const activo = document.getElementById('editActivo') ? document.getElementById('editActivo').value : 1;

    // Recoger alérgenos seleccionados en edición
    const alergenosSeleccionados = [];
    document.querySelectorAll('input[name="editAlergenos"]:checked').forEach(checkbox => {
        alergenosSeleccionados.push(checkbox.value);
    });

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
                activo: parseInt(activo), // Envía el estado de activo
                alergenos: alergenosSeleccionados
            })
        });

        const result = await response.json();

        if (response.ok && result.success) {
            alert(result.success);

            editingId = null;
            // Después de editar, recargamos la lista con la versión de ADMIN
            await cargarMenu(true);
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
            // Después de eliminar, recargamos la lista con la versión de ADMIN
            await cargarMenu(true);
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

            await cargarReservas();   // Volver a cargar las reservas desde la BD
            renderReservasAdmin();    // Redibujar la lista de reservas
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

function renderAlergenosIcons(alergenosList) {
    if (!alergenosList || alergenosList.length === 0) return '';

    // Si viene como string JSON, parsearlo
    let lista = alergenosList;
    if (typeof lista === 'string') {
        try {
            lista = JSON.parse(lista);
        } catch (e) {
            lista = [];
        }
    }

    if (!Array.isArray(lista)) return '';

    let html = '<div class="alergenos-icons" style="margin-top: 0.5rem;">';
    lista.forEach(key => {
        if (ALERGENOS[key]) {
            html += `<span title="${ALERGENOS[key].label}" style="font-size: 1.2rem; margin-right: 5px; cursor: help;">${ALERGENOS[key].icon}</span>`;
        }
    });
    html += '</div>';
    return html;
}

function renderAlergenosCheckboxes(containerId, nameAttribute, selectedAlergenos = []) {
    const container = document.getElementById(containerId);
    if (!container) return;

    // Parsear si es string
    let selected = selectedAlergenos;
    if (typeof selected === 'string') {
        try {
            selected = JSON.parse(selected);
        } catch (e) {
            selected = [];
        }
    }
    if (!Array.isArray(selected)) selected = [];

    let html = '';
    for (const key in ALERGENOS) {
        const isChecked = selected.includes(key) ? 'checked' : '';
        html += `
            <label style="display: inline-flex; align-items: center; margin-right: 10px; margin-bottom: 5px; cursor: pointer;">
                <input type="checkbox" name="${nameAttribute}" value="${key}" ${isChecked} style="margin-right: 5px;">
                ${ALERGENOS[key].icon} ${ALERGENOS[key].label}
            </label>
        `;
    }
    container.innerHTML = html;
}

function renderMenu() {
    const container = document.getElementById('menuContainer');
    if (!container) return;

    const tipos = { 'primero': 'Primeros Platos', 'segundo': 'Segundos Platos', 'postre': 'Postres' };
    let html = '';

    for (let tipo in tipos) {
        const platos = menuDelDia.filter(p => p.tipo === tipo);
        if (platos.length > 0) {
            platos.forEach(plato => {
                const alergenosHtml = renderAlergenosIcons(plato.alergenos);
                html += `
                    <div class="plato-card">
                        <img src="${plato.imagen}" alt="${plato.nombre}">
                        <div class="plato-info">
                            <div class="plato-tipo">${tipos[tipo]}</div>
                            <h3 class="plato-nombre">${plato.nombre}</h3>
                            <p class="plato-descripcion">${plato.descripcion}</p>
                            ${alergenosHtml}
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

    // Iteramos sobre todos los platos, que ahora incluye activos e inactivos gracias a cargarMenu(true)
    menuDelDia.forEach(plato => {

        // Lógica de estado: usamos '==' ya que el valor puede ser un string ("1") o un número (1)
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
                    <div class="form-group">
                        <label>Alérgenos</label>
                        <div id="editAlergenosContainer_${plato.id}" class="alergenos-grid"></div>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <button onclick="guardarEdicion('${plato.id}')" class="btn btn-small">Guardar</button>
                        <button onclick="cancelarEdicion()" class="btn btn-danger btn-small">Cancelar</button>
                    </div>
                </div>
            `;

            // Renderizar checkboxes después de insertar el HTML
            setTimeout(() => {
                renderAlergenosCheckboxes(`editAlergenosContainer_${plato.id}`, 'editAlergenos', plato.alergenos);
            }, 0);

        } else {
            // --- CÓDIGO DE LA TARJETA DE VISUALIZACIÓN ---
            const alergenosHtml = renderAlergenosIcons(plato.alergenos);

            html += `
                <div class="plato-admin">
                    <img src="${plato.imagen}" alt="${plato.nombre}">
                    <div class="plato-admin-info">
                        <div class="plato-tipo">${plato.tipo.toUpperCase()}${estadoBadge}</div>
                        <h4>${plato.nombre}</h4>
                        <p style="color: #666;">${plato.descripcion}</p>
                        ${alergenosHtml}
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
    // Renderizar checkboxes para nuevo plato
    renderAlergenosCheckboxes('nuevoAlergenosContainer', 'nuevoAlergenos');
}

function cancelarNuevo() {
    document.getElementById('formNuevoPlato').classList.add('hidden');
    document.getElementById('nuevoNombre').value = '';
    document.getElementById('nuevoDescripcion').value = '';
    document.getElementById('nuevoImagen').value = '';
    // Limpiar checkboxes
    const container = document.getElementById('nuevoAlergenosContainer');
    if (container) container.innerHTML = '';
}


// =================================================================
// HEADER INTELIGENTE
// =================================================================

let prevScrollPos = window.pageYOffset;
const header = document.querySelector('header');
const headerHeight = header ? header.offsetHeight : 0;

window.onscroll = function () {
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
};

// =================================================================
// GESTIÓN DE MENÚ SEMANAL
// =================================================================

let semanaSeleccionada = null;

// Obtener el lunes de la semana actual
function obtenerLunesActual() {
    const hoy = new Date(); // Use Date object directly
    const diaSemana = hoy.getDay(); // 0 (domingo) a 6 (sábado)
    const diasHastaLunes = diaSemana === 0 ? -6 : 1 - diaSemana;
    const lunes = new Date(hoy);
    lunes.setDate(hoy.getDate() + diasHastaLunes);
    return lunes.toISOString().split('T')[0];
}

// Establecer el lunes actual en el selector
function setLunesActual() {
    const selector = document.getElementById('semanaSelector');
    if (selector) {
        selector.value = obtenerLunesActual();
    }
}

// Cargar la semana seleccionada
async function cargarSemana() {
    const selector = document.getElementById('semanaSelector');
    const fecha = selector.value;

    if (!fecha) {
        alert('Por favor selecciona una fecha');
        return;
    }

    // Verificar que sea lunes
    const fechaObj = new Date(fecha + 'T00:00:00');
    const diaSemana = fechaObj.getDay();
    if (diaSemana !== 1) {
        alert('Por favor selecciona un lunes');
        return;
    }

    semanaSeleccionada = fecha;

    // Calcular domingo
    const domingo = new Date(fechaObj);
    domingo.setDate(fechaObj.getDate() + 6);
    const domingoStr = domingo.toISOString().split('T')[0];

    // Mostrar info de semana
    const semanaInfo = document.getElementById('semanaInfo');
    const semanaTexto = document.getElementById('semanaTexto');
    semanaTexto.textContent = `${fecha} al ${domingoStr}`;
    semanaInfo.style.display = 'block';

    // Cargar menú semanal
    try {
        const response = await fetch(`./api/menu_semanal.php?fecha=${fecha}`);
        const data = await response.json();

        if (data.error) {
            console.error('Error:', data.error);
            renderPlatosDisponibles([]);
        } else {
            renderPlatosDisponibles(data.platos || []);
        }
    } catch (error) {
        console.error('Error al cargar menú semanal:', error);
        renderPlatosDisponibles([]);
    }
}

// Renderizar platos disponibles con checkboxes
function renderPlatosDisponibles(platosAsignados) {
    const container = document.getElementById('platosDisponibles');
    if (!container) return;

    // Obtener IDs de platos ya asignados
    const idsAsignados = platosAsignados.map(p => p.id);

    // Agrupar platos por tipo
    const tipos = { 'primero': [], 'segundo': [], 'postre': [] };
    menuDelDia.forEach(plato => {
        if (plato.activo == 1 && tipos[plato.tipo]) {
            tipos[plato.tipo].push(plato);
        }
    });

    let html = '';

    for (const tipo in tipos) {
        const platos = tipos[tipo];
        if (platos.length === 0) continue;

        const tipoLabel = tipo === 'primero' ? 'Primeros Platos' :
            tipo === 'segundo' ? 'Segundos Platos' : 'Postres';

        html += `<div class="tipo-section">
                    <h5 style="color: #2c5f2d; margin: 1rem 0 0.5rem 0;">${tipoLabel}</h5>`;

        platos.forEach(plato => {
            const isChecked = idsAsignados.includes(plato.id) ? 'checked' : '';
            html += `
                <label class="plato-selectable">
                    <input type="checkbox" name="platoSemanal" value="${plato.id}" ${isChecked}>
                    <img src="${plato.imagen}" alt="${plato.nombre}">
                    <div class="plato-selectable-info">
                        <strong>${plato.nombre}</strong>
                        <p>${plato.descripcion}</p>
                    </div>
                </label>
            `;
        });

        html += '</div>';
    }

    container.innerHTML = html || '<p style="color: #666;">No hay platos disponibles</p>';
}

// Guardar menú semanal
async function guardarMenuSemanal() {
    if (!semanaSeleccionada) {
        alert('Por favor selecciona una semana primero');
        return;
    }

    // Obtener platos seleccionados
    const checkboxes = document.querySelectorAll('input[name="platoSemanal"]:checked');
    const platoIds = Array.from(checkboxes).map(cb => parseInt(cb.value));

    if (platoIds.length === 0) {
        if (!confirm('No has seleccionado ningún plato. ¿Deseas limpiar el menú de esta semana?')) {
            return;
        }
    }

    try {
        const response = await fetch('./api/asignar_menu_semanal.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                semana_inicio: semanaSeleccionada,
                plato_ids: platoIds
            })
        });

        const data = await response.json();

        if (data.error) {
            alert('Error: ' + data.error);
        } else {
            alert(`✅ Menú semanal guardado correctamente.\n${data.platos_asignados} platos asignados.`);
            // Recargar la semana
            cargarSemana();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error de red al guardar el menú semanal');
    }
}