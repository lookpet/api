<?php

namespace Tests\Unit\PetDomain\VO;

use App\PetDomain\VO\Utm;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @covers \App\PetDomain\VO\Utm
 */
class UtmTest extends TestCase
{
    private const SOURCE = 'source';
    private const MEDIUM = 'medium';
    private const CAMPAIGN = 'campaign';

    public function testGetters(): void
    {
        $utm = new Utm();
        self::assertNull($utm->getSource());
        self::assertNull($utm->getMedium());
        self::assertNull($utm->getCampaign());

        $utm = new Utm(
            self::SOURCE,
            self::MEDIUM,
            self::CAMPAIGN
        );

        self::assertSame(self::SOURCE, $utm->getSource());
        self::assertSame(self::MEDIUM, $utm->getMedium());
        self::assertSame(self::CAMPAIGN, $utm->getCampaign());
    }
}
