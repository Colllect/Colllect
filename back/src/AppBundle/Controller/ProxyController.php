<?php

namespace AppBundle\Controller;

use ApiBundle\Service\CollectionElementService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProxyController extends Controller
{
    public function elementAction(string $encodedCollectionPath, string $encodedElementBasename)
    {
        /** @var CollectionElementService $collectionElementService */
        $collectionElementService = $this->get('api.service.collection_element');
        $response = $collectionElementService->getContent(
            $encodedElementBasename,
            $encodedCollectionPath
        );

        return $response;
    }
}
