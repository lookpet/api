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

        if (empty($utmSource) && $request->request->has('utm_source')) {
            $utmSource = $request->request->get('utm_source');
        }

        if (empty($utmMedium) && $request->request->has('utm_medium')) {
            $utmMedium = $request->request->get('utm_medium');
        }

        if (empty($utmCampaign) && $request->request->has('utm_campaign')) {
            $utmCampaign = $request->request->get('utm_campaign');
        }

        if (empty($utmSource) && $request->headers->has('utm_source')) {
            $utmSource = $request->headers->get('utm_source');
        }

        if (empty($utmMedium) && $request->headers->has('utm_medium')) {
            $utmMedium = $request->headers->get('utm_medium');
        }

        if (empty($utmCampaign) && $request->headers->has('utm_campaign')) {
            $utmCampaign = $request->headers->get('utm_campaign');
        }

        return new Utm(
            $utmSource,
            $utmMedium,
            $utmCampaign
        );
    }
}
