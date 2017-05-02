<?php

namespace AppBundle\Controller;

use ApiBundle\Service\CollectionService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProxyController extends Controller
{
    public function inboxElementAction($encodedElementBasename)
    {
        $collectionService = $this->get('api.service.collection');
        $response = $collectionService->getElementContentResponseByEncodedElementBasename(
            $encodedElementBasename,
            CollectionService::INBOX_FOLDER
        );

        return $response;
    }
}
