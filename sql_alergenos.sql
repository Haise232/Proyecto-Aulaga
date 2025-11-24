-- Ejecuta este comando en tu base de datos (phpMyAdmin o consola MySQL)
-- Esto añade la columna necesaria para guardar los alérgenos de cada plato.
ALTER TABLE platos
ADD COLUMN alergenos TEXT DEFAULT NULL
AFTER imagen;
-- EJEMPLOS DE CÓMO INSERTAR ALÉRGENOS (FORMATO JSON)
-- Copia y pega estos valores en la columna 'alergenos' o usa los comandos UPDATE.
-- Ejemplo 1: Plato con Gluten y Lácteos
-- UPDATE platos SET alergenos = '["gluten", "lacteos"]' WHERE id = 1;
-- Ejemplo 2: Plato con Huevos, Soja y Frutos de Cáscara
-- UPDATE platos SET alergenos = '["huevos", "soja", "frutos_cascara"]' WHERE id = 2;
-- Ejemplo 3: Plato con Pescado
-- UPDATE platos SET alergenos = '["pescado"]' WHERE id = 3;
-- LISTA COMPLETA DE CLAVES VÁLIDAS (según index.js):
-- "gluten", "crustaceos", "huevos", "pescado", "cacahuetes", "soja", 
-- "lacteos", "frutos_cascara", "apio", "mostaza", "sesamo", 
-- "sulfitos", "altramuces", "moluscos"