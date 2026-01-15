# 🚀 Guía Rápida de Instalación

Esta guía te ayudará a configurar el proyecto **Restaurante Aulaga** en pocos minutos.

## ⚡ Inicio Rápido

### 1. Requisitos Previos
- PHP 7.4 o superior
- MySQL/MariaDB
- Servidor web (XAMPP, WAMP, LAMP, etc.)

### 2. Clonar el Repositorio
```bash
git clone https://github.com/Haise232/Proyecto-Aulaga.git
cd Proyecto-Aulaga
```

### 3. Configurar Base de Datos

#### 3.1 Crear la Base de Datos
```sql
CREATE DATABASE restaurante_aulaga CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### 3.2 Importar las Tablas
```bash
# Opción 1: Usar el script completo (incluye datos de ejemplo)
mysql -u root -p restaurante_aulaga < sql/instalacion_completa.sql

# Opción 2: Crear solo las tablas sin datos
mysql -u root -p restaurante_aulaga < sql/menu_semanal.sql
```

### 4. Configurar Conexión

Copia y edita el archivo de configuración:
```bash
cp config/db.example.php config/db.php
```

Edita `config/db.php` con tus credenciales:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'restaurante_aulaga');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
```

### 5. Acceder a la Aplicación

Coloca el proyecto en tu servidor web y accede:
- **Página principal**: `http://localhost/Proyecto-Aulaga/`
- **Admin**: `http://localhost/Proyecto-Aulaga/admin.php`
- **Contraseña por defecto**: `admin123`

## 🔐 Cambiar Contraseña de Admin

1. Abre en el navegador: `http://localhost/Proyecto-Aulaga/hash_generator.php`
2. Copia el hash generado
3. Edita `config/auth.php` y reemplaza el valor de `ADMIN_PASSWORD_HASH`

## ✅ Verificación

Si todo está correcto:
- ✅ La página principal muestra el restaurante
- ✅ El menú del día aparece (si hay datos de ejemplo)
- ✅ Puedes hacer login en el admin
- ✅ Puedes añadir, editar y eliminar platos

## 🐛 Problemas Comunes

**Error de conexión a BD:**
- Verifica que MySQL esté activo
- Comprueba las credenciales en `config/db.php`
- Asegúrate de que la base de datos existe

**No se muestra el menú:**
- Ejecuta el script con datos de ejemplo: `sql/instalacion_completa.sql`
- O añade platos desde el panel de administración

**No puedo hacer login:**
- La contraseña por defecto es `admin123`
- Verifica que el archivo `config/auth.php` existe

## 📚 Documentación Completa

Para más detalles, consulta el [README.md](README.md) completo.

---

💡 **¿Necesitas ayuda?** Abre un issue en GitHub.
