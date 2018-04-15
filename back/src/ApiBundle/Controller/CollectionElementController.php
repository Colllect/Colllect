<?php

namespace ApiBundle\Controller;

use ApiBundle\Model\Element;
use ApiBundle\Service\CollectionElementService;
use ApiBundle\Service\CollectionService;
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
     *         description="Returned when collection files are listed",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(@Model(type=Element::class))
     *         )
     *     )
     * )
     *
     * @param string $encodedCollectionPath
     * @return Element[]
     */
    public function getCollectionElementsAction(string $encodedCollectionPath)
    {
        /** @var CollectionElementService $collectionElementService */
        $collectionElementService = $this->get('api.service.collection_element');
        $elements = $collectionElementService->list($encodedCollectionPath);

        return $elements;
    }


    /**
     * Add an element to Collection
     *
     * To create an element you need to match one of these combination:
     *  - `file`: `type` parameter is ignored as type is detected with file data
     *  - `url` (+ `type`)
     *  - content + type
     *
     * In any case you can define the name of the element with `basename`
     *
     * @Rest\Route("/collections/{encodedCollectionPath}/elements")
     * @Rest\View(statusCode=201)
     *
     * @Operation(
     *     tags={"Collection Elements"},
     *     summary="Add an element to Collection",
     *     consumes={"multipart/form-data"},
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
     *         name="file",
     *         in="formData",
     *         description="Element file to import",
     *         type="file"
     *     ),
     *     @SWG\Parameter(
     *         name="url",
     *         in="formData",
     *         description="Element URL to import from",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="type",
     *         in="formData",
     *         description="Element type",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="Element name",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="tags",
     *         in="formData",
     *         description="Element tag list",
     *         type="array",
     *         @SWG\Items(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="content",
     *         in="formData",
     *         description="Element content",
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Returned when element was created in Collection",
     *         @SWG\Schema(@Model(type=Element::class))
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Returned when form is invalid"
     *     )
     * )
     *
     * @param Request $request
     * @param string $encodedCollectionPath
     * @return Element|FormInterface
     * @throws \ApiBundle\Exception\FilesystemCannotWriteException
     */
    public function postCollectionElementAction(Request $request, string $encodedCollectionPath)
    {
        /** @var CollectionElementService $collectionElementService */
        $collectionElementService = $this->get('api.service.collection_element');
        $element = $collectionElementService->create($encodedCollectionPath, $request);

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
     *         name="encodedElementBasename",
     *         in="path",
     *         description="Encoded element basename",
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when Collection file is found",
     *         @SWG\Schema(@Model(type=Element::class))
     *     ),
     *     @SWG\Response(
     *         response=404,
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
        /** @var CollectionElementService $collectionElementService */
        $collectionElementService = $this->get('api.service.collection_element');
        $element = $collectionElementService->get($encodedElementBasename, $encodedCollectionPath);

        return $element;
    }


    /**
     * Update a Collection element
     *
     * @Rest\Route("/collections/{encodedCollectionPath}/elements/{encodedElementBasename}")
     * @Rest\View()
     *
     * @Operation(
     *     tags={"Collection Elements"},
     *     summary="Update a Collection element",
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
     *         name="encodedElementBasename",
     *         in="path",
     *         description="Encoded element basename",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="Element name",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="tags",
     *         in="formData",
     *         description="Element tag list",
     *         type="array",
     *         @SWG\Items(type="string")
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when Collection file is updated",
     *         @SWG\Schema(@Model(type=Element::class))
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Returned when form is invalid"
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Returned when Collection file is not found"
     *     )
     * )
     *
     * @param Request $request
     * @param string $encodedCollectionPath
     * @param string $encodedElementBasename
     * @return Element
     */
    public function putCollectionElementAction(Request $request, string $encodedCollectionPath, string $encodedElementBasename)
    {
        /** @var CollectionElementService $collectionElementService */
        $collectionElementService = $this->get('api.service.collection_element');
        $element = $collectionElementService->update($encodedElementBasename, $encodedCollectionPath, $request);

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
     *         name="encodedElementBasename",
     *         in="path",
     *         description="Encoded element basename",
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
     * @param string $encodedElementBasename
     */
    public function deleteCollectionElementAction(string $encodedCollectionPath, string $encodedElementBasename)
    {
        /** @var CollectionElementService $collectionElementService */
        $collectionElementService = $this->get('api.service.collection_element');
        $collectionElementService->delete($encodedElementBasename, $encodedCollectionPath);
    }
}
