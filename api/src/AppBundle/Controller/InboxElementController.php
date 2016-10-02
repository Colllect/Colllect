<?php

namespace AppBundle\Controller;

use AppBundle\FlysystemAdapter\FlysystemAdapterInterface;
use AppBundle\Model\Image;
use League\Flysystem\FilesystemInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;

class InboxElementController extends FOSRestController
{
    /**
     * List all Inbox elements
     *
     * @Rest\View()
     *
     * @ApiDoc(
     *     section="Inbox Elements",
     *     statusCodes={
     *         201="Returned when user was created",
     *         400="Returned when parameters are invalids"
     *     },
     *     responseMap={
     *         200=User::class
     *     }
     * )
     */
    public function getInboxElementsAction()
    {
        /** @var FlysystemAdapterInterface $flysystem */
        $flysystem = $this->get('app.flysystem.dropbox');
        /** @var FilesystemInterface $filesystem */
        $filesystem = $flysystem->getFilesystem($this->getUser());

        $metas = $filesystem->listContents('Inbox');

        $files = [];
        foreach ($metas as $meta) {
            $files[] = new Image($meta);
        }

        return $files;
    }
}
