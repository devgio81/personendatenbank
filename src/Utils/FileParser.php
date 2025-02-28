<?php

namespace App\Utils;

use App\Models\Person;
use App\Models\Adresse;
use App\Models\Bankverbindung;

class FileParser
{
    /**
     * Parst eine strukturierte Textdatei und erstellt Person-Objekte
     */
    public function parseFile(string $filePath): array
    {
        $content = file_get_contents($filePath);
        $lines = explode("\n", $content);
        
        $personen = [];
        $currentPerson = null;
        $currentSection = null;
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Leere Zeilen überspringen
            if (empty($line)) {
                continue;
            }
            
            // Neue Person beginnt
            if (preg_match('/^PERSON:(.*)$/', $line, $matches)) {
                // Vorherige Person speichern, wenn vorhanden
                if ($currentPerson !== null) {
                    $personen[] = $currentPerson;
                }
                
                $currentPerson = new Person('', '');
                $currentSection = 'PERSON';
                continue;
            }
            
            // Adresse beginnt
            if (preg_match('/^ADRESSE:(.*)$/', $line, $matches)) {
                $currentSection = 'ADRESSE';
                continue;
            }
            
            // Bankverbindung beginnt
            if (preg_match('/^BANKVERBINDUNG:(.*)$/', $line, $matches)) {
                $currentSection = 'BANKVERBINDUNG';
                continue;
            }
            
            // Daten verarbeiten
            if ($currentPerson !== null && $currentSection !== null) {
                $this->processLine($line, $currentPerson, $currentSection);
            }
        }
        
        // Letzte Person hinzufügen
        if ($currentPerson !== null) {
            $personen[] = $currentPerson;
        }
        
        return $personen;
    }
    
    private function processLine(string $line, Person $person, string $section): void
    {
        // Schlüssel-Wert-Paare verarbeiten (Format: Schlüssel: Wert)
        if (preg_match('/^([^:]+):(.*)$/', $line, $matches)) {
            $key = trim($matches[1]);
            $value = trim($matches[2]);
            
            switch ($section) {
                case 'PERSON':
                    $this->processPersonData($person, $key, $value);
                    break;
                    
                case 'ADRESSE':
                    $this->processAdresseData($person, $key, $value);
                    break;
                    
                case 'BANKVERBINDUNG':
                    $this->processBankverbindungData($person, $key, $value);
                    break;
            }
        }
    }
    
    private function processPersonData(Person $person, string $key, string $value): void
    {
        switch (strtolower($key)) {
            case 'vorname':
                $person->setVorname($value);
                break;
                
            case 'nachname':
                $person->setNachname($value);
                break;
                
            case 'geburtsdatum':
                if (!empty($value)) {
                    $date = \DateTime::createFromFormat('d.m.Y', $value);
                    if ($date) {
                        $person->setGeburtsdatum($date);
                    }
                }
                break;
                
            case 'email':
                $person->setEmail($value);
                break;
                
            case 'handynummer':
                $person->setHandynummer($value);
                break;
        }
    }
    
    private function processAdresseData(Person $person, string $key, string $value): void
    {
        static $adresseData = [];
        
        // Wenn ein neuer Schlüssel "strasse" kommt, beginnt eine neue Adresse
        if (strtolower($key) === 'strasse' && !empty($adresseData)) {
            $this->createAndAddAdresse($person, $adresseData);
            $adresseData = [];
        }
        
        $adresseData[strtolower($key)] = $value;
        
        // Wenn wir alle notwendigen Daten haben, erstellen wir die Adresse
        if (isset($adresseData['strasse']) && isset($adresseData['hausnummer']) && 
            isset($adresseData['plz']) && isset($adresseData['ort'])) {
            $this->createAndAddAdresse($person, $adresseData);
            $adresseData = [];
        }
    }
    
    private function createAndAddAdresse(Person $person, array $data): void
    {
        if (!isset($data['strasse']) || !isset($data['hausnummer']) || 
            !isset($data['plz']) || !isset($data['ort'])) {
            return;
        }
        
        $istAktuell = isset($data['ist_aktuell']) ? filter_var($data['ist_aktuell'], FILTER_VALIDATE_BOOLEAN) : false;
        $land = $data['land'] ?? 'Deutschland';
        
        $gueltigVon = null;
        if (isset($data['gueltig_von'])) {
            $gueltigVon = \DateTime::createFromFormat('d.m.Y', $data['gueltig_von']);
        }
        
        $gueltigBis = null;
        if (isset($data['gueltig_bis'])) {
            $gueltigBis = \DateTime::createFromFormat('d.m.Y', $data['gueltig_bis']);
        }
        
        $adresse = new Adresse(
            $data['strasse'],
            $data['hausnummer'],
            $data['plz'],
            $data['ort'],
            $land,
            $istAktuell,
            $gueltigVon,
            $gueltigBis
        );
        
        try {
            $person->addAdresse($adresse);
        } catch (\RuntimeException $e) {
            // Maximale Anzahl von Adressen erreicht, Warnung loggen
            error_log($e->getMessage());
        }
    }
    
    private function processBankverbindungData(Person $person, string $key, string $value): void
    {
        static $bankData = [];
        
        // Wenn ein neuer Schlüssel "iban" kommt, beginnt eine neue Bankverbindung
        if (strtolower($key) === 'iban' && !empty($bankData)) {
            $this->createAndAddBankverbindung($person, $bankData);
            $bankData = [];
        }
        
        $bankData[strtolower($key)] = $value;
        
        // Wenn wir die IBAN haben, können wir die Bankverbindung erstellen
        if (isset($bankData['iban'])) {
            $this->createAndAddBankverbindung($person, $bankData);
            $bankData = [];
        }
    }
    
    private function createAndAddBankverbindung(Person $person, array $data): void
    {
        if (!isset($data['iban'])) {
            return;
        }
        
        $istAktiv = isset($data['ist_aktiv']) ? filter_var($data['ist_aktiv'], FILTER_VALIDATE_BOOLEAN) : true;
        
        $bankverbindung = new Bankverbindung(
            $data['iban'],
            $data['kontoinhaber'] ?? null,
            $data['bic'] ?? null,
            $data['bankname'] ?? null,
            $istAktiv
        );
        
        try {
            $person->addBankverbindung($bankverbindung);
        } catch (\RuntimeException $e) {
            // Maximale Anzahl von Bankverbindungen erreicht, Warnung loggen
            error_log($e->getMessage());
        }
    }
} 