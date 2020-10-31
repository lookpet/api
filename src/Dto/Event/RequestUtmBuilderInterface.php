<?php

namespace App\Dto\Event;

use App\PetDomain\VO\Utm;
use Symfony\Component\HttpFoundation\Request;

interface RequestUtmBuilderInterface
{
    public function build(Request $request): Utm;
}
