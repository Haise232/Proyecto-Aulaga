-- =========================================================
-- SCRIPT DE INSTALACIÓN COMPLETA - RESTAURANTE AULAGA
-- =========================================================
-- Este script crea todas las tablas necesarias para el funcionamiento del sistema
-- Ejecutar en orden: primero crear la base de datos, luego ejecutar este script
-- =========================================================
-- 1. TABLA DE PLATOS
-- =========================================================
CREATE TABLE IF NOT EXISTS platos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('primero', 'segundo', 'postre') NOT NULL COMMENT 'Tipo de plato: primero, segundo o postre',
    nombre VARCHAR(255) NOT NULL COMMENT 'Nombre del plato',
    descripcion TEXT COMMENT 'Descripción detallada del plato',
    imagen VARCHAR(500) COMMENT 'URL de la imagen del plato',
    alergenos JSON COMMENT 'Array JSON con códigos de alérgenos',
    activo TINYINT(1) DEFAULT 1 COMMENT '1 = visible en menú público, 0 = oculto',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación del registro',
    INDEX idx_tipo (tipo),
    INDEX idx_activo (activo)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Platos del restaurante con información de alérgenos';
-- =========================================================
-- 2. TABLA DE RESERVAS
-- =========================================================
CREATE TABLE IF NOT EXISTS reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL COMMENT 'Nombre completo del cliente',
    email VARCHAR(255) NOT NULL COMMENT 'Email del cliente para confirmación',
    telefono VARCHAR(20) COMMENT 'Teléfono de contacto',
    fecha DATE NOT NULL COMMENT 'Fecha de la reserva',
    hora TIME NOT NULL COMMENT 'Hora de la reserva',
    personas VARCHAR(10) NOT NULL COMMENT 'Número de personas (1-6 o "7+")',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación de la reserva',
    INDEX idx_fecha (fecha),
    INDEX idx_email (email)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Reservas de mesas del restaurante';
-- =========================================================
-- 3. TABLA DE MENÚ SEMANAL
-- =========================================================
CREATE TABLE IF NOT EXISTS menu_semanal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plato_id INT NOT NULL COMMENT 'ID del plato asignado',
    semana_inicio DATE NOT NULL COMMENT 'Fecha de inicio de la semana (lunes)',
    semana_fin DATE NOT NULL COMMENT 'Fecha de fin de la semana (domingo)',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación del registro',
    FOREIGN KEY (plato_id) REFERENCES platos(id) ON DELETE CASCADE,
    INDEX idx_semana (semana_inicio, semana_fin),
    INDEX idx_plato (plato_id),
    UNIQUE KEY unique_plato_semana (plato_id, semana_inicio) COMMENT 'Evita duplicados: mismo plato en la misma semana'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Asignación de platos a semanas específicas';
-- =========================================================
-- 4. DATOS DE EJEMPLO (OPCIONAL)
-- =========================================================
-- Insertar platos de ejemplo
INSERT INTO platos (
        tipo,
        nombre,
        descripcion,
        imagen,
        alergenos,
        activo
    )
VALUES (
        'primero',
        'Ensalada César',
        'Lechuga romana, pollo a la parrilla, crutones y aderezo César',
        'https://images.unsplash.com/photo-1546793665-c74683f339c1?w=400&h=300&fit=crop',
        '["gluten", "lacteos", "huevos"]',
        1
    ),
    (
        'primero',
        'Sopa de Verduras',
        'Sopa casera con verduras frescas de temporada',
        'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=400&h=300&fit=crop',
        '["apio"]',
        1
    ),
    (
        'segundo',
        'Pollo al Horno',
        'Muslo de pollo al horno con patatas asadas',
        'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?w=400&h=300&fit=crop',
        '[]',
        1
    ),
    (
        'segundo',
        'Merluza a la Plancha',
        'Filete de merluza con guarnición de verduras',
        'https://images.unsplash.com/photo-1580959375944-1ab5b8c5f596?w=400&h=300&fit=crop',
        '["pescado"]',
        1
    ),
    (
        'postre',
        'Flan Casero',
        'Flan de huevo tradicional con caramelo',
        'https://images.unsplash.com/photo-1624353365286-3f8d62daad51?w=400&h=300&fit=crop',
        '["huevos", "lacteos"]',
        1
    ),
    (
        'postre',
        'Macedonia de Frutas',
        'Frutas frescas de temporada',
        'https://images.unsplash.com/photo-1564093497595-593b96d80180?w=400&h=300&fit=crop',
        '[]',
        1
    );
-- =========================================================
-- 5. LISTA DE ALÉRGENOS DISPONIBLES
-- =========================================================
-- Los siguientes códigos de alérgenos están soportados en el sistema:
-- 
-- "gluten"          - Gluten (🌾)
-- "crustaceos"      - Crustáceos (🦀)
-- "huevos"          - Huevos (🥚)
-- "pescado"         - Pescado (🐟)
-- "cacahuetes"      - Cacahuetes (🥜)
-- "soja"            - Soja (🫘)
-- "lacteos"         - Lácteos (🥛)
-- "frutos_cascara"  - Frutos de cáscara (🌰)
-- "apio"            - Apio (🌿)
-- "mostaza"         - Mostaza (🌭)
-- "sesamo"          - Sésamo (🌱)
-- "sulfitos"        - Sulfitos (🍷)
-- "altramuces"      - Altramuces (🌼)
-- "moluscos"        - Moluscos (🐙)
--
-- Ejemplo de uso en INSERT:
-- INSERT INTO platos (tipo, nombre, descripcion, imagen, alergenos, activo) 
-- VALUES ('primero', 'Pasta Carbonara', 'Pasta con salsa carbonara', 'url_imagen', '["gluten", "huevos", "lacteos"]', 1);
-- =========================================================
-- FIN DEL SCRIPT DE INSTALACIÓN
-- =========================================================