<?php

namespace App\Modules\Core\ValueObjects;

use InvalidArgumentException;

class DocumentNumber
{
    private string $document;
    private string $type; // CPF or CNPJ

    public function __construct(string $document)
    {
        $clean = preg_replace('/[^\d]/', '', $document);

        if (strlen($clean) === 11) {
            $this->type = 'CPF';
        } elseif (strlen($clean) === 14) {
            $this->type = 'CNPJ';
        } else {
            throw new InvalidArgumentException("Invalid document number. Must be 11 (CPF) or 14 (CNPJ) digits.");
        }

        // Ideally, we'd add actual Modulo 11 check algorithm here for validity.

        $this->document = $clean;
    }

    public function getDocument(): string
    {
        return $this->document;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function format(): string
    {
        if ($this->type === 'CPF') {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->document);
        }
        
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $this->document);
    }

    public function __toString(): string
    {
        return $this->document;
    }
}
