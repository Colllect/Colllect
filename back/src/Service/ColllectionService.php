<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedFilesystemInterface;
use App\Entity\User;
use App\Service\FilesystemAdapter\FilesystemAdapterManager;
use App\Form\ColllectionType;
use App\Model\Colllection;
use App\Util\ColllectionPath;
use App\Util\Metadata;
use Exception;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Stopwatch\Stopwatch;

class ColllectionService
{
    /**
     * @var EnhancedFilesystemInterface
     */
    private $filesystem;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var Stopwatch
     */
    private $stopwatch;

    /**
     * ColllectionService constructor.
     *
     * @throws Exception
     */
    public function __construct(
        Security $security,
        FilesystemAdapterManager $flysystemAdapters,
        FormFactoryInterface $formFactory,
        Stopwatch $stopwatch = null
    ) {
        $user = $security->getUser();

        if (!$user instanceof User) {
            throw new Exception('$user must be instance of ' . User::class);
        }

        $this->filesystem = $flysystemAdapters->getFilesystem($user);
        $this->formFactory = $formFactory;
        $this->stopwatch = $stopwatch;
    }

    /**
     * Get an array of all user Colllections.
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
        } catch (Exception $e) {
            // We can't catch 'not found'-like exception for each adapter,
            // so we normalize the result
            if ($this->stopwatch) {
                $this->stopwatch->stop('colllection_list');
            }

            return [];
        }

        if (\count($colllectionsMetadata) === 0) {
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
     * Create a colllection.
     *
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
     * Update a colllection by path.
     *
     * @param string  $encodedColllectionPath Base 64 encoded colllection path
     * @param Request $request
     *
     * @return Colllection|FormInterface
     *
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
     * Get a colllection by path.
     *
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     *
     * @return Colllection
     *
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
     * Delete a colllection based on base 64 encoded basename.
     *
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     */
    public function delete(string $encodedColllectionPath): void
    {
        if ($this->stopwatch) {
            $this->stopwatch->start('colllection_delete');
        }

        $colllectionPath = ColllectionPath::decode($encodedColllectionPath);

        $this->filesystem->deleteDir($colllectionPath);

        if ($this->stopwatch) {
            $this->stopwatch->stop('colllection_delete');
        }
    }
}
