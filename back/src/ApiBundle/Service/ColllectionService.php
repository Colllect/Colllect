<?php

namespace ApiBundle\Service;

use ApiBundle\EnhancedFlysystemAdapter\EnhancedFilesystemInterface;
use ApiBundle\FilesystemAdapter\FilesystemAdapterManager;
use ApiBundle\Form\Type\ColllectionType;
use ApiBundle\Model\Colllection;
use ApiBundle\Util\ColllectionPath;
use ApiBundle\Util\Metadata;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Stopwatch\Stopwatch;

class ColllectionService
{
    /**
     * @var EnhancedFilesystemInterface
     */
    private $filesystem;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var Stopwatch
     */
    private $stopwatch;


    public function __construct(TokenStorage $tokenStorage, FilesystemAdapterManager $flysystemAdapters, FormFactory $formFactory, Stopwatch $stopwatch = null)
    {
        $user = $tokenStorage->getToken()->getUser();

        $this->filesystem = $flysystemAdapters->getFilesystem($user);
        $this->formFactory = $formFactory;
        $this->stopwatch = $stopwatch;
    }

    /**
     * Get an array of all user Colllections
     *
     * @return Colllection[]
     */
    public function list(): array
    {
        if ($this->stopwatch) {
            $this->stopwatch->start('colllection_list');
        }

        try {
            $colllectionsMetadata = $this->filesystem->listContents(ColllectionPath::COLLLECTIONS_FOLDER);
        } catch (\Exception $e) {
            // We can't catch 'not found'-like exception for each adapter,
            // so we normalize the result
            if ($this->stopwatch) {
                $this->stopwatch->stop('colllection_list');
            }

            return [];
        }

        if (count($colllectionsMetadata) === 0) {
            if ($this->stopwatch) {
                $this->stopwatch->stop('colllection_list');
            }

            return [];
        }

        // Get typed colllection for each folder
        $colllections = [];
        foreach ($colllectionsMetadata as $colllectionMetadata) {
            if ($colllectionMetadata['type'] !== 'dir') {
                continue;
            }

            $colllectionMetadata = Metadata::standardize($colllectionMetadata);
            $colllections[] = new Colllection($colllectionMetadata);
        }

        // Sort colllections by name
        uasort(
            $colllections,
            function (Colllection $a, Colllection $b) {
                return $a->getName() < $b->getName() ? -1 : 1;
            }
        );

        $colllectionList = array_values($colllections);

        if ($this->stopwatch) {
            $this->stopwatch->stop('colllection_list');
        }

        return $colllectionList;
    }

    /**
     * Create a colllection
     *
     * @param Request $request
     * @return Colllection|FormInterface
     */
    public function create(Request $request)
    {
        if ($this->stopwatch) {
            $this->stopwatch->start('colllection_create');
        }

        $colllection = new Colllection();
        $form = $this->formFactory->create(ColllectionType::class, $colllection);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            if ($this->stopwatch) {
                $this->stopwatch->stop('colllection_create');
            }

            return $form;
        }

        $path = ColllectionPath::COLLLECTIONS_FOLDER . '/' . $colllection->getName();
        $this->filesystem->createDir($path);

        if ($this->stopwatch) {
            $this->stopwatch->stop('colllection_create');
        }

        return $colllection;
    }

    /**
     * Get a colllection by path
     *
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     * @return Colllection
     * @throws FileNotFoundException
     */
    public function get(string $encodedColllectionPath): Colllection
    {
        if ($this->stopwatch) {
            $this->stopwatch->start('colllection_get');
        }

        $colllectionPath = ColllectionPath::decode($encodedColllectionPath);

        $meta = $this->filesystem->getMetadata($colllectionPath);

        if (!$meta) {
            if ($this->stopwatch) {
                $this->stopwatch->stop('colllection_get');
            }

            throw new NotFoundHttpException('error.colllection_not_found');
        }

        $standardizedMeta = Metadata::standardize($meta, $colllectionPath);

        $colllection = new Colllection($standardizedMeta);

        if ($this->stopwatch) {
            $this->stopwatch->stop('colllection_get');
        }

        return $colllection;
    }

    /**
     * Update a colllection by path
     *
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     * @param Request $request
     * @return Colllection|FormInterface
     * @throws FileNotFoundException
     */
    public function update(string $encodedColllectionPath, Request $request)
    {
        if ($this->stopwatch) {
            $this->stopwatch->start('colllection_update');
        }

        $colllection = $this->get($encodedColllectionPath);

        $renamedColllection = new Colllection();
        $form = $this->formFactory->create(ColllectionType::class, $renamedColllection);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            if ($this->stopwatch) {
                $this->stopwatch->stop('colllection_update');
            }

            return $form;
        }

        $path = ColllectionPath::COLLLECTIONS_FOLDER . '/' . $colllection->getName();
        $renamedPath = ColllectionPath::COLLLECTIONS_FOLDER . '/' . $renamedColllection->getName();
        $this->filesystem->renameDir($path, $renamedPath);

        if ($this->stopwatch) {
            $this->stopwatch->stop('colllection_update');
        }

        return $renamedColllection;
    }

    /**
     * Delete a colllection based on base 64 encoded basename
     *
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     */
    public function delete(string $encodedColllectionPath)
    {
        if ($this->stopwatch) {
            $this->stopwatch->start('colllection_delete');
        }

        $colllectionPath = ColllectionPath::decode($encodedColllectionPath);

        try {
            $this->filesystem->deleteDir($colllectionPath);
        } catch (FileNotFoundException $e) {
            // If file does not exists or was already deleted, it's OK
        }

        if ($this->stopwatch) {
            $this->stopwatch->stop('colllection_delete');
        }
    }
}
