CREATE DATABASE IF NOT EXISTS preguntas_respuestas;
USE preguntas_respuestas;

CREATE TABLE usuarios (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          nombre VARCHAR(100) NOT NULL,
                          anio_nacimiento YEAR,
                          sexo ENUM('Masculino', 'Femenino', 'Prefiero no cargarlo') DEFAULT 'Prefiero no cargarlo',
                          pais VARCHAR(100),
                          ciudad VARCHAR(100),
                          email VARCHAR(100) UNIQUE NOT NULL,
                          password VARCHAR(255) NOT NULL,
                          nombre_usuario VARCHAR(50) UNIQUE NOT NULL,
                          foto_perfil VARCHAR(255) DEFAULT NULL,
                          rol ENUM('usuario', 'admin', 'editor') DEFAULT 'usuario',
                          fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO usuarios (nombre, anio_nacimiento, sexo, pais, ciudad, email, password, nombre_usuario, rol)
VALUES (
           'Administrador General',
           1990,
           'Prefiero no cargarlo',
           'Argentina',
           'Buenos Aires',
           'admin@admin.com',
           '$2y$10$sGNoYVw8RJX7iGrwWrKnzeyGxY2k6aJbZZPyAvHEZ1uTRPgeMFmQG',
           'admin',
           'admin'
       );

INSERT INTO usuarios (nombre, anio_nacimiento, sexo, pais, ciudad, email, password, nombre_usuario, rol)
VALUES (
           'Lautaro Saez',
           2001,
           'Masculino',
           'Argentina',
           'Buenos Aires',
           'lauty01saez@gmail.com',
           '$2y$10$sGNoYVw8RJX7iGrwWrKnzeyGxY2k6aJbZZPyAvHEZ1uTRPgeMFmQG',
           'Lauty01',
           'editor'
       );


