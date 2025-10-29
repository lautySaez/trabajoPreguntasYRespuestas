# TRABAJO PRACTICO FINAL PROGRAMACION WEB 2

## 🚀 Configuración Inicial para Desarrolladores

### ⚠️ Error Común: "Class PHPMailer not found"

Si te aparece el error `Fatal error: Class "PHPMailer\PHPMailer\PHPMailer" not found`, sigue estos pasos:

### Opción 1: Instalación Automática (Recomendada)
1. Ejecuta el archivo `instalar_phpmailer.bat` haciendo doble clic
2. Sigue las instrucciones en pantalla
3. El script instalará automáticamente PHPMailer

### Opción 2: Instalación Manual
1. **Instalar Composer** (si no lo tienes):
   - Descargar desde: https://getcomposer.org/download/
   - Instalar siguiendo las instrucciones

2. **Instalar dependencias**:
   ```bash
   cd C:\xampp\htdocs\trabajoPreguntasYRespuestas
   composer install
   ```

3. **Verificar instalación**:
   ```bash
   php test_phpmailer.php
   ```

### Opción 3: Si Composer no funciona
1. Descargar PHPMailer desde: https://github.com/PHPMailer/PHPMailer/releases
2. Extraer en la carpeta `vendor/phpmailer/phpmailer/`
3. Verificar que existe el archivo: `vendor/phpmailer/phpmailer/src/PHPMailer.php`

## 📋 Requisitos del Sistema

- **PHP 7.4+**
- **MySQL/MariaDB**
- **Apache/Nginx**
- **Composer** (para gestión de dependencias)

## 🛠️ Configuración de Base de Datos

1. Crear una base de datos MySQL
2. Ejecutar los scripts SQL en este orden:
   ```sql
   SOURCE preguntas_database.sql;
   SOURCE 600_preguntas_parte1.sql;
   SOURCE 600_preguntas_parte2.sql;
   SOURCE 600_preguntas_parte3.sql;
   SOURCE 600_preguntas_parte4.sql;
   SOURCE 600_preguntas_parte5.sql;
   ```

## 📧 Configuración de Email

El sistema usa PHPMailer para envío de emails de verificación. 
La configuración está en `controllers/LoginController.php` línea 171.

## 🎮 Características del Juego

- **6 Categorías**: Deporte, Entretenimiento, Historia, Ciencia, Arte, Geografía
- **600 Preguntas** (100 por categoría)
- **Sistema de dificultad** basado en estadísticas de respuestas
- **3 Niveles**: Fácil, Medio, Difícil
- **Mapa interactivo** para encontrar contrincantes

## 🔧 Solución de Problemas

### Error "PHPMailer not found"
- Ejecutar `instalar_phpmailer.bat`
- O seguir las instrucciones de instalación manual arriba

### Error de base de datos
- Verificar configuración en `config/config.php`
- Asegurar que MySQL esté ejecutándose

### Errores de permisos
- Verificar permisos de escritura en `uploads/`
- Verificar configuración de Apache/PHP

---
💡 **Tip**: Si tienes problemas, ejecuta primero `instalar_phpmailer.bat` y luego `php test_phpmailer.php` para verificar que todo funciona.
