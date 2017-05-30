<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @ApiDoc(
     *     resource=true,
     *     resourceDescription="Collections",
     *     section="Collections",
     *     statusCodes={
     *         200="Returned when collections are listed"
     *     },
     *     responseMap={
     *         200="array<ApiBundle\Model\Collection>"
     *     }
     * )
     *
     * @return \ApiBundle\Model\Collection[]
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
     * @ApiDoc(
     *     section="Collections",
     *     input={"class"=\ApiBundle\Form\Type\CollectionType::class, "name"=""},
     *     statusCodes={
     *         201="Returned when Collection was created",
     *         400="Returned when form is invalid"
     *     },
     *     responseMap={
     *         201=ApiBundle\Model\Collection::class
     *     }
     * )
     *
     * @param Request $request
     * @return array|\Symfony\Component\Form\Form
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
     * @ApiDoc(
     *     section="Collections",
     *     requirements={
     *         {"name"="encodedCollectionPath", "requirement"="base64 URL encoded", "dataType"="string", "description"="Collection path encoded as base64 URL"}
     *     },
     *     statusCodes={
     *         200="Returned when Collection is found",
     *         404="Returned when Collection file is not found"
     *     }
     * )
     *
     * @param string $encodedCollectionPath
     * @return string
     */
    public function getCollectionAction($encodedCollectionPath)
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
     * @ApiDoc(
     *     section="Collections",
     *     requirements={
     *         {"name"="encodedCollectionPath", "requirement"="base64 URL encoded", "dataType"="string", "description"="Collection path encoded as base64 URL"}
     *     },
     *     statusCodes={
     *         204="Returned when Collection file is deleted",
     *         404="Returned when Collection file is not found"
     *     }
     * )
     *
     * @param string $encodedCollectionPath
     */
    public function deleteCollectionAction($encodedCollectionPath)
    {
        $collectionService = $this->get('api.service.collection');
        $collectionService->delete($encodedCollectionPath);
    }
}
