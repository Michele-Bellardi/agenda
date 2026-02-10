CREATE DATABASE Agenda;
USE Agenda;

CREATE TABLE utenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50),
    cognome VARCHAR(50),
    email VARCHAR(100),
    telefono VARCHAR(20),
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255)
);

CREATE TABLE promemoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_utente INT,
    descrizione TEXT,
    data DATE,
    ora TIME,
    durata INT,
    ricorrenza ENUM('no','settimanale','mensile','annuale'),
    FOREIGN KEY (id_utente) REFERENCES utenti(id)
);

CREATE TABLE appuntamenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descrizione TEXT,
    data DATE,
    ora TIME,
    durata INT
);

CREATE TABLE appuntamento_utenti (
    id_app INT,
    id_utente INT,
    FOREIGN KEY (id_app) REFERENCES appuntamenti(id),
    FOREIGN KEY (id_utente) REFERENCES utenti(id)
);
