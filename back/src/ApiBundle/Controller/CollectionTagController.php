<?php

namespace ApiBundle\Controller;

use ApiBundle\Model\Tag;
use ApiBundle\Service\CollectionTagService;
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
class CollectionTagController extends FOSRestController
{
    /**
     * List all Collection tags
     *
     * @Rest\Route("/collections/{encodedCollectionPath}/tags")
     * @Rest\View()
     *
     * @Operation(
     *     tags={"Collection Tags"},
     *     summary="List all Collection tags",
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when collection tags are listed",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(@Model(type=Tag::class))
     *         )
     *     )
     * )
     *
     * @param string $encodedCollectionPath
     * @return Tag[]
     */
    public function getCollectionTagsAction(string $encodedCollectionPath)
    {
        /** @var CollectionTagService $collectionTagService */
        $collectionTagService = $this->get('api.service.collection_tag');
        $elements = $collectionTagService->list($encodedCollectionPath);

        return $elements;
    }


    /**
     * Create a Collection tag
     *
     * @Rest\Route("/collections/{encodedCollectionPath}/tags")
     * @Rest\View(statusCode=201)
     *
     * @Operation(
     *     tags={"Collection Tags"},
     *     summary="Create a Collection tag",
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="Name of the collection tag",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Returned when Collection tag was created",
     *         @SWG\Schema(@Model(type=Tag::class))
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Returned when form is invalid"
     *     )
     * )
     *
     * @param Request $request
     * @param string $encodedCollectionPath
     * @return Tag|FormInterface
     */
    public function postCollectionTagAction(Request $request, string $encodedCollectionPath)
    {
        /** @var CollectionTagService $collectionTagService */
        $collectionTagService = $this->get('api.service.collection_tag');
        $response = $collectionTagService->create($encodedCollectionPath, $request);

        return $response;
    }


    /**
     * Get a Collection tag
     *
     * @Rest\Route("/collections/{encodedCollectionPath}/tags/{encodedTagName}")
     * @Rest\View()
     *
     * @Operation(
     *     tags={"Collection Tags"},
     *     summary="Get a Collection tag",
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Parameter(
     *         name="encodedCollectionPath",
     *         in="path",
     *         description="Encoded collection path",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="encodedTagName",
     *         in="path",
     *         description="Encoded collection tag name",
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when Collection tag is found",
     *         @SWG\Schema(@Model(type=Tag::class))
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Returned when Collection tag does not exists"
     *     )
     * )
     *
     * @param string $encodedCollectionPath
     * @param string $encodedTagName
     * @return Tag
     */
    public function getCollectionTagAction(string $encodedCollectionPath, string $encodedTagName)
    {
        /** @var CollectionTagService $collectionService */
        $collectionTagService = $this->get('api.service.collection_tag');
        $tag = $collectionTagService->get($encodedCollectionPath, $encodedTagName);

        return $tag;
    }


    /**
     * Update a Collection tag
     *
     * @Rest\Route("/collections/{encodedCollectionPath}/tags/{encodedTagName}")
     * @Rest\View(statusCode=200)
     *
     * @Operation(
     *     tags={"Collection Tags"},
     *     summary="Update a Collection tag",
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Parameter(
     *         name="encodedCollectionPath",
     *         in="path",
     *         description="Encoded collection path",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="encodedTagName",
     *         in="path",
     *         description="Encoded collection tag name",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="Name of the collection tag",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when Collection tag was updated",
     *         @SWG\Schema(@Model(type=Tag::class))
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Returned when form is invalid"
     *     )
     * )
     *
     * @param Request $request
     * @param string $encodedCollectionPath
     * @param string $encodedTagName
     * @return Tag|FormInterface
     */
    public function putCollectionTagAction(Request $request, string $encodedCollectionPath, string $encodedTagName)
    {
        /** @var CollectionTagService $collectionTagService */
        $collectionTagService = $this->get('api.service.collection_tag');
        $response = $collectionTagService->update($encodedCollectionPath, $encodedTagName, $request);

        return $response;
    }


    /**
     * Delete a Collection tag
     *
     * @Rest\Route("/collections/{encodedCollectionPath}/tags/{encodedTagName}")
     * @Rest\View(statusCode=204)
     *
     * @Operation(
     *     tags={"Collection Tags"},
     *     summary="Delete a Collection tag",
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Parameter(
     *         name="encodedCollectionPath",
     *         in="path",
     *         description="Encoded collection path",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="encodedTagName",
     *         in="path",
     *         description="Encoded collection tag name",
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="Returned when Collection file is deleted"
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Returned when Collection file is not found"
     *     )
     * )
     *
     * @param string $encodedCollectionPath
     * @param string $encodedTagName
     */
    public function deleteCollectionAction(string $encodedCollectionPath, string $encodedTagName)
    {
        /** @var CollectionTagService $collectionTagService */
        $collectionTagService = $this->get('api.service.collection_tag');
        $collectionTagService->delete($encodedCollectionPath, $encodedTagName);
    }
}
