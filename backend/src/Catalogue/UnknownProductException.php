<?php

declare(strict_types=1);

namespace Acme\Catalogue;

use DomainException;

final class UnknownProductException extends DomainException
{
    public static function forCode(string $code): self
    {
        return new self("Unknown product code '{$code}'.");
    }
}
