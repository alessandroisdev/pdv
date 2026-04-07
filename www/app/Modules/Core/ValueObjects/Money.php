<?php

namespace App\Modules\Core\ValueObjects;

use InvalidArgumentException;

class Money
{
    private int $cents;

    public function __construct(int $cents)
    {
        $this->cents = $cents;
    }

    public static function fromReais(float|string $reais): self
    {
        $cents = (int) round(((float) $reais) * 100);
        return new self($cents);
    }

    public function getCents(): int
    {
        return $this->cents;
    }

    public function getReais(): float
    {
        return $this->cents / 100;
    }

    public function add(Money $other): self
    {
        return new self($this->cents + $other->getCents());
    }

    public function subtract(Money $other): self
    {
        if ($this->cents < $other->getCents()) {
            throw new InvalidArgumentException("Resulting money cannot be negative.");
        }
        return new self($this->cents - $other->getCents());
    }

    public function __toString(): string
    {
        return 'R$ ' . number_format($this->getReais(), 2, ',', '.');
    }
}
