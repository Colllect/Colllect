<?php

namespace ApiBundle\Controller;

use ApiBundle\Model\Collection;
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
class CollectionController extends FOSRestController
{
    /**
     * List all Collections
     *
     * @Rest\Route("/collections")
     * @Rest\View()
     *
     * @Operation(
     *     tags={"Collections"},
     *     summary="List all Collections",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when collections are listed",
     *         @SWG\Schema(
     *             type="array",
     *             @Model(type=Collection::class)
     *         )
     *     )
     * )
     *
     * @return Collection[]
     */
    public function getCollectionsAction()
    {
        $collectionService = $this->get('api.service.collection');
        $elements = $collectionService->list();

        return $elements;
    }


    /**
     * Create a Collection
     *
     * @Rest\Route("/collections")
     * @Rest\View(statusCode=201)
     *
     * @Operation(
     *     tags={"Collections"},
     *     summary="Create a Collection",
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="Name of the collection",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Returned when Collection was created",
     *         @Model(type=Collection::class)
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when form is invalid"
     *     )
     * )
     *
     * @param Request $request
     * @return Collection|FormInterface
     */
    public function postCollectionAction(Request $request)
    {
        $collectionService = $this->get('api.service.collection');
        $response = $collectionService->create($request);

        return $response;
    }


    /**
     * Get a Collection
     *
     * @Rest\Route("/collections/{encodedCollectionPath}")
     * @Rest\View()
     *
     * @Operation(
     *     tags={"Collections"},
     *     summary="Get a Collection",
     *     @SWG\Parameter(
     *         name="encodedCollectionPath",
     *         in="path",
     *         description="Encoded collection path",
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when Collection is found",
     *         @Model(type=Collection::class)
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when Collection file is not found"
     *     )
     * )
     *
     * @param string $encodedCollectionPath
     * @return Collection
     */
    public function getCollectionAction(string $encodedCollectionPath)
    {
        $collectionService = $this->get('api.service.collection');
        $collection = $collectionService->get($encodedCollectionPath);

        return $collection;
    }


    /**
     * Delete a Collection
     *
     * @Rest\Route("/collections/{encodedCollectionPath}")
     * @Rest\View(statusCode=204)
     *
     * @Operation(
     *     tags={"Collections"},
     *     summary="Delete a Collection",
     *     @SWG\Parameter(
     *         name="encodedCollectionPath",
     *         in="path",
     *         description="Encoded collection path",
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="Returned when Collection file is deleted"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when Collection file is not found"
     *     )
     * )
     *
     * @param string $encodedCollectionPath
     */
    public function deleteCollectionAction(string $encodedCollectionPath)
    {
        $collectionService = $this->get('api.service.collection');
        $collectionService->delete($encodedCollectionPath);
    }
}
