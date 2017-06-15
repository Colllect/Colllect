<?php

namespace ApiBundle\Controller;

use ApiBundle\Model\Element;
use ApiBundle\Form\Type\ElementType;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormInterface;
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
     * @Operation(
     *     tags={"Collection Elements"},
     *     summary="List all Collection elements",
     *     @SWG\Parameter(
     *         name="encodedCollectionPath",
     *         in="path",
     *         description="Encoded collection path",
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when collection files are listed",
     *         @SWG\Schema(
     *             type="array",
     *             @Model(type=Element::class)
     *         )
     *     )
     * )
     *
     * @param string $encodedCollectionPath
     * @return Element[]
     */
    public function getCollectionElementsAction(string $encodedCollectionPath)
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
     * @Operation(
     *     tags={"Collection Elements"},
     *     summary="Add an element to Collection",
     *     @SWG\Parameter(
     *         name="encodedCollectionPath",
     *         in="path",
     *         description="Encoded collection path",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="form",
     *         in="body",
     *         description="Element",
     *         @Model(type=ElementType::class)
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Returned when element was created in Collection",
     *         @Model(type=Element::class)
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when form is invalid"
     *     )
     * )
     *
     * @param Request $request
     * @param string $encodedCollectionPath
     * @return Element|FormInterface
     */
    public function postCollectionElementAction(Request $request, string $encodedCollectionPath)
    {
        $collectionService = $this->get('api.service.collection');
        $element = $collectionService->addElement($request, $encodedCollectionPath);

        return $element;
    }


    /**
     * Get a Collection element
     *
     * @Rest\Route("/collections/{encodedCollectionPath}/elements/{encodedElementBasename}")
     * @Rest\View()
     *
     * @Operation(
     *     tags={"Collection Elements"},
     *     summary="Get a Collection element",
     *     @SWG\Parameter(
     *         name="encodedCollectionPath",
     *         in="path",
     *         description="Encoded collection path",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="encodedElementBasename",
     *         in="path",
     *         description="Encoded element basename",
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when Collection file is found",
     *         @Model(type=Element::class)
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when Collection file is not found"
     *     )
     * )
     *
     * @param string $encodedCollectionPath
     * @param string $encodedElementBasename
     * @return Element
     */
    public function getCollectionElementAction(string $encodedCollectionPath, string $encodedElementBasename)
    {
        $collectionService = $this->get('api.service.collection');
        $element = $collectionService->getElementByEncodedElementBasename($encodedElementBasename, $encodedCollectionPath);

        return $element;
    }


    /**
     * Delete a Collection element
     *
     * @Rest\Route("/collections/{encodedCollectionPath}/elements/{encodedElementBasename}")
     * @Rest\View(statusCode=204)
     *
     * @Operation(
     *     tags={"Collection Elements"},
     *     summary="Delete a Collection element",
     *     @SWG\Parameter(
     *         name="encodedCollectionPath",
     *         in="path",
     *         description="Encoded collection path",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="encodedElementBasename",
     *         in="path",
     *         description="Encoded element basename",
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
     * @param string $encodedElementBasename
     */
    public function deleteCollectionElementAction(string $encodedCollectionPath, string $encodedElementBasename)
    {
        $collectionService = $this->get('api.service.collection');
        $collectionService->deleteElementByEncodedElementBasename($encodedElementBasename, $encodedCollectionPath);
    }
}
