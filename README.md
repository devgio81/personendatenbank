# Personendatenbank-Importer

Eine objektorientierte PHP-Anwendung zum Importieren von Personendaten aus strukturierten Textdateien in eine MySQL-Datenbank.

## Funktionsumfang

Die Anwendung ermöglicht:
- Import von Personendaten aus strukturierten Textdateien
- Speicherung in einer relationalen Datenbank
- Verwaltung von Personen mit mehreren Adressen und Bankverbindungen
- Validierung der Daten vor dem Import

## Technische Details

- Entwickelt mit PHP 8.3
- MySQL-Datenbank
- Objektorientierte Programmierung
- Typsicherheit durch strikte Typdefinitionen

## Anforderungen

- PHP 8.3 oder höher
- MySQL 5.7 oder höher
- Composer für die Abhängigkeitsverwaltung

## Schritt-für-Schritt Installation

### 1. Repository klonen

```bash
git clone https://github.com/devgio81/personendatenbank.git
cd personendatenbank
```

### 2. Abhängigkeiten installieren

```bash
composer install
```

### 3. Datenbank einrichten

Stellen Sie sicher, dass MySQL läuft und erstellen Sie die Datenbank:

```bash
mysql -u root -p < database/schema.sql
```

### 4. Konfiguration anpassen

Öffnen Sie die Datei `config/database.php` und passen Sie die Datenbankverbindungsdaten an:

```php
return [
    'host' => 'localhost',      // Ihr Datenbank-Host
    'database' => 'personendatenbank',  // Name der Datenbank
    'username' => 'root',       // Ihr Datenbank-Benutzername
    'password' => '',           // Ihr Datenbank-Passwort
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];
```

### 5. Beispieldaten vorbereiten

Erstellen Sie eine Textdatei im Format wie unter "Format der Importdatei" beschrieben oder verwenden Sie die mitgelieferte Beispieldatei:

```bash
mkdir -p import
cp beispieldaten.txt import/
```

### 6. Import ausführen

Führen Sie den Import mit folgendem Befehl aus:

```bash
php import.php import/beispieldaten.txt
```

## Verwendung

### Import von Personendaten

```bash
php import.php pfad/zur/importdatei.txt
```

### Format der Importdatei

Die Importdatei muss in einem bestimmten Format vorliegen:

```
PERSON:
Vorname: Max
Nachname: Mustermann
Geburtsdatum: 15.05.1980
Email: max.mustermann@example.com
Handynummer: +49 123 4567890

ADRESSE:
Strasse: Musterstraße
Hausnummer: 123
PLZ: 12345
Ort: Musterstadt
Land: Deutschland
ist_aktuell: true
gueltig_von: 01.01.2020

BANKVERBINDUNG:
IBAN: DE89370400440532013000
BIC: COBADEFFXXX
Kontoinhaber: Max Mustermann
Bankname: Musterbank
```

- Jeder Abschnitt beginnt mit `PERSON:`, `ADRESSE:` oder `BANKVERBINDUNG:`
- Jede Zeile enthält ein Schlüssel-Wert-Paar im Format `Schlüssel: Wert`
- Eine Person kann mehrere Adressen und Bankverbindungen haben
- Leere Zeilen trennen die Abschnitte

## Datenmodell

### Person
- Vorname (Pflichtfeld)
- Nachname (Pflichtfeld)
- Geburtsdatum (optional, Format: DD.MM.YYYY)
- Email-Adresse (optional)
- Handynummer (optional)

### Adresse
- Straße (Pflichtfeld)
- Hausnummer (Pflichtfeld)
- PLZ (Pflichtfeld)
- Ort (Pflichtfeld)
- Land (optional, Standard: Deutschland)
- Ist aktuelle Adresse (optional, Standard: false)
- Gültig von (optional, Format: DD.MM.YYYY)
- Gültig bis (optional, Format: DD.MM.YYYY)

### Bankverbindung
- IBAN (Pflichtfeld)
- Kontoinhaber (optional)
- BIC (optional)
- Bankname (optional)
- Ist aktiv (optional, Standard: true)

## Fehlerbehebung

### Häufige Probleme

1. **Datenbankverbindungsfehler**
   - Überprüfen Sie die Konfiguration in `config/database.php`
   - Stellen Sie sicher, dass MySQL läuft
   - Überprüfen Sie Benutzername und Passwort

2. **Importfehler**
   - Überprüfen Sie das Format der Importdatei
   - Stellen Sie sicher, dass Pflichtfelder ausgefüllt sind
   - Überprüfen Sie die Datumsformate (DD.MM.YYYY)

3. **PHP-Fehler**
   - Stellen Sie sicher, dass PHP 8.3 oder höher installiert ist
   - Führen Sie `composer install` aus, um Abhängigkeiten zu installieren

## Projektstruktur

- `config/`: Konfigurationsdateien
- `database/`: Datenbankschema
- `import/`: Beispieldateien für den Import
- `src/`: Quellcode
  - `Database/`: Datenbankverbindung und Repository
  - `Models/`: Datenmodelle (Person, Adresse, Bankverbindung)
  - `Services/`: Geschäftslogik
  - `Utils/`: Hilfsfunktionen (FileParser)
- `import.php`: Hauptskript für den Import