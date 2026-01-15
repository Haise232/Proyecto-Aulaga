# 🌺 Restaurante Aulaga

> Sistema de gestión integral para restaurante con menú dinámico, sistema de reservas y panel de administración.

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)

## 📋 Descripción

**Restaurante Aulaga** es una aplicación web completa diseñada para la gestión de un restaurante escolar. El sistema permite:

- 📖 **Visualización del menú del día** con información detallada de platos y alérgenos
- 📅 **Gestión de menús semanales** para planificar con antelación
- 🎫 **Sistema de reservas** con confirmación por correo electrónico
- 🔐 **Panel de administración** protegido con autenticación
- ⚙️ **CRUD completo** para platos, alérgenos y reservas
- 📱 **Diseño responsive** adaptado a dispositivos móviles

## ✨ Características Principales

### Para Clientes
- Navegación intuitiva y diseño moderno
- Visualización del menú del día con imágenes
- Sistema de iconos para identificar alérgenos
- Formulario de reservas sencillo
- Confirmación de reserva por email

### Para Administradores
- Login seguro con gestión de sesiones PHP
- Gestión completa de platos (crear, editar, eliminar, activar/desactivar)
- Asignación de menús semanales
- Administración de alérgenos por plato
- Gestión de reservas recibidas
- Interfaz de administración intuitiva

## 🛠️ Tecnologías Utilizadas

### Backend
- **PHP 7.4+** - Lógica del servidor y API REST
- **MySQL/MariaDB** - Base de datos relacional
- **PDO** - Abstracción de base de datos con prepared statements

### Frontend
- **HTML5** - Estructura semántica
- **CSS3** - Diseño moderno y responsive
- **JavaScript ES6+** - Interactividad y consumo de API
- **Fetch API** - Comunicación asíncrona con el backend

### Seguridad
- Autenticación con sesiones PHP
- Hashing de contraseñas con `password_hash()`
- Prepared statements para prevenir SQL injection
- Validación de datos en cliente y servidor

## 📁 Estructura del Proyecto

```
Proyecto-Aulaga/
├── api/                          # Endpoints de la API REST
│   ├── admin_platos.php         # Obtener todos los platos (admin)
│   ├── asignar_menu_semanal.php # Asignar platos a semana
│   ├── crear_plato.php          # Crear nuevo plato
│   ├── editar_plato.php         # Editar plato existente
│   ├── eliminar_plato.php       # Eliminar plato
│   ├── eliminar_reserva.php     # Eliminar reserva
│   ├── login.php                # Autenticación de administrador
│   ├── logout.php               # Cerrar sesión
│   ├── menu.php                 # Obtener menú del día (público)
│   ├── menu_semanal.php         # Obtener menú de semana
│   ├── migration_alergenos.php  # Migración de alérgenos
│   └── reservas.php             # Gestión de reservas
├── config/                       # Configuración del sistema
│   ├── auth.php                 # Funciones de autenticación
│   └── db.php                   # Conexión a base de datos
├── includes/                     # Componentes reutilizables
│   ├── footer.php               # Pie de página
│   └── header.php               # Cabecera y navegación
├── sql/                          # Scripts de base de datos
│   └── menu_semanal.sql         # Tabla de menús semanales
├── src/                          # Recursos estáticos
│   ├── css/
│   │   └── style.css            # Estilos principales
│   ├── img/                     # Imágenes del proyecto
│   └── js/
│       └── index.js             # Lógica JavaScript principal
├── admin.php                     # Panel de administración
├── hash_generator.php            # Utilidad para generar hashes
├── index.php                     # Página principal
├── menu.php                      # Página del menú
├── procesar_reserva.php          # Procesamiento de reservas
├── reservas.php                  # Formulario de reservas
└── sql_alergenos.sql             # Datos de alérgenos
```

## 🚀 Instalación

### Prerrequisitos

- **PHP 7.4+** o superior
- **MySQL 5.7+** o **MariaDB 10.3+**
- **Servidor web** (Apache, Nginx, etc.)
- **Composer** (opcional, para dependencias futuras)

### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/tu-usuario/Proyecto-Aulaga.git
   cd Proyecto-Aulaga
   ```

2. **Configurar la base de datos**
   
   a. Crear la base de datos:
   ```sql
   CREATE DATABASE restaurante_aulaga CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

   b. Crear la tabla de platos:
   ```sql
   CREATE TABLE platos (
       id INT AUTO_INCREMENT PRIMARY KEY,
       tipo ENUM('primero', 'segundo', 'postre') NOT NULL,
       nombre VARCHAR(255) NOT NULL,
       descripcion TEXT,
       imagen VARCHAR(500),
       alergenos JSON,
       activo TINYINT(1) DEFAULT 1,
       fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
   ```

   c. Crear la tabla de reservas:
   ```sql
   CREATE TABLE reservas (
       id INT AUTO_INCREMENT PRIMARY KEY,
       nombre VARCHAR(255) NOT NULL,
       email VARCHAR(255) NOT NULL,
       telefono VARCHAR(20),
       fecha DATE NOT NULL,
       hora TIME NOT NULL,
       personas VARCHAR(10) NOT NULL,
       fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
   ```

   d. Importar tabla de menú semanal:
   ```bash
   mysql -u root -p restaurante_aulaga < sql/menu_semanal.sql
   ```

   e. Importar datos de alérgenos (opcional):
   ```bash
   mysql -u root -p restaurante_aulaga < sql_alergenos.sql
   ```

3. **Configurar la conexión a la base de datos**
   
   Editar el archivo `config/db.php` con tus credenciales:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'restaurante_aulaga');
   define('DB_USER', 'tu_usuario');
   define('DB_PASS', 'tu_contraseña');
   ```

4. **Generar hash de contraseña de administrador**
   
   a. Abrir en el navegador: `http://localhost/Proyecto-Aulaga/hash_generator.php`
   
   b. Copiar el hash generado
   
   c. Editar `config/auth.php` y actualizar el hash:
   ```php
   define('ADMIN_PASSWORD_HASH', 'tu_hash_aqui');
   ```

5. **Configurar servidor web**
   
   Apuntar el documento raíz a la carpeta del proyecto.

6. **Acceder a la aplicación**
   - **Página principal**: `http://localhost/Proyecto-Aulaga/`
   - **Panel admin**: `http://localhost/Proyecto-Aulaga/admin.php`
   - **Contraseña por defecto**: `admin123` (cámbiala en producción)

## 🎯 Uso

### Cliente

1. Navega a la página principal para ver información del restaurante
2. Visita "Menú del Día" para ver los platos disponibles
3. Accede a "Reservas" para solicitar una mesa
4. Recibirás confirmación por email (si está configurado)

### Administrador

1. Accede a `admin.php`
2. Inicia sesión con la contraseña configurada
3. Gestiona platos desde el panel:
   - Añadir nuevos platos con imágenes y alérgenos
   - Editar platos existentes
   - Activar/desactivar platos
   - Eliminar platos
4. Asigna menús semanales:
   - Selecciona un lunes
   - Marca los platos disponibles para esa semana
   - Guarda cambios
5. Gestiona reservas recibidas

## 🔒 Seguridad

- ✅ Contraseñas hasheadas con `password_hash()` y `password_verify()`
- ✅ Prepared statements en todas las consultas SQL
- ✅ Validación de sesiones PHP
- ✅ Protección de endpoints de administración
- ✅ Sanitización de entradas de usuario
- ⚠️ **Importante**: Cambiar la contraseña por defecto en producción
- ⚠️ **Importante**: Configurar HTTPS en producción
- ⚠️ **Importante**: Actualizar credenciales de base de datos

## 📧 Configuración de Email

Para habilitar el envío de confirmaciones por email, editar `procesar_reserva.php` según tu servidor SMTP o usar servicios como SendGrid, Mailgun, etc.

## 🌐 API Endpoints

### Públicos
- `GET /api/menu.php` - Obtener platos activos
- `GET /api/menu_semanal.php?fecha=YYYY-MM-DD` - Menú de la semana
- `POST /api/login.php` - Autenticación

### Protegidos (requieren autenticación)
- `GET /api/admin_platos.php` - Todos los platos (activos e inactivos)
- `GET /api/reservas.php` - Lista de reservas
- `POST /api/crear_plato.php` - Crear plato
- `POST /api/editar_plato.php` - Editar plato
- `POST /api/eliminar_plato.php` - Eliminar plato
- `POST /api/eliminar_reserva.php` - Eliminar reserva
- `POST /api/asignar_menu_semanal.php` - Asignar menú semanal

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add: nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📝 Licencia

Este proyecto es un proyecto educativo/de portafolio. Siéntete libre de usarlo como referencia.

## 👤 Autor

**Joaquín**

- GitHub: [@Haise232](https://github.com/Haise232)

## 🙏 Agradecimientos

- Proyecto desarrollado como parte de formación en desarrollo web
- Inspirado en sistemas reales de gestión de restaurantes escolares

---

⭐ Si este proyecto te ha sido útil, considera darle una estrella en GitHub
