<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\NotSupportedElementTypeException;
use App\Service\ColllectionElementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/proxy/{encodedColllectionPath}/{encodedElementBasename}", name="login", methods={"GET"})
     *
     * @throws NotSupportedElementTypeException
     */
    public function element(Request $request, string $encodedColllectionPath, string $encodedElementBasename)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $response = $this->colllectionElementService->getContent(
            $encodedElementBasename,
            $encodedColllectionPath,
            $request->headers
        );

        return $response;
    }
}
