CREATE DATABASE IF NOT EXISTS personendatenbank CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE personendatenbank;

CREATE TABLE IF NOT EXISTS personen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vorname VARCHAR(100) NOT NULL,
    nachname VARCHAR(100) NOT NULL,
    geburtsdatum DATE,
    email VARCHAR(255),
    handynummer VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS adressen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    person_id INT NOT NULL,
    strasse VARCHAR(255) NOT NULL,
    hausnummer VARCHAR(20) NOT NULL,
    plz VARCHAR(10) NOT NULL,
    ort VARCHAR(100) NOT NULL,
    land VARCHAR(100) DEFAULT 'Deutschland',
    ist_aktuell BOOLEAN DEFAULT FALSE,
    gueltig_von DATE,
    gueltig_bis DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (person_id) REFERENCES personen(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS bankverbindungen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    person_id INT NOT NULL,
    kontoinhaber VARCHAR(255),
    iban VARCHAR(50) NOT NULL,
    bic VARCHAR(20),
    bankname VARCHAR(255),
    ist_aktiv BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (person_id) REFERENCES personen(id) ON DELETE CASCADE
); 