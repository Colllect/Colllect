<?php

namespace ApiBundle\Controller;

use ApiBundle\Model\Tag;
use ApiBundle\Service\ColllectionTagService;
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
class ColllectionTagController extends FOSRestController
{
    /**
     * List all Colllection tags
     *
     * @Rest\Route("/colllections/{encodedColllectionPath}/tags")
     * @Rest\View()
     *
     * @Operation(
     *     tags={"Colllection Tags"},
     *     summary="List all Colllection tags",
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when colllection tags are listed",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(@Model(type=Tag::class))
     *         )
     *     )
     * )
     *
     * @param string $encodedColllectionPath
     * @return Tag[]
     */
    public function getColllectionTagsAction(string $encodedColllectionPath)
    {
        /** @var ColllectionTagService $colllectionTagService */
        $colllectionTagService = $this->get('api.service.colllection_tag');
        $elements = $colllectionTagService->list($encodedColllectionPath);

        return $elements;
    }


    /**
     * Create a Colllection tag
     *
     * @Rest\Route("/colllections/{encodedColllectionPath}/tags")
     * @Rest\View(statusCode=201)
     *
     * @Operation(
     *     tags={"Colllection Tags"},
     *     summary="Create a Colllection tag",
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="Name of the colllection tag",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Returned when Colllection tag was created",
     *         @SWG\Schema(@Model(type=Tag::class))
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Returned when form is invalid"
     *     )
     * )
     *
     * @param Request $request
     * @param string $encodedColllectionPath
     * @return Tag|FormInterface
     */
    public function postColllectionTagAction(Request $request, string $encodedColllectionPath)
    {
        /** @var ColllectionTagService $colllectionTagService */
        $colllectionTagService = $this->get('api.service.colllection_tag');
        $response = $colllectionTagService->create($encodedColllectionPath, $request);

        return $response;
    }


    /**
     * Get a Colllection tag
     *
     * @Rest\Route("/colllections/{encodedColllectionPath}/tags/{encodedTagName}")
     * @Rest\View()
     *
     * @Operation(
     *     tags={"Colllection Tags"},
     *     summary="Get a Colllection tag",
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Parameter(
     *         name="encodedColllectionPath",
     *         in="path",
     *         description="Encoded colllection path",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="encodedTagName",
     *         in="path",
     *         description="Encoded colllection tag name",
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when Colllection tag is found",
     *         @SWG\Schema(@Model(type=Tag::class))
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Returned when Colllection tag does not exists"
     *     )
     * )
     *
     * @param string $encodedColllectionPath
     * @param string $encodedTagName
     * @return Tag
     */
    public function getColllectionTagAction(string $encodedColllectionPath, string $encodedTagName)
    {
        /** @var ColllectionTagService $colllectionService */
        $colllectionTagService = $this->get('api.service.colllection_tag');
        $tag = $colllectionTagService->get($encodedColllectionPath, $encodedTagName);

        return $tag;
    }


    /**
     * Update a Colllection tag
     *
     * @Rest\Route("/colllections/{encodedColllectionPath}/tags/{encodedTagName}")
     * @Rest\View(statusCode=200)
     *
     * @Operation(
     *     tags={"Colllection Tags"},
     *     summary="Update a Colllection tag",
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Parameter(
     *         name="encodedColllectionPath",
     *         in="path",
     *         description="Encoded colllection path",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="encodedTagName",
     *         in="path",
     *         description="Encoded colllection tag name",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="Name of the colllection tag",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when Colllection tag was updated",
     *         @SWG\Schema(@Model(type=Tag::class))
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Returned when form is invalid"
     *     )
     * )
     *
     * @param Request $request
     * @param string $encodedColllectionPath
     * @param string $encodedTagName
     * @return Tag|FormInterface
     */
    public function putColllectionTagAction(Request $request, string $encodedColllectionPath, string $encodedTagName)
    {
        /** @var ColllectionTagService $colllectionTagService */
        $colllectionTagService = $this->get('api.service.colllection_tag');
        $response = $colllectionTagService->update($encodedColllectionPath, $encodedTagName, $request);

        return $response;
    }


    /**
     * Delete a Colllection tag
     *
     * @Rest\Route("/colllections/{encodedColllectionPath}/tags/{encodedTagName}")
     * @Rest\View(statusCode=204)
     *
     * @Operation(
     *     tags={"Colllection Tags"},
     *     summary="Delete a Colllection tag",
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Parameter(
     *         name="encodedColllectionPath",
     *         in="path",
     *         description="Encoded colllection path",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="encodedTagName",
     *         in="path",
     *         description="Encoded colllection tag name",
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
     * @param string $encodedTagName
     */
    public function deleteColllectionAction(string $encodedColllectionPath, string $encodedTagName)
    {
        /** @var ColllectionTagService $colllectionTagService */
        $colllectionTagService = $this->get('api.service.colllection_tag');
        $colllectionTagService->delete($encodedColllectionPath, $encodedTagName);
    }
}
