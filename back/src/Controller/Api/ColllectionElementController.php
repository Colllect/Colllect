<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\EmptyFileException;
use App\Exception\FilesystemCannotRenameException;
use App\Exception\InvalidElementLinkException;
use App\Exception\NotSupportedElementTypeException;
use App\Service\ColllectionElementService;
use League\Flysystem\FilesystemException;
use Nelmio\ApiDocBundle\Annotation as ApiDoc;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ColllectionElementController extends AbstractController
{
    public function __construct(
        private ColllectionElementService $colllectionElementService,
    ) {
    }

    /**
     * Add an element to Colllection.
     *
     * To create an element you need to match one of these combination:
     *  - `file`: `type` parameter is ignored as type is detected with file data
     *  - `url` (+ `type`)
     *  - `content` + `type`
     *
     * In any case you can define the name of the element with `basename`
     *
     * @Route("/", name="create", methods={"POST"})
     *
     * @ApiDoc\Areas({"default"})
     *
     * @SWG\Tag(name="Colllection Elements")
     * @ApiDoc\Operation(
     *     consumes={"multipart/form-data"}
     * )
     *
     * @SWG\Parameter(
     *     name="encodedColllectionPath",
     *     in="path",
     *     description="Encoded colllection path",
     *     type="string"
     * )
     *
     * @SWG\Parameter(
     *     name="file",
     *     in="formData",
     *     description="Element file to import",
     *     type="file"
     * )
     * @SWG\Parameter(
     *     name="url",
     *     in="formData",
     *     description="Element URL to import from",
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="type",
     *     in="formData",
     *     description="Element type",
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="name",
     *     in="formData",
     *     description="Element name",
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="tags",
     *     in="formData",
     *     description="Element tag list",
     *     type="array",
     *     @SWG\Items(type="string")
     * )
     * @SWG\Parameter(
     *     name="content",
     *     in="formData",
     *     description="Element content",
     *     type="string"
     * )
     *
     * @SWG\Response(
     *     response=201,
     *     description="Returned when element was created in Colllection",
     *     @SWG\Schema(ref="#/definitions/Element")
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Returned when form is invalid"
     * )
     *
     * @throws EmptyFileException
     * @throws InvalidElementLinkException
     * @throws NotSupportedElementTypeException
     * @throws FilesystemException
     */
    public function createColllectionElement(Request $request, string $encodedColllectionPath): JsonResponse
    {
        $element = $this->colllectionElementService->create($encodedColllectionPath, $request);

        return $this->json($element, Response::HTTP_CREATED);
    }

    /**
     * List all Colllection elements.
     *
     * @Route("/", name="list", methods={"GET"})
     *
     * @ApiDoc\Areas({"default"})
     *
     * @SWG\Tag(name="Colllection Elements")
     *
     * @SWG\Parameter(
     *     name="encodedColllectionPath",
     *     in="path",
     *     description="Encoded colllection path",
     *     type="string"
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returned when colllection files are listed",
     *     @SWG\Schema(ref="#/definitions/ElementList")
     * )
     */
    public function listColllectionElements(string $encodedColllectionPath): JsonResponse
    {
        $elements = $this->colllectionElementService->list($encodedColllectionPath);

        return $this->json($elements);
    }

    /**
     * Get a Colllection element.
     *
     * @Route("/{encodedElementBasename}", name="get", methods={"GET"})
     *
     * @ApiDoc\Areas({"default"})
     *
     * @SWG\Tag(name="Colllection Elements")
     *
     * @SWG\Parameter(
     *     name="encodedColllectionPath",
     *     in="path",
     *     description="Encoded colllection path",
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="encodedElementBasename",
     *     in="path",
     *     description="Encoded element basename",
     *     type="string"
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returned when Colllection file is found",
     *     @SWG\Schema(ref="#/definitions/Element")
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Returned when Colllection file is not found"
     * )
     *
     * @throws FilesystemException
     * @throws NotSupportedElementTypeException
     */
    public function getColllectionElement(string $encodedColllectionPath, string $encodedElementBasename): JsonResponse
    {
        $element = $this->colllectionElementService->get($encodedElementBasename, $encodedColllectionPath);

        return $this->json($element);
    }

    /**
     * Update a Colllection element.
     *
     * @Route("/{encodedElementBasename}", name="update", methods={"PUT"})
     *
     * @ApiDoc\Areas({"default"})
     *
     * @SWG\Tag(name="Colllection Elements")
     * @ApiDoc\Operation(
     *     consumes={"application/x-www-form-urlencoded"}
     * )
     *
     * @SWG\Parameter(
     *     name="encodedColllectionPath",
     *     in="path",
     *     description="Encoded colllection path",
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="encodedElementBasename",
     *     in="path",
     *     description="Encoded element basename",
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="name",
     *     in="formData",
     *     description="Element name",
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="tags",
     *     in="formData",
     *     description="Element tag list",
     *     type="array",
     *     @SWG\Items(type="string")
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returned when Colllection file is updated",
     *     @SWG\Schema(ref="#/definitions/Element")
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Returned when form is invalid"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Returned when Colllection file is not found"
     * )
     *
     * @throws FilesystemCannotRenameException
     * @throws FilesystemException
     * @throws NotSupportedElementTypeException
     */
    public function updateColllectionElement(
        Request $request,
        string $encodedColllectionPath,
        string $encodedElementBasename
    ): JsonResponse {
        $element = $this->colllectionElementService->update($encodedElementBasename, $encodedColllectionPath, $request);

        return $this->json($element);
    }

    /**
     * Delete a Colllection element.
     *
     * @Route("/{encodedElementBasename}", name="delete", methods={"DELETE"})
     *
     * @ApiDoc\Areas({"default"})
     *
     * @SWG\Tag(name="Colllection Elements")
     *
     * @SWG\Parameter(
     *     name="encodedColllectionPath",
     *     in="path",
     *     description="Encoded colllection path",
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="encodedElementBasename",
     *     in="path",
     *     description="Encoded element basename",
     *     type="string"
     * )
     *
     * @SWG\Response(
     *     response=204,
     *     description="Returned when Colllection file is deleted"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Returned when Colllection file is not found"
     * )
     *
     * @throws NotSupportedElementTypeException
     */
    public function deleteColllectionElement(string $encodedColllectionPath, string $encodedElementBasename): Response
    {
        $this->colllectionElementService->delete($encodedElementBasename, $encodedColllectionPath);

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
