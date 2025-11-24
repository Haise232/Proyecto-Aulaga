-- Crear tabla para gestión de menú semanal
-- Esta tabla permite asignar múltiples platos a cada semana
CREATE TABLE IF NOT EXISTS menu_semanal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plato_id INT NOT NULL,
    semana_inicio DATE NOT NULL,
    semana_fin DATE NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (plato_id) REFERENCES platos(id) ON DELETE CASCADE,
    INDEX idx_semana (semana_inicio, semana_fin),
    INDEX idx_plato (plato_id),
    -- Evitar duplicados: mismo plato en la misma semana
    UNIQUE KEY unique_plato_semana (plato_id, semana_inicio)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Ejemplo de cómo insertar platos para una semana
-- INSERT INTO menu_semanal (plato_id, semana_inicio, semana_fin) 
-- VALUES 
--   (1, '2024-11-25', '2024-12-01'),
--   (2, '2024-11-25', '2024-12-01'),
--   (3, '2024-11-25', '2024-12-01');