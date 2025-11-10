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
=======
# preguntasYRespuestas



## Getting started

To make it easy for you to get started with GitLab, here's a list of recommended next steps.

Already a pro? Just edit this README.md and make it your own. Want to make it easy? [Use the template at the bottom](#editing-this-readme)!

## Add your files

- [ ] [Create](https://docs.gitlab.com/ee/user/project/repository/web_editor.html#create-a-file) or [upload](https://docs.gitlab.com/ee/user/project/repository/web_editor.html#upload-a-file) files
- [ ] [Add files using the command line](https://docs.gitlab.com/topics/git/add_files/#add-files-to-a-git-repository) or push an existing Git repository with the following command:

```
cd existing_repo
git remote add origin https://gitlab.com/lautySaez/preguntasyrespuestas.git
git branch -M main
git push -uf origin main
```

## Integrate with your tools

- [ ] [Set up project integrations](https://gitlab.com/lautySaez/preguntasyrespuestas/-/settings/integrations)

## Collaborate with your team

- [ ] [Invite team members and collaborators](https://docs.gitlab.com/ee/user/project/members/)
- [ ] [Create a new merge request](https://docs.gitlab.com/ee/user/project/merge_requests/creating_merge_requests.html)
- [ ] [Automatically close issues from merge requests](https://docs.gitlab.com/ee/user/project/issues/managing_issues.html#closing-issues-automatically)
- [ ] [Enable merge request approvals](https://docs.gitlab.com/ee/user/project/merge_requests/approvals/)
- [ ] [Set auto-merge](https://docs.gitlab.com/user/project/merge_requests/auto_merge/)

## Test and Deploy

Use the built-in continuous integration in GitLab.

- [ ] [Get started with GitLab CI/CD](https://docs.gitlab.com/ee/ci/quick_start/)
- [ ] [Analyze your code for known vulnerabilities with Static Application Security Testing (SAST)](https://docs.gitlab.com/ee/user/application_security/sast/)
- [ ] [Deploy to Kubernetes, Amazon EC2, or Amazon ECS using Auto Deploy](https://docs.gitlab.com/ee/topics/autodevops/requirements.html)
- [ ] [Use pull-based deployments for improved Kubernetes management](https://docs.gitlab.com/ee/user/clusters/agent/)
- [ ] [Set up protected environments](https://docs.gitlab.com/ee/ci/environments/protected_environments.html)

***

# Editing this README

When you're ready to make this README your own, just edit this file and use the handy template below (or feel free to structure it however you want - this is just a starting point!). Thanks to [makeareadme.com](https://www.makeareadme.com/) for this template.

## Suggestions for a good README

Every project is different, so consider which of these sections apply to yours. The sections used in the template are suggestions for most open source projects. Also keep in mind that while a README can be too long and detailed, too long is better than too short. If you think your README is too long, consider utilizing another form of documentation rather than cutting out information.

## Name
Choose a self-explaining name for your project.

## Description
Let people know what your project can do specifically. Provide context and add a link to any reference visitors might be unfamiliar with. A list of Features or a Background subsection can also be added here. If there are alternatives to your project, this is a good place to list differentiating factors.

## Badges
On some READMEs, you may see small images that convey metadata, such as whether or not all the tests are passing for the project. You can use Shields to add some to your README. Many services also have instructions for adding a badge.

## Visuals
Depending on what you are making, it can be a good idea to include screenshots or even a video (you'll frequently see GIFs rather than actual videos). Tools like ttygif can help, but check out Asciinema for a more sophisticated method.

## Installation
Within a particular ecosystem, there may be a common way of installing things, such as using Yarn, NuGet, or Homebrew. However, consider the possibility that whoever is reading your README is a novice and would like more guidance. Listing specific steps helps remove ambiguity and gets people to using your project as quickly as possible. If it only runs in a specific context like a particular programming language version or operating system or has dependencies that have to be installed manually, also add a Requirements subsection.

## Usage
Use examples liberally, and show the expected output if you can. It's helpful to have inline the smallest example of usage that you can demonstrate, while providing links to more sophisticated examples if they are too long to reasonably include in the README.

## Support
Tell people where they can go to for help. It can be any combination of an issue tracker, a chat room, an email address, etc.

## Roadmap
If you have ideas for releases in the future, it is a good idea to list them in the README.

## Contributing
State if you are open to contributions and what your requirements are for accepting them.

For people who want to make changes to your project, it's helpful to have some documentation on how to get started. Perhaps there is a script that they should run or some environment variables that they need to set. Make these steps explicit. These instructions could also be useful to your future self.

You can also document commands to lint the code or run tests. These steps help to ensure high code quality and reduce the likelihood that the changes inadvertently break something. Having instructions for running tests is especially helpful if it requires external setup, such as starting a Selenium server for testing in a browser.

## Authors and acknowledgment
Show your appreciation to those who have contributed to the project.

## License
For open source projects, say how it is licensed.
