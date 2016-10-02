<?php

namespace AppBundle\Controller;

use AppBundle\FlysystemAdapter\FlysystemAdapterInterface;
use AppBundle\Model\Element;
use AppBundle\Model\Image;
use League\Flysystem\FilesystemInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
            if (Element::isValidElement($meta['path'])) {
                $element = new Image($meta);
                $files[] = $element;
            }
        }

        return $files;
    }


    /**
     * Get an Inbox element
     *
     * @Rest\Route("/inbox/elements/{elementName}")
     * @Rest\View()
     *
     * @ApiDoc(
     *     section="Inbox Elements",
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

        if (!Element::isValidElement($basename)) {
            throw new BadRequestHttpException("request.unsupported_element_type");
        }

        $filesystem = $this->getFilesystem();

        $path = self::INBOX_FOLDER . '/' . $basename;

        $meta = $filesystem->getMetadata($path);
        $element = new Image($meta);

        return $element;
    }


    /**
     * Create an Inbox element
     *
     * @Rest\Route("/inbox/elements/{elementName}")
     * @Rest\View()
     *
     * @ApiDoc(
     *     section="Inbox Elements",
     *     statusCodes={
     *         200="Returned when Inbox file is found",
     *         404="Returned when Inbox file is not found"
     *     },
     *     responseMap={
     *         200=AppBundle\Model\Element::class
     *     }
     * )
     *
     * @param $elementName
     * @return string
     */
    public function postInboxElementAction($elementName)
    {
        if (!$this->isValidBase64($elementName)) {
            throw new BadRequestHttpException("request.invalid_element_name");
        }

        $filename = base64_decode($elementName);
        $filesystem = $this->getFilesystem();

        $path = self::INBOX_FOLDER . '/' . $filename;

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
