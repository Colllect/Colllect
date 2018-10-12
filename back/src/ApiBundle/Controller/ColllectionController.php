<?php

namespace ApiBundle\Controller;

use ApiBundle\Model\Colllection;
use ApiBundle\Service\ColllectionService;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Security("has_role('ROLE_USER')")
 */
class ColllectionController extends FOSRestController
{
    /**
     * List all Colllections
     *
     * @Rest\Route("/colllections")
     * @Rest\View()
     *
     * @Operation(
     *     tags={"Colllections"},
     *     summary="List all Colllections",
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when colllections are listed",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(@Model(type=Colllection::class))
     *         )
     *     )
     * )
     *
     * @return Colllection[]
     */
    public function getColllectionsAction()
    {
        /** @var ColllectionService $colllectionService */
        $colllectionService = $this->get('api.service.colllection');
        $colllections = $colllectionService->list();

        return $colllections;
    }


    /**
     * Create a Colllection
     *
     * @Rest\Route("/colllections")
     * @Rest\View(statusCode=201)
     *
     * @Operation(
     *     tags={"Colllections"},
     *     summary="Create a Colllection",
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="Name of the colllection",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Returned when Colllection was created",
     *         @SWG\Schema(@Model(type=Colllection::class))
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Returned when form is invalid"
     *     )
     * )
     *
     * @param Request $request
     * @return Colllection|FormInterface
     */
    public function postColllectionAction(Request $request)
    {
        /** @var ColllectionService $colllectionService */
        $colllectionService = $this->get('api.service.colllection');
        $response = $colllectionService->create($request);

        return $response;
    }


    /**
     * Get a Colllection
     *
     * @Rest\Route("/colllections/{encodedColllectionPath}")
     * @Rest\View()
     *
     * @Operation(
     *     tags={"Colllections"},
     *     summary="Get a Colllection",
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Parameter(
     *         name="encodedColllectionPath",
     *         in="path",
     *         description="Encoded colllection path",
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when Colllection is found",
     *         @SWG\Schema(@Model(type=Colllection::class))
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Returned when Colllection file is not found"
     *     )
     * )
     *
     * @param string $encodedColllectionPath
     * @return Colllection
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function getColllectionAction(string $encodedColllectionPath)
    {
        /** @var ColllectionService $colllectionService */
        $colllectionService = $this->get('api.service.colllection');
        $colllection = $colllectionService->get($encodedColllectionPath);

        return $colllection;
    }


    /**
     * Update a Colllection
     *
     * @Rest\Route("/colllections/{encodedColllectionPath}")
     * @Rest\View(statusCode=200)
     *
     * @Operation(
     *     tags={"Colllections"},
     *     summary="Update a Colllection",
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="Name of the colllection",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when Colllection was updated",
     *         @SWG\Schema(@Model(type=Colllection::class))
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Returned when form is invalid"
     *     )
     * )
     *
     * @param Request $request
     * @param string $encodedColllectionPath
     * @return Colllection|FormInterface
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function putColllectionAction(Request $request, string $encodedColllectionPath)
    {
        /** @var ColllectionService $colllectionService */
        $colllectionService = $this->get('api.service.colllection');
        $response = $colllectionService->update($encodedColllectionPath, $request);

        return $response;
    }


    /**
     * Delete a Colllection
     *
     * @Rest\Route("/colllections/{encodedColllectionPath}")
     * @Rest\View(statusCode=204)
     *
     * @Operation(
     *     tags={"Colllections"},
     *     summary="Delete a Colllection",
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Parameter(
     *         name="encodedColllectionPath",
     *         in="path",
     *         description="Encoded colllection path",
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="Returned when Colllection file is deleted"
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Returned when Colllection file is not found"
     *     )
     * )
     *
     * @param string $encodedColllectionPath
     */
    public function deleteColllectionAction(string $encodedColllectionPath)
    {
        /** @var ColllectionService $colllectionService */
        $colllectionService = $this->get('api.service.colllection');
        $colllectionService->delete($encodedColllectionPath);
    }
}
