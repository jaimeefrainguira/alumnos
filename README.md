# Sistema de Gestión de Pagos de Alumnos

Aplicación MVC en PHP 8 + MySQL para administrar cuotas y abonos parciales.

## Requisitos
- PHP 8+
- MySQL 8+
- Composer
- Hosting compartido (AeonFree compatible)

## Instalación rápida
1. Importar `database/schema.sql` en la base de datos `mseet_41403283_alumos`.
2. Ejecutar `composer install`.
3. Subir todos los archivos al dominio (`alumnos.zya.me`) dejando `index.php` en la **raíz** del sitio.
4. Verificar que Apache tenga `mod_rewrite` activo (archivo `.htaccess` incluido).
5. Acceder con:
   - usuario: `admin`
   - clave: `admin123`

## Configuración MySQL por defecto
El archivo `config/database.php` ya viene preparado para tu hosting:
- Host: `sql211.hstn.me`
- Base de datos: `mseet_41403283_alumos`
- Usuario: `mseet_41403283`
- Contraseña: `4016508a8b`

## Módulos
- Login y sesiones seguras
- Gestión de alumnos
- Gestión de cuotas anuales
- Registro de abonos parciales por modal (AJAX)
- Matriz de pagos mensual (estado pagado/parcial/pendiente)
- Dashboard con Chart.js
- Buscador público + PDF individual
- PDF general para administración
