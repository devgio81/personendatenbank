<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Database\DatabaseConnection;
use App\Database\DatabaseRepository;
use App\Services\ImportService;
use App\Services\PersonService;
use App\Utils\FileParser;

// Konfiguration laden
$config = require_once __DIR__ . '/config/database.php';

// Dependency Injection Container (manuell)
$dbConnection = new DatabaseConnection($config);
$repository = new DatabaseRepository($dbConnection);
$personService = new PersonService($repository);
$fileParser = new FileParser();
$importService = new ImportService($fileParser, $personService);

// Kommandozeilenargumente verarbeiten
$filePath = $argv[1] ?? null;

if (!$filePath) {
    echo "Bitte geben Sie den Pfad zur Importdatei an.\n";
    echo "Beispiel: php import.php import/beispieldaten.txt\n";
    exit(1);
}

// Import durchführen
$result = $importService->importFile($filePath);

// Ergebnis ausgeben
echo "Import abgeschlossen.\n";
echo "Erfolgreich importiert: {$result['success']}\n";
echo "Fehlgeschlagen: {$result['failed']}\n";

if (!empty($result['errors'])) {
    echo "\nFehler:\n";
    foreach ($result['errors'] as $error) {
        echo "- $error\n";
    }
} 