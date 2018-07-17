<?php

namespace ApiBundle\Controller;

use ApiBundle\Model\Element;
use ApiBundle\Service\ColllectionElementService;
use ApiBundle\Service\ColllectionService;
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
class ColllectionElementController extends FOSRestController
{
    /**
     * List all Colllection elements
     *
     * @Rest\Route("/colllections/{encodedColllectionPath}/elements")
     * @Rest\View()
     *
     * @Operation(
     *     tags={"Colllection Elements"},
     *     summary="List all Colllection elements",
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Parameter(
     *         name="encodedColllectionPath",
     *         in="path",
     *         description="Encoded colllection path",
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when colllection files are listed",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(@Model(type=Element::class))
     *         )
     *     )
     * )
     *
     * @param string $encodedColllectionPath
     * @return Element[]
     */
    public function getColllectionElementsAction(string $encodedColllectionPath)
    {
        /** @var ColllectionElementService $colllectionElementService */
        $colllectionElementService = $this->get('api.service.colllection_element');
        $elements = $colllectionElementService->list($encodedColllectionPath);

        return $elements;
    }


    /**
     * Add an element to Colllection
     *
     * To create an element you need to match one of these combination:
     *  - `file`: `type` parameter is ignored as type is detected with file data
     *  - `url` (+ `type`)
     *  - content + type
     *
     * In any case you can define the name of the element with `basename`
     *
     * @Rest\Route("/colllections/{encodedColllectionPath}/elements")
     * @Rest\View(statusCode=201)
     *
     * @Operation(
     *     tags={"Colllection Elements"},
     *     summary="Add an element to Colllection",
     *     consumes={"multipart/form-data"},
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
     *         description="Returned when element was created in Colllection",
     *         @SWG\Schema(@Model(type=Element::class))
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Returned when form is invalid"
     *     )
     * )
     *
     * @param Request $request
     * @param string $encodedColllectionPath
     * @return Element|FormInterface
     * @throws \ApiBundle\Exception\FilesystemCannotWriteException
     */
    public function postColllectionElementAction(Request $request, string $encodedColllectionPath)
    {
        /** @var ColllectionElementService $colllectionElementService */
        $colllectionElementService = $this->get('api.service.colllection_element');
        $element = $colllectionElementService->create($encodedColllectionPath, $request);

        return $element;
    }


    /**
     * Get a Colllection element
     *
     * @Rest\Route("/colllections/{encodedColllectionPath}/elements/{encodedElementBasename}")
     * @Rest\View()
     *
     * @Operation(
     *     tags={"Colllection Elements"},
     *     summary="Get a Colllection element",
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
     *         name="encodedElementBasename",
     *         in="path",
     *         description="Encoded element basename",
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when Colllection file is found",
     *         @SWG\Schema(@Model(type=Element::class))
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Returned when Colllection file is not found"
     *     )
     * )
     *
     * @param string $encodedColllectionPath
     * @param string $encodedElementBasename
     * @return Element
     */
    public function getColllectionElementAction(string $encodedColllectionPath, string $encodedElementBasename)
    {
        /** @var ColllectionElementService $colllectionElementService */
        $colllectionElementService = $this->get('api.service.colllection_element');
        $element = $colllectionElementService->get($encodedElementBasename, $encodedColllectionPath);

        return $element;
    }


    /**
     * Update a Colllection element
     *
     * @Rest\Route("/colllections/{encodedColllectionPath}/elements/{encodedElementBasename}")
     * @Rest\View()
     *
     * @Operation(
     *     tags={"Colllection Elements"},
     *     summary="Update a Colllection element",
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
     *         description="Returned when Colllection file is updated",
     *         @SWG\Schema(@Model(type=Element::class))
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Returned when form is invalid"
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Returned when Colllection file is not found"
     *     )
     * )
     *
     * @param Request $request
     * @param string $encodedColllectionPath
     * @param string $encodedElementBasename
     * @return Element
     */
    public function putColllectionElementAction(Request $request, string $encodedColllectionPath, string $encodedElementBasename)
    {
        /** @var ColllectionElementService $colllectionElementService */
        $colllectionElementService = $this->get('api.service.colllection_element');
        $element = $colllectionElementService->update($encodedElementBasename, $encodedColllectionPath, $request);

        return $element;
    }


    /**
     * Delete a Colllection element
     *
     * @Rest\Route("/colllections/{encodedColllectionPath}/elements/{encodedElementBasename}")
     * @Rest\View(statusCode=204)
     *
     * @Operation(
     *     tags={"Colllection Elements"},
     *     summary="Delete a Colllection element",
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
     *         name="encodedElementBasename",
     *         in="path",
     *         description="Encoded element basename",
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
     * @param string $encodedElementBasename
     */
    public function deleteColllectionElementAction(string $encodedColllectionPath, string $encodedElementBasename)
    {
        /** @var ColllectionElementService $colllectionElementService */
        $colllectionElementService = $this->get('api.service.colllection_element');
        $colllectionElementService->delete($encodedElementBasename, $encodedColllectionPath);
    }
}
