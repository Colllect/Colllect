<?php

namespace ApiBundle\Controller;

use ApiBundle\Service\CollectionService;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Security("has_role('ROLE_USER')")
 */
class CollectionElementController extends FOSRestController
{
    /**
     * List all Collection elements
     *
     * @Rest\Route("/collections/{encodedCollectionPath}/elements")
     * @Rest\View()
     *
     * @ApiDoc(
     *     resource=true,
     *     resourceDescription="Collection Elements",
     *     section="Collection Elements",
     *     statusCodes={
     *         200="Returned when inbox files are listed"
     *     },
     *     responseMap={
     *         200="array<ApiBundle\Model\Element>"
     *     }
     * )
     * @param string $encodedCollectionPath
     * @return \ApiBundle\Model\Element[]
     */
    public function getCollectionElementsAction($encodedCollectionPath)
    {
        $collectionService = $this->get('api.service.collection');
        $elements = $collectionService->listElements($encodedCollectionPath);

        return $elements;
    }


    /**
     * Add an element to Collection
     *
     * @Rest\Route("/collections/{encodedCollectionPath}/elements")
     * @Rest\View(statusCode=201)
     *
     * @ApiDoc(
     *     section="Collection Elements",
     *     input={"class"=\ApiBundle\Form\Type\ElementType::class, "name"=""},
     *     statusCodes={
     *         201="Returned when element was created in Collection",
     *         400="Returned when form is invalid"
     *     },
     *     responseMap={
     *         201=ApiBundle\Model\AbstractElement::class
     *     }
     * )
     *
     * @param Request $request
     * @param string $encodedCollectionPath
     * @return array|\Symfony\Component\Form\Form
     */
    public function postCollectionElementsAction(Request $request, $encodedCollectionPath)
    {
        $collectionService = $this->get('api.service.collection');
        $response = $collectionService->addElement($request, $encodedCollectionPath);

        return $response;
    }


    /**
     * Get an Collection element
     *
     * @Rest\Route("/collections/{encodedCollectionPath}/elements/{encodedElementBasename}")
     * @Rest\View()
     *
     * @ApiDoc(
     *     section="Collection Elements",
     *     requirements={
     *         {"name"="encodedElementBasename", "requirement"="base64 URL encoded", "dataType"="string", "description"="Element basename encoded as base64 URL"}
     *     },
     *     statusCodes={
     *         200="Returned when Collection file is found",
     *         404="Returned when Collection file is not found"
     *     }
     * )
     *
     * @param string $encodedCollectionPath
     * @param string $encodedElementBasename
     * @return string
     */
    public function getCollectionElementAction($encodedCollectionPath, $encodedElementBasename)
    {
        $collectionService = $this->get('api.service.collection');
        $element = $collectionService->getElementByEncodedElementBasename($encodedElementBasename, $encodedCollectionPath);

        return $element;
    }


    /**
     * Delete an Collection element
     *
     * @Rest\Route("/collections/{encodedCollectionPath}/elements/{encodedElementBasename}")
     * @Rest\View(statusCode=204)
     *
     * @ApiDoc(
     *     section="Collection Elements",
     *     requirements={
     *         {"name"="encodedElementBasename", "requirement"="base64 URL encoded", "dataType"="string", "description"="Element basename encoded as base64 URL"}
     *     },
     *     statusCodes={
     *         204="Returned when Collection file is deleted",
     *         404="Returned when Collection file is not found"
     *     }
     * )
     *
     * @param string $encodedCollectionPath
     * @param string $encodedElementBasename
     */
    public function deleteCollectionElementAction($encodedCollectionPath, $encodedElementBasename)
    {
        $collectionService = $this->get('api.service.collection');
        $collectionService->deleteElementByEncodedElementBasename($encodedElementBasename, $encodedCollectionPath);
    }
}
