<?php

namespace Tests\Unit\Dto\Event;

use App\Dto\Event\RequestUtmBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @group unit
 * @covers \App\Dto\Event\RequestUtmBuilder
 */
class RequestUtmBuilderTest extends TestCase
{
    private const UTM_SOURCE = 'friend';
    private const UTM_MEDIUM = 'organic';
    private const UTM_CAMPAIGN = 'word of mouth';

    private RequestUtmBuilder $requestUtmBuilder;

    public function testItBuildsDtoByGetParams(): void
    {
        $request = new Request(
            [
                'utm_source' => self::UTM_SOURCE,
                'utm_medium' => self::UTM_MEDIUM,
                'utm_campaign' => self::UTM_CAMPAIGN,
            ]
        );

        $utm = $this->requestUtmBuilder->build($request);

        self::assertSame(self::UTM_SOURCE, $utm->getSource());
        self::assertSame(self::UTM_MEDIUM, $utm->getMedium());
        self::assertSame(self::UTM_CAMPAIGN, $utm->getCampaign());
    }

    public function testItBuildsDtoByPostParams(): void
    {
        $request = new Request(
            [],
            [
                'utm_source' => self::UTM_SOURCE,
                'utm_medium' => self::UTM_MEDIUM,
                'utm_campaign' => self::UTM_CAMPAIGN,
            ]
        );

        $utm = $this->requestUtmBuilder->build($request);

        self::assertSame(self::UTM_SOURCE, $utm->getSource());
        self::assertSame(self::UTM_MEDIUM, $utm->getMedium());
        self::assertSame(self::UTM_CAMPAIGN, $utm->getCampaign());
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->requestUtmBuilder = new RequestUtmBuilder();
    }
}
