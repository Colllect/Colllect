<?php

namespace AppBundle\Controller;

use ApiBundle\Service\CollectionElementService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProxyController extends Controller
{
    public function elementAction(Request $request, string $encodedCollectionPath, string $encodedElementBasename)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var CollectionElementService $collectionElementService */
        $collectionElementService = $this->get('api.service.collection_element');
        $response = $collectionElementService->getContent(
            $encodedElementBasename,
            $encodedCollectionPath,
            $request->headers
        );

        return $response;
    }
}
