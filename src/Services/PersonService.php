<?php

namespace App\Services;

use App\Database\DatabaseRepository;
use App\Models\Person;

class PersonService
{
    private DatabaseRepository $repository;

    public function __construct(DatabaseRepository $repository)
    {
        $this->repository = $repository;
    }

    public function savePerson(Person $person): int
    {
        // Prüfen, ob die Person bereits existiert (anhand der E-Mail)
        if ($person->getEmail()) {
            $existingPerson = $this->repository->findPersonByEmail($person->getEmail());
            if ($existingPerson) {
                throw new \Exception("Eine Person mit dieser E-Mail existiert bereits: " . $person->getEmail());
            }
        }

        // Validierung
        $this->validatePerson($person);

        // Person speichern
        return $this->repository->savePerson($person);
    }

    private function validatePerson(Person $person): void
    {
        if (empty($person->getVorname())) {
            throw new \Exception("Vorname darf nicht leer sein");
        }

        if (empty($person->getNachname())) {
            throw new \Exception("Nachname darf nicht leer sein");
        }

        if ($person->getEmail() && !filter_var($person->getEmail(), FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Ungültige E-Mail-Adresse: " . $person->getEmail());
        }
    }
} 