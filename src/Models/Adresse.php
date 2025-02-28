<?php

namespace App\Models;

class Adresse
{
    private ?int $id = null;
    private ?int $personId = null;
    private string $strasse;
    private string $hausnummer;
    private string $plz;
    private string $ort;
    private string $land;
    private bool $istAktuell;
    private ?\DateTime $gueltigVon = null;
    private ?\DateTime $gueltigBis = null;

    public function __construct(
        string $strasse,
        string $hausnummer,
        string $plz,
        string $ort,
        string $land = 'Deutschland',
        bool $istAktuell = false,
        ?\DateTime $gueltigVon = null,
        ?\DateTime $gueltigBis = null
    ) {
        $this->strasse = $strasse;
        $this->hausnummer = $hausnummer;
        $this->plz = $plz;
        $this->ort = $ort;
        $this->land = $land;
        $this->istAktuell = $istAktuell;
        $this->gueltigVon = $gueltigVon;
        $this->gueltigBis = $gueltigBis;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getPersonId(): ?int
    {
        return $this->personId;
    }

    public function setPersonId(int $personId): self
    {
        $this->personId = $personId;
        return $this;
    }

    public function getStrasse(): string
    {
        return $this->strasse;
    }

    public function getHausnummer(): string
    {
        return $this->hausnummer;
    }

    public function getPlz(): string
    {
        return $this->plz;
    }

    public function getOrt(): string
    {
        return $this->ort;
    }

    public function getLand(): string
    {
        return $this->land;
    }

    public function istAktuell(): bool
    {
        return $this->istAktuell;
    }

    public function setIstAktuell(bool $istAktuell): self
    {
        $this->istAktuell = $istAktuell;
        return $this;
    }

    public function getGueltigVon(): ?\DateTime
    {
        return $this->gueltigVon;
    }

    public function getGueltigBis(): ?\DateTime
    {
        return $this->gueltigBis;
    }
} 