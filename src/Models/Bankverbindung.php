<?php

namespace App\Models;

class Bankverbindung
{
    private ?int $id = null;
    private ?int $personId = null;
    private ?string $kontoinhaber;
    private string $iban;
    private ?string $bic;
    private ?string $bankname;
    private bool $istAktiv;

    public function __construct(
        string $iban,
        ?string $kontoinhaber = null,
        ?string $bic = null,
        ?string $bankname = null,
        bool $istAktiv = true
    ) {
        $this->iban = $iban;
        $this->kontoinhaber = $kontoinhaber;
        $this->bic = $bic;
        $this->bankname = $bankname;
        $this->istAktiv = $istAktiv;
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

    public function getKontoinhaber(): ?string
    {
        return $this->kontoinhaber;
    }

    public function getIban(): string
    {
        return $this->iban;
    }

    public function getBic(): ?string
    {
        return $this->bic;
    }

    public function getBankname(): ?string
    {
        return $this->bankname;
    }

    public function istAktiv(): bool
    {
        return $this->istAktiv;
    }
} 