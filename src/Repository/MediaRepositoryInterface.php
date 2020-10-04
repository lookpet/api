<?php

namespace App\Repository;

use App\Entity\Media;

interface MediaRepositoryInterface
{
    /**
     * @param string $mediaId
     *
     * @return Media|null
     */
    public function findById(string $mediaId): ?Media;
}
