<?php

namespace AppBundle\Controller;

use ApiBundle\Service\ColllectionElementService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProxyController extends Controller
{
    public function elementAction(Request $request, string $encodedColllectionPath, string $encodedElementBasename)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var ColllectionElementService $colllectionElementService */
        $colllectionElementService = $this->get('api.service.colllection_element');
        $response = $colllectionElementService->getContent(
            $encodedElementBasename,
            $encodedColllectionPath,
            $request->headers
        );

        return $response;
    }
}
