<?php

namespace AppBundle\Controller;

use ApiBundle\Service\CollectionService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProxyController extends Controller
{
    public function elementAction(string $encodedCollectionPath, string $encodedElementBasename)
    {
        $collectionService = $this->get('api.service.collection');
        $response = $collectionService->getElementContentResponseByEncodedElementBasename(
            $encodedElementBasename,
            $encodedCollectionPath
        );

        return $response;
    }
}
