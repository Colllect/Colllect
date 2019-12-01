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
    /**
     * @var ColllectionTagService
     */
    private $colllectionTagService;

    public function __construct(ColllectionTagService $colllectionTagService)
    {
        $this->colllectionTagService = $colllectionTagService;
    }

    /**
     * Create a Colllection tag.
     *
     * @Route("/", name="create", methods={"POST"})
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
    public function createColllectionTag(Request $request, string $encodedColllectionPath): JsonResponse
    {
        $response = $this->colllectionTagService->create($encodedColllectionPath, $request);

        return $this->json($response, Response::HTTP_CREATED);
    }

    /**
     * List all Colllection tags.
     *
     * @Route("/", name="list", methods={"GET"})
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
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(
     *             property="itemListElement",
     *             type="array",
     *             @SWG\Items(ref=@ApiDoc\Model(type=Tag::class))
     *         )
     *     )
     * )
     *
     * @throws FileNotFoundException
     */
    public function listColllectionTags(string $encodedColllectionPath): JsonResponse
    {
        $tags = $this->colllectionTagService->list($encodedColllectionPath);

        return $this->json(
            [
                'itemListElement' => $tags,
            ]
        );
    }

    /**
     * Get a Colllection tag.
     *
     * @Route("/{encodedTagName}", name="get", methods={"GET"})
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
    public function getColllectionTag(string $encodedColllectionPath, string $encodedTagName): JsonResponse
    {
        $tag = $this->colllectionTagService->get($encodedColllectionPath, $encodedTagName);

        return $this->json($tag);
    }

    /**
     * Update a Colllection tag.
     *
     * @Route("/{encodedTagName}", name="update", methods={"PUT"})
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
    public function updateColllectionTag(
        Request $request,
        string $encodedColllectionPath,
        string $encodedTagName
    ): JsonResponse {
        $response = $this->colllectionTagService->update($encodedColllectionPath, $encodedTagName, $request);

        return $this->json($response);
    }

    /**
     * Delete a Colllection tag.
     *
     * @Route("/{encodedTagName}", name="delete", methods={"DELETE"})
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
    public function deleteColllection(string $encodedColllectionPath, string $encodedTagName): Response
    {
        $this->colllectionTagService->delete($encodedColllectionPath, $encodedTagName);

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
