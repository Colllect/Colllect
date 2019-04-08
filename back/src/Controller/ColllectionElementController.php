<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\FilesystemCannotRenameException;
use App\Exception\FilesystemCannotWriteException;
use App\Exception\NotSupportedElementTypeException;
use App\Model\Element;
use App\Service\ColllectionElementService;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use Nelmio\ApiDocBundle\Annotation as ApiDoc;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ColllectionElementController extends AbstractController
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
     *     @SWG\Schema(ref=@ApiDoc\Model(type=Element::class))
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Returned when form is invalid"
     * )
     *
     * @param Request $request
     * @param string  $encodedColllectionPath
     *
     * @return JsonResponse
     *
     * @throws FilesystemCannotWriteException
     * @throws NotSupportedElementTypeException
     * @throws FileExistsException
     * @throws FileNotFoundException
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
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(
     *             property="itemListElement",
     *             type="array",
     *             @SWG\Items(ref=@ApiDoc\Model(type=Element::class))
     *         )
     *     )
     * )
     *
     * @param string $encodedColllectionPath
     *
     * @return JsonResponse
     */
    public function listColllectionElements(string $encodedColllectionPath): JsonResponse
    {
        $elements = $this->colllectionElementService->list($encodedColllectionPath);

        return $this->json(
            [
                'itemListElement' => $elements,
            ]
        );
    }

    /**
     * Get a Colllection element.
     *
     * @Route("/{encodedElementBasename}", name="get", methods={"GET"})
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
     *     @SWG\Schema(ref=@ApiDoc\Model(type=Element::class))
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Returned when Colllection file is not found"
     * )
     *
     * @param string $encodedColllectionPath
     * @param string $encodedElementBasename
     *
     * @return JsonResponse
     *
     * @throws NotSupportedElementTypeException
     * @throws FileNotFoundException
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
     *     @SWG\Schema(ref=@ApiDoc\Model(type=Element::class))
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
     * @param Request $request
     * @param string  $encodedColllectionPath
     * @param string  $encodedElementBasename
     *
     * @return JsonResponse
     *
     * @throws FilesystemCannotRenameException
     * @throws FilesystemCannotWriteException
     * @throws NotSupportedElementTypeException
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    public function updateColllectionElement(Request $request, string $encodedColllectionPath, string $encodedElementBasename): JsonResponse
    {
        $element = $this->colllectionElementService->update($encodedElementBasename, $encodedColllectionPath, $request);

        return $this->json($element);
    }

    /**
     * Delete a Colllection element.
     *
     * @Route("/{encodedElementBasename}", name="delete", methods={"DELETE"})
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
     * @param string $encodedColllectionPath
     * @param string $encodedElementBasename
     *
     * @return Response
     *
     * @throws NotSupportedElementTypeException
     */
    public function deleteColllectionElement(string $encodedColllectionPath, string $encodedElementBasename): Response
    {
        $this->colllectionElementService->delete($encodedElementBasename, $encodedColllectionPath);

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
