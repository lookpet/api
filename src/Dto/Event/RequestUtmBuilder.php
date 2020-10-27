<?php

namespace App\Dto\Event;

use App\PetDomain\VO\Utm;
use Symfony\Component\HttpFoundation\Request;

final class RequestUtmBuilder implements RequestUtmBuilderInterface
{
    public function build(Request $request): Utm
    {
        $utmSource = $request->get('utm_source');
        $utmMedium = $request->get('utm_medium');
        $utmCampaign = $request->get('utm_campaign');

        if (empty($utmSource)) {
            $utmSource = $request->request->get('utm_source');
        }

        if (empty($utmMedium)) {
            $utmMedium = $request->request->get('utm_medium');
        }

        if (empty($utmCampaign)) {
            $utmCampaign = $request->request->get('utm_campaign');
        }

        return new Utm(
            $utmSource,
            $utmMedium,
            $utmCampaign
        );
    }
}
