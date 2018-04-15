<?php

namespace ApiBundle\Controller;

use ApiBundle\Model\Collection;
use ApiBundle\Service\CollectionService;
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
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when collections are listed",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(@Model(type=Collection::class))
     *         )
     *     )
     * )
     *
     * @return Collection[]
     */
    public function getCollectionsAction()
    {
        /** @var CollectionService $collectionService */
        $collectionService = $this->get('api.service.collection');
        $collections = $collectionService->list();

        return $collections;
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
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="Name of the collection",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Returned when Collection was created",
     *         @SWG\Schema(@Model(type=Collection::class))
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Returned when form is invalid"
     *     )
     * )
     *
     * @param Request $request
     * @return Collection|FormInterface
     */
    public function postCollectionAction(Request $request)
    {
        /** @var CollectionService $collectionService */
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
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Parameter(
     *         name="encodedCollectionPath",
     *         in="path",
     *         description="Encoded collection path",
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when Collection is found",
     *         @SWG\Schema(@Model(type=Collection::class))
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Returned when Collection file is not found"
     *     )
     * )
     *
     * @param string $encodedCollectionPath
     * @return Collection
     */
    public function getCollectionAction(string $encodedCollectionPath)
    {
        /** @var CollectionService $collectionService */
        $collectionService = $this->get('api.service.collection');
        $collection = $collectionService->get($encodedCollectionPath);

        return $collection;
    }


    /**
     * Update a Collection
     *
     * @Rest\Route("/collections/{encodedCollectionPath}")
     * @Rest\View(statusCode=200)
     *
     * @Operation(
     *     tags={"Collections"},
     *     summary="Update a Collection",
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="Name of the collection",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when Collection was updated",
     *         @SWG\Schema(@Model(type=Collection::class))
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Returned when form is invalid"
     *     )
     * )
     *
     * @param Request $request
     * @param string $encodedCollectionPath
     * @return Collection|FormInterface
     */
    public function putCollectionAction(Request $request, string $encodedCollectionPath)
    {
        /** @var CollectionService $collectionService */
        $collectionService = $this->get('api.service.collection');
        $response = $collectionService->update($encodedCollectionPath, $request);

        return $response;
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
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Parameter(
     *         name="encodedCollectionPath",
     *         in="path",
     *         description="Encoded collection path",
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
     */
    public function deleteCollectionAction(string $encodedCollectionPath)
    {
        /** @var CollectionService $collectionService */
        $collectionService = $this->get('api.service.collection');
        $collectionService->delete($encodedCollectionPath);
    }
}
