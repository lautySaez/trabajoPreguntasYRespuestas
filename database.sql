CREATE DATABASE IF NOT EXISTS preguntas_respuestas;

USE preguntas_respuestas;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    sexo ENUM('Masculino', 'Femenino', 'Prefiero no cargarlo') DEFAULT 'Prefiero no cargarlo',
    pais VARCHAR(100),
    ciudad VARCHAR(100),
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nombre_usuario VARCHAR(50) UNIQUE NOT NULL,
    foto_perfil VARCHAR(255) DEFAULT NULL,
    rol ENUM('Jugador', 'Administrador', 'Editor') DEFAULT 'Jugador',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado_registro ENUM('Activo', 'Inactivo') DEFAULT 'Inactivo',
    token_verificacion INT DEFAULT NULL
);

INSERT INTO
    usuarios (
        nombre,
        fecha_nacimiento,
        sexo,
        pais,
        ciudad,
        email,
        password,
        nombre_usuario,
        rol,
        estado_registro,
        token_verificacion,
        foto_perfil
    )
VALUES
    (
        'Administrador General',
        '1990-10-10',
        'Prefiero no cargarlo',
        'Argentina',
        'Buenos Aires',
        'admin@admin.com',
        '$2y$10$ZI6Bft2VF/7ibKP5Hc1n.OMpBpwsO6J7sVC3tsN0UFUoYdPqL0ZqK',
        'admin',
        'Administrador',
        'Activo',
        NULL,
        NULL
    )