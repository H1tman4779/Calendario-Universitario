CREATE DATABASE calendario_universitario_db;
USE calendario_universitario_db;

CREATE TABLE eventos(
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, title VARCHAR(40) NOT NULL, descripcion VARCHAR(40) NOT NULL, start DATETIME NOT NULL, end DATETIME NOT NULL
);
