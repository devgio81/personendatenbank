<?php

namespace App\Database;

use App\Models\Person;
use App\Models\Adresse;
use App\Models\Bankverbindung;
use PDO;

class DatabaseRepository
{
    private PDO $db;

    public function __construct(DatabaseConnection $connection)
    {
        $this->db = $connection->getConnection();
    }

    public function savePerson(Person $person): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO personen (vorname, nachname, geburtsdatum, email, handynummer)
            VALUES (:vorname, :nachname, :geburtsdatum, :email, :handynummer)
        ");

        $geburtsdatum = $person->getGeburtsdatum() ? $person->getGeburtsdatum()->format('Y-m-d') : null;

        $stmt->bindValue(':vorname', $person->getVorname());
        $stmt->bindValue(':nachname', $person->getNachname());
        $stmt->bindValue(':geburtsdatum', $geburtsdatum);
        $stmt->bindValue(':email', $person->getEmail());
        $stmt->bindValue(':handynummer', $person->getHandynummer());
        $stmt->execute();

        $personId = (int) $this->db->lastInsertId();
        $person->setId($personId);

        // Adressen speichern
        foreach ($person->getAdressen() as $adresse) {
            $this->saveAdresse($adresse, $personId);
        }

        // Bankverbindungen speichern
        foreach ($person->getBankverbindungen() as $bankverbindung) {
            $this->saveBankverbindung($bankverbindung, $personId);
        }

        return $personId;
    }

    public function saveAdresse(Adresse $adresse, int $personId): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO adressen (person_id, strasse, hausnummer, plz, ort, land, ist_aktuell, gueltig_von, gueltig_bis)
            VALUES (:person_id, :strasse, :hausnummer, :plz, :ort, :land, :ist_aktuell, :gueltig_von, :gueltig_bis)
        ");

        $gueltigVon = $adresse->getGueltigVon() ? $adresse->getGueltigVon()->format('Y-m-d') : null;
        $gueltigBis = $adresse->getGueltigBis() ? $adresse->getGueltigBis()->format('Y-m-d') : null;

        $stmt->bindValue(':person_id', $personId);
        $stmt->bindValue(':strasse', $adresse->getStrasse());
        $stmt->bindValue(':hausnummer', $adresse->getHausnummer());
        $stmt->bindValue(':plz', $adresse->getPlz());
        $stmt->bindValue(':ort', $adresse->getOrt());
        $stmt->bindValue(':land', $adresse->getLand());
        $stmt->bindValue(':ist_aktuell', $adresse->istAktuell(), PDO::PARAM_BOOL);
        $stmt->bindValue(':gueltig_von', $gueltigVon);
        $stmt->bindValue(':gueltig_bis', $gueltigBis);
        $stmt->execute();

        $adresseId = (int) $this->db->lastInsertId();
        $adresse->setId($adresseId);
        $adresse->setPersonId($personId);

        return $adresseId;
    }

    public function saveBankverbindung(Bankverbindung $bankverbindung, int $personId): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO bankverbindungen (person_id, kontoinhaber, iban, bic, bankname, ist_aktiv)
            VALUES (:person_id, :kontoinhaber, :iban, :bic, :bankname, :ist_aktiv)
        ");

        $stmt->bindValue(':person_id', $personId);
        $stmt->bindValue(':kontoinhaber', $bankverbindung->getKontoinhaber());
        $stmt->bindValue(':iban', $bankverbindung->getIban());
        $stmt->bindValue(':bic', $bankverbindung->getBic());
        $stmt->bindValue(':bankname', $bankverbindung->getBankname());
        $stmt->bindValue(':ist_aktiv', $bankverbindung->istAktiv(), PDO::PARAM_BOOL);
        $stmt->execute();

        $bankverbindungId = (int) $this->db->lastInsertId();
        $bankverbindung->setId($bankverbindungId);
        $bankverbindung->setPersonId($personId);

        return $bankverbindungId;
    }

    public function findPersonByEmail(string $email): ?Person
    {
        $stmt = $this->db->prepare("SELECT * FROM personen WHERE email = :email LIMIT 1");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        
        $result = $stmt->fetch();
        
        if (!$result) {
            return null;
        }
        
        $geburtsdatum = $result['geburtsdatum'] ? new \DateTime($result['geburtsdatum']) : null;
        
        $person = new Person(
            $result['vorname'],
            $result['nachname'],
            $geburtsdatum,
            $result['email'],
            $result['handynummer']
        );
        
        $person->setId((int) $result['id']);
        
        // Adressen laden
        $this->loadAdressen($person);
        
        // Bankverbindungen laden
        $this->loadBankverbindungen($person);
        
        return $person;
    }

    private function loadAdressen(Person $person): void
    {
        $stmt = $this->db->prepare("SELECT * FROM adressen WHERE person_id = :person_id ORDER BY ist_aktuell DESC");
        $stmt->bindValue(':person_id', $person->getId());
        $stmt->execute();
        
        $adressen = $stmt->fetchAll();
        
        foreach ($adressen as $adresseData) {
            $gueltigVon = $adresseData['gueltig_von'] ? new \DateTime($adresseData['gueltig_von']) : null;
            $gueltigBis = $adresseData['gueltig_bis'] ? new \DateTime($adresseData['gueltig_bis']) : null;
            
            $adresse = new Adresse(
                $adresseData['strasse'],
                $adresseData['hausnummer'],
                $adresseData['plz'],
                $adresseData['ort'],
                $adresseData['land'],
                (bool) $adresseData['ist_aktuell'],
                $gueltigVon,
                $gueltigBis
            );
            
            $adresse->setId((int) $adresseData['id']);
            $adresse->setPersonId((int) $adresseData['person_id']);
            
            $person->addAdresse($adresse);
        }
    }

    private function loadBankverbindungen(Person $person): void
    {
        $stmt = $this->db->prepare("SELECT * FROM bankverbindungen WHERE person_id = :person_id");
        $stmt->bindValue(':person_id', $person->getId());
        $stmt->execute();
        
        $bankverbindungen = $stmt->fetchAll();
        
        foreach ($bankverbindungen as $bankverbindungData) {
            $bankverbindung = new Bankverbindung(
                $bankverbindungData['iban'],
                $bankverbindungData['kontoinhaber'],
                $bankverbindungData['bic'],
                $bankverbindungData['bankname'],
                (bool) $bankverbindungData['ist_aktiv']
            );
            
            $bankverbindung->setId((int) $bankverbindungData['id']);
            $bankverbindung->setPersonId((int) $bankverbindungData['person_id']);
            
            $person->addBankverbindung($bankverbindung);
        }
    }
} 