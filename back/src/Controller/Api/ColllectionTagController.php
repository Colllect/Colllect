<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\FilesystemCannotWriteException;
use App\Exception\TagAlreadyExistsException;
use App\Model\Tag;
use App\Service\ColllectionTagService;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use Nelmio\ApiDocBundle\Annotation as ApiDoc;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ColllectionTagController extends AbstractController
{
    public function __construct(
        private readonly ColllectionTagService $colllectionTagService,
    ) {
    }

    /**
     * Create a Colllection tag.
     *
     *
     * @ApiDoc\Areas({"default"})
     *
     * @SWG\Tag(name="Colllection Tags")
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
     *
     * @SWG\Parameter(
     *     name="name",
     *     in="formData",
     *     description="Name of the colllection tag",
     *     required=false,
     *     type="string"
     * )
     *
     * @SWG\Response(
     *     response=201,
     *     description="Returned when Colllection tag was created",
     *     @SWG\Schema(ref=@ApiDoc\Model(type=Tag::class))
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Returned when form is invalid"
     * )
     *
     * @throws FilesystemCannotWriteException
     * @throws TagAlreadyExistsException
     * @throws FileNotFoundException
     */
    #[Route(path: '/', name: 'create', methods: ['POST'])]
    public function createColllectionTag(Request $request, string $encodedColllectionPath) : JsonResponse
    {
        $response = $this->colllectionTagService->create($encodedColllectionPath, $request);
        return $this->json($response, Response::HTTP_CREATED);
    }

    /**
     * List all Colllection tags.
     *
     *
     * @ApiDoc\Areas({"default"})
     *
     * @SWG\Tag(name="Colllection Tags")
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
     *     description="Returned when colllection tags are listed",
     *     @SWG\Schema(ref="#/definitions/TagList")
     * )
     *
     * @throws FileNotFoundException
     */
    #[Route(path: '/', name: 'list', methods: ['GET'])]
    public function listColllectionTags(string $encodedColllectionPath) : JsonResponse
    {
        $tags = $this->colllectionTagService->list($encodedColllectionPath);
        return $this->json($tags);
    }

    /**
     * Get a Colllection tag.
     *
     *
     * @ApiDoc\Areas({"default"})
     *
     * @SWG\Tag(name="Colllection Tags")
     *
     * @SWG\Parameter(
     *     name="encodedColllectionPath",
     *     in="path",
     *     description="Encoded colllection path",
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="encodedTagName",
     *     in="path",
     *     description="Encoded colllection tag name",
     *     type="string"
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returned when Colllection tag is found",
     *     @SWG\Schema(ref=@ApiDoc\Model(type=Tag::class))
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Returned when Colllection tag does not exists"
     * )
     */
    #[Route(path: '/{encodedTagName}', name: 'get', methods: ['GET'])]
    public function getColllectionTag(string $encodedColllectionPath, string $encodedTagName) : JsonResponse
    {
        $tag = $this->colllectionTagService->get($encodedColllectionPath, $encodedTagName);
        return $this->json($tag);
    }

    /**
     * Update a Colllection tag.
     *
     *
     * @ApiDoc\Areas({"default"})
     *
     * @SWG\Tag(name="Colllection Tags")
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
     *     name="encodedTagName",
     *     in="path",
     *     description="Encoded colllection tag name",
     *     type="string"
     * )
     *
     * @SWG\Parameter(
     *     name="name",
     *     in="formData",
     *     description="Name of the colllection tag",
     *     required=false,
     *     type="string"
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returned when Colllection tag was updated",
     *     @SWG\Schema(ref=@ApiDoc\Model(type=Tag::class))
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Returned when form is invalid"
     * )
     *
     * @throws FilesystemCannotWriteException
     * @throws TagAlreadyExistsException
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    #[Route(path: '/{encodedTagName}', name: 'update', methods: ['PUT'])]
    public function updateColllectionTag(Request $request, string $encodedColllectionPath, string $encodedTagName) : JsonResponse
    {
        $response = $this->colllectionTagService->update($encodedColllectionPath, $encodedTagName, $request);
        return $this->json($response);
    }

    /**
     * Delete a Colllection tag.
     *
     *
     * @ApiDoc\Areas({"default"})
     *
     * @SWG\Tag(name="Colllection Tags")
     *
     * @SWG\Parameter(
     *     name="encodedColllectionPath",
     *     in="path",
     *     description="Encoded colllection path",
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="encodedTagName",
     *     in="path",
     *     description="Encoded colllection tag name",
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
     * @throws FilesystemCannotWriteException
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    #[Route(path: '/{encodedTagName}', name: 'delete', methods: ['DELETE'])]
    public function deleteColllection(string $encodedColllectionPath, string $encodedTagName) : Response
    {
        $this->colllectionTagService->delete($encodedColllectionPath, $encodedTagName);
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
