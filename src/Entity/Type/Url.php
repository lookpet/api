<?php

declare(strict_types=1);

namespace App\Entity\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class Url extends Type
{
    private const URL = 'url';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): \App\PetDomain\VO\url
    {
        return new \App\PetDomain\VO\url($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value->__toString();
    }

    public function getName()
    {
        return self::URL;
    }
}
