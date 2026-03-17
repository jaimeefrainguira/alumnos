CREATE DATABASE IF NOT EXISTS colegio_pagos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE colegio_pagos;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin','publico') NOT NULL DEFAULT 'publico'
);

CREATE TABLE alumnos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) DEFAULT NULL,
    direccion VARCHAR(200) DEFAULT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE cuotas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    anio INT NOT NULL,
    mes INT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    UNIQUE KEY idx_cuota_anio_mes (anio, mes)
);

CREATE TABLE abonos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alumno_id INT NOT NULL,
    mes INT NOT NULL,
    anio INT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    fecha_abono DATE NOT NULL,
    CONSTRAINT fk_abonos_alumno FOREIGN KEY (alumno_id) REFERENCES alumnos(id) ON DELETE CASCADE,
    INDEX idx_abonos_lookup (alumno_id, mes, anio)
);

INSERT INTO usuarios (usuario, password, rol)
VALUES ('admin', '$2y$12$R2D0Da3BrfU3SES1gKDFnufRVQrZe77/A/OwTgGq3YBUu4YkEoHku', 'admin');

INSERT INTO cuotas (anio, mes, valor) VALUES 
(2026, 1, 30.00),
(2026, 2, 35.00),
(2026, 3, 30.00);

INSERT INTO alumnos (codigo, nombre, telefono, direccion) VALUES
('ALU-001', 'Juan Pérez', '0999999991', 'Av. Central 123'),
('ALU-002', 'María Gómez', '0999999992', 'Calle Norte 456'),
('ALU-003', 'Carlos López', '0999999993', 'Barrio Sur 789');

INSERT INTO abonos (alumno_id, mes, anio, valor, fecha_abono) VALUES
(1, 1, 2026, 10.00, '2026-01-05'),
(1, 1, 2026, 10.00, '2026-01-10'),
(1, 1, 2026, 10.00, '2026-01-20'),
(2, 1, 2026, 15.00, '2026-01-15'),
(3, 2, 2026, 30.00, '2026-02-02');
