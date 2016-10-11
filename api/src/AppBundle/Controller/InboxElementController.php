<?php

namespace AppBundle\Controller;

use AppBundle\FlysystemAdapter\FlysystemAdapterInterface;
use AppBundle\Form\Type\ElementType;
use AppBundle\Model\AbstractElement;
use AppBundle\Model\Element;
use AppBundle\Model\Image;
use League\Flysystem\FilesystemInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Security("has_role('ROLE_USER')")
 */
class InboxElementController extends FOSRestController
{
    const INBOX_FOLDER = 'Inbox';

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
        $filesystem = $this->getFilesystem();

        $metadata = $filesystem->listContents(self::INBOX_FOLDER);

        uasort($metadata, function ($a, $b) {
            if ($a['timestamp'] == $b['timestamp']) {
                return 0;
            }

            return $a['timestamp'] < $b['timestamp'] ? -1 : 1;
        });

        $files = [];
        foreach ($metadata as $meta) {
            if (AbstractElement::isValidElement($meta['path'])) {
                $element = new Image($meta);
                $files[] = $element;
            }
        }

        return $files;
    }


    /**
     * Add an element to Inbox
     *
     * @Rest\Route("/inbox/elements")
     * @Rest\View()
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
     * @return Element|\Symfony\Component\Form\Form
     */
    public function postInboxElementsAction(Request $request)
    {
        $element = new Element();
        $form = $this->createForm(ElementType::class, $element);

        $requestContent = $request->request->all();
        foreach ($request->files as $k => $file) {
            $requestContent[$k] = $file;
        }

        $form->submit($requestContent);

        if (!$form->isValid()) {
            return $form;
        }

        $element->fetchContent();

        $filesystem = $this->getFilesystem();
        $filesystem->write($element->getBasename(), $element->getContent());

        return $element;
    }


    /**
     * Get an Inbox element
     *
     * @Rest\Route("/inbox/elements/{elementName}")
     * @Rest\View()
     *
     * @ApiDoc(
     *     section="Inbox Elements",
     *     requirements={
     *         {"name"="elementName", "requirement"="base64 URL encoded", "dataType"="string", "description"="Element basename as base64 URL encoded"}
     *     },
     *     statusCodes={
     *         200="Returned when Inbox file is found",
     *         404="Returned when Inbox file is not found"
     *     },
     *     responseMap={
     *         400={"class"=ElementType::class, "fos_rest_form_errors"=true, "name"=""}
     *     }
     * )
     *
     * @param $elementName
     * @return string
     */
    public function getInboxElementAction($elementName)
    {
        if (!$this->isValidBase64($elementName)) {
            throw new BadRequestHttpException("request.invalid_element_name");
        }

        $basename = base64_decode($elementName);

        if (!AbstractElement::isValidElement($basename)) {
            throw new BadRequestHttpException("request.unsupported_element_type");
        }

        $filesystem = $this->getFilesystem();

        $path = self::INBOX_FOLDER . '/' . $basename;

        $meta = $filesystem->getMetadata($path);
        $element = new Image($meta);

        return $element;
    }


    private function getFilesystem()
    {
        /** @var FlysystemAdapterInterface $flysystem */
        $flysystem = $this->get('app.flysystem.dropbox');
        /** @var FilesystemInterface $filesystem */
        $filesystem = $flysystem->getFilesystem($this->getUser());

        return $filesystem;
    }


    private function isValidBase64($string)
    {
        if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string)) {
            return false;
        }

        $decoded = base64_decode($string, true);

        if (!$decoded) {
            return false;
        }

        if (base64_encode($decoded) != $string) {
            return false;
        }

        return true;
    }
}
