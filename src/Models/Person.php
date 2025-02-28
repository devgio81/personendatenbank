<?php

namespace App\Models;

class Person
{
    private ?int $id = null;
    private string $vorname;
    private string $nachname;
    private ?\DateTime $geburtsdatum = null;
    private ?string $email = null;
    private ?string $handynummer = null;
    private array $adressen = [];
    private array $bankverbindungen = [];
    
    // Konstanten für Begrenzungen
    private const MAX_ADRESSEN = 10;
    private const MAX_BANKVERBINDUNGEN = 10;

    public function __construct(
        string $vorname,
        string $nachname,
        ?\DateTime $geburtsdatum = null,
        ?string $email = null,
        ?string $handynummer = null
    ) {
        $this->vorname = $vorname;
        $this->nachname = $nachname;
        $this->geburtsdatum = $geburtsdatum;
        $this->email = $email;
        $this->handynummer = $handynummer;
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

    public function getVorname(): string
    {
        return $this->vorname;
    }

    public function setVorname(string $vorname): self
    {
        $this->vorname = $vorname;
        return $this;
    }

    public function getNachname(): string
    {
        return $this->nachname;
    }

    public function setNachname(string $nachname): self
    {
        $this->nachname = $nachname;
        return $this;
    }

    public function getGeburtsdatum(): ?\DateTime
    {
        return $this->geburtsdatum;
    }

    public function setGeburtsdatum(?\DateTime $geburtsdatum): self
    {
        $this->geburtsdatum = $geburtsdatum;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getHandynummer(): ?string
    {
        return $this->handynummer;
    }

    public function setHandynummer(?string $handynummer): self
    {
        $this->handynummer = $handynummer;
        return $this;
    }

    /**
     * Fügt eine Adresse hinzu, wenn das Limit nicht überschritten ist
     * 
     * @param Adresse $adresse Die hinzuzufügende Adresse
     * @return self
     * @throws \RuntimeException Wenn das Limit überschritten wird
     */
    public function addAdresse(Adresse $adresse): self
    {
        if (count($this->adressen) >= self::MAX_ADRESSEN) {
            throw new \RuntimeException(sprintf(
                "Maximale Anzahl von %d Adressen für Person %s %s erreicht",
                self::MAX_ADRESSEN,
                $this->vorname,
                $this->nachname
            ));
        }
        
        $this->adressen[] = $adresse;
        return $this;
    }

    public function getAdressen(): array
    {
        return $this->adressen;
    }

    /**
     * Fügt eine Bankverbindung hinzu, wenn das Limit nicht überschritten ist
     * 
     * @param Bankverbindung $bankverbindung Die hinzuzufügende Bankverbindung
     * @return self
     * @throws \RuntimeException Wenn das Limit überschritten wird
     */
    public function addBankverbindung(Bankverbindung $bankverbindung): self
    {
        if (count($this->bankverbindungen) >= self::MAX_BANKVERBINDUNGEN) {
            throw new \RuntimeException(sprintf(
                "Maximale Anzahl von %d Bankverbindungen für Person %s %s erreicht",
                self::MAX_BANKVERBINDUNGEN,
                $this->vorname,
                $this->nachname
            ));
        }
        
        $this->bankverbindungen[] = $bankverbindung;
        return $this;
    }

    public function getBankverbindungen(): array
    {
        return $this->bankverbindungen;
    }

    public function getAktuelleAdresse(): ?Adresse
    {
        foreach ($this->adressen as $adresse) {
            if ($adresse->istAktuell()) {
                return $adresse;
            }
        }
        
        return !empty($this->adressen) ? $this->adressen[0] : null;
    }
} 