<?php

declare(strict_types=1);

namespace Acme;

use InvalidArgumentException;
use JsonSerializable;

/**
 * Amount in integer cents (single currency).
 */
final readonly class Money implements JsonSerializable
{
    private function __construct(public int $cents) {}

    public static function ofCents(int $cents): self
    {
        return new self($cents);
    }

    public static function zero(): self
    {
        return new self(0);
    }

    public function add(self $other): self
    {
        return new self($this->cents + $other->cents);
    }

    public function subtract(self $other): self
    {
        return new self($this->cents - $other->cents);
    }

    public function multiply(int $factor): self
    {
        return new self($this->cents * $factor);
    }

    // Rounds half up: 50% of 3295 => 1648
    public function percentage(int $percent): self
    {
        if ($percent < 0 || $percent > 100) {
            throw new InvalidArgumentException("Percentage must be between 0 and 100, got {$percent}.");
        }

        return new self((int) round($this->cents * $percent / 100, 0, PHP_ROUND_HALF_UP));
    }

    public function isLessThan(self $other): bool
    {
        return $this->cents < $other->cents;
    }

    public function equals(self $other): bool
    {
        return $this->cents === $other->cents;
    }

    public function format(): string
    {
        $sign = $this->cents < 0 ? '-' : '';

        return sprintf('%s$%d.%02d', $sign, intdiv(abs($this->cents), 100), abs($this->cents) % 100);
    }

    /**
     * @return array{cents: int, formatted: string}
     */
    public function jsonSerialize(): array
    {
        return ['cents' => $this->cents, 'formatted' => $this->format()];
    }
}
