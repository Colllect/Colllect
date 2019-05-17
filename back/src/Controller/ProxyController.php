<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\NotSupportedElementTypeException;
use App\Service\ColllectionElementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProxyController extends AbstractController
{
    /**
     * @var ColllectionElementService
     */
    private $colllectionElementService;

    public function __construct(ColllectionElementService $colllectionElementService)
    {
        $this->colllectionElementService = $colllectionElementService;
    }

    /**
     * @Route("/proxy/{encodedColllectionPath}/{encodedElementBasename}", name="element", methods={"GET"})
     *
     * @throws NotSupportedElementTypeException
     */
    public function element(Request $request, string $encodedColllectionPath, string $encodedElementBasename)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Avoid fetch storage if request is canceled
        if (connection_aborted()) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        $response = $this->colllectionElementService->getContent(
            $encodedElementBasename,
            $encodedColllectionPath,
            $request->headers
        );

        return $response;
    }
}
