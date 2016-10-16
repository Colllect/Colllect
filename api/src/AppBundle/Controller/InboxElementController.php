<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\ElementType;
use AppBundle\Service\CollectionService;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Security("has_role('ROLE_USER')")
 */
class InboxElementController extends FOSRestController
{
    /**
     * List all Inbox elements
     *
     * @Rest\Route("/inbox/elements")
     * @Rest\View()
     *
     * @ApiDoc(
     *     section="Inbox Elements",
     *     statusCodes={
     *         200="Returned when inbox files are listed"
     *     },
     *     responseMap={
     *         200="array<AppBundle\Model\Element>"
     *     }
     * )
     */
    public function getInboxElementsAction()
    {
        $collectionService = $this->get('app.service.collection');
        $elements = $collectionService->listElements(CollectionService::INBOX_FOLDER);

        return $elements;
    }


    /**
     * Add an element to Inbox
     *
     * @Rest\Route("/inbox/elements")
     * @Rest\View(statusCode=201)
     *
     * @ApiDoc(
     *     section="Inbox Elements",
     *     input={"class"=\AppBundle\Form\Type\ElementType::class, "name"=""},
     *     statusCodes={
     *         201="Returned when element was created in Inbox",
     *         400="Returned when form is invalid"
     *     },
     *     responseMap={
     *         201=AppBundle\Model\AbstractElement::class
     *     }
     * )
     *
     * @param Request $request
     * @return array|\Symfony\Component\Form\Form
     */
    public function postInboxElementsAction(Request $request)
    {
        $collectionService = $this->get('app.service.collection');
        $response = $collectionService->addElement($request, CollectionService::INBOX_FOLDER);

        return $response;
    }


    /**
     * Get an Inbox element
     *
     * @Rest\Route("/inbox/elements/{encodedElementBasename}")
     * @Rest\View()
     *
     * @ApiDoc(
     *     section="Inbox Elements",
     *     requirements={
     *         {"name"="encodedElementBasename", "requirement"="base64 URL encoded", "dataType"="string", "description"="Element basename encoded as base64 URL"}
     *     },
     *     statusCodes={
     *         200="Returned when Inbox file is found",
     *         404="Returned when Inbox file is not found"
     *     }
     * )
     *
     * @param $encodedElementBasename
     * @return string
     */
    public function getInboxElementAction($encodedElementBasename)
    {
        $collectionService = $this->get('app.service.collection');
        $element = $collectionService->getElementByEncodedElementBasename($encodedElementBasename, CollectionService::INBOX_FOLDER);

        return $element;
    }


    /**
     * Delete an Inbox element
     *
     * @Rest\Route("/inbox/elements/{encodedElementBasename}")
     * @Rest\View(statusCode=204)
     *
     * @ApiDoc(
     *     section="Inbox Elements",
     *     requirements={
     *         {"name"="encodedElementBasename", "requirement"="base64 URL encoded", "dataType"="string", "description"="Element basename encoded as base64 URL"}
     *     },
     *     statusCodes={
     *         204="Returned when Inbox file is deleted",
     *         404="Returned when Inbox file is not found"
     *     }
     * )
     *
     * @param $encodedElementBasename
     */
    public function deleteInboxElementAction($encodedElementBasename)
    {
        $collectionService = $this->get('app.service.collection');
        $collectionService->deleteElementByEncodedElementBasename($encodedElementBasename, CollectionService::INBOX_FOLDER);
    }
}
