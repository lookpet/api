<?php

namespace App\Repository;

use App\Entity\User;
use App\PetDomain\VO\EventContext;
use App\PetDomain\VO\EventType;
use App\PetDomain\VO\Utm;

interface UserEventRepositoryInterface
{
    public function log(EventType $type, User $user, Utm $utm, ?EventContext $context = null): void;
}
