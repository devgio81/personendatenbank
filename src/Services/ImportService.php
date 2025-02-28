<?php

namespace App\Services;

use App\Utils\FileParser;
use App\Services\PersonService;

class ImportService
{
    private FileParser $fileParser;
    private PersonService $personService;

    public function __construct(FileParser $fileParser, PersonService $personService)
    {
        $this->fileParser = $fileParser;
        $this->personService = $personService;
    }

    public function importFile(string $filePath): array
    {
        $result = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        if (!file_exists($filePath)) {
            $result['errors'][] = "Datei nicht gefunden: $filePath";
            return $result;
        }

        try {
            $personen = $this->fileParser->parseFile($filePath);
            
            foreach ($personen as $person) {
                try {
                    $this->personService->savePerson($person);
                    $result['success']++;
                } catch (\Exception $e) {
                    $result['failed']++;
                    $result['errors'][] = "Fehler beim Speichern von {$person->getVorname()} {$person->getNachname()}: " . $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            $result['errors'][] = "Fehler beim Parsen der Datei: " . $e->getMessage();
        }

        return $result;
    }
} 