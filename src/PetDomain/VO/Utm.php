<?php

namespace App\PetDomain\VO;

class Utm
{
    private ?string $source;
    private ?string $medium;
    private ?string $campaign;

    public function __construct(?string $source = null, ?string $medium = null, ?string $campaign = null)
    {
        $this->source = $source;
        $this->medium = $medium;
        $this->campaign = $campaign;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getMedium(): ?string
    {
        return $this->medium;
    }

    public function getCampaign(): ?string
    {
        return $this->campaign;
    }
}
