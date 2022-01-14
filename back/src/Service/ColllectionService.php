<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Form\ColllectionType;
use App\Model\Colllection;
use App\Service\FilesystemAdapter\EnhancedFilesystem\EnhancedFilesystemInterface;
use App\Service\FilesystemAdapter\FilesystemAdapterManager;
use App\Util\ColllectionPath;
use App\Util\Metadata;
use Exception;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Stopwatch\Stopwatch;

class ColllectionService
{
    private readonly EnhancedFilesystemInterface $filesystem;

    /**
     * ColllectionService constructor.
     *
     * @throws Exception
     */
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly ?Stopwatch $stopwatch,
        FilesystemAdapterManager $flysystemAdapters,
        Security $security,
    ) {
        $user = $security->getUser();

        if (!$user instanceof User) {
            throw new Exception('$user must be instance of ' . User::class);
        }

        $this->filesystem = $flysystemAdapters->getFilesystem($user);
    }

    /**
     * Get an array of all user Colllections.
     *
     * @return Colllection[]
     */
    public function list(): array
    {
        $this->stopwatch?->start('colllection_list');

        try {
            $colllectionsMetadata = $this->filesystem->listContents(ColllectionPath::COLLLECTIONS_FOLDER)
                ->filter(fn (StorageAttributes $attributes): bool => $attributes->isDir())
                ->sortByPath()
                ->toArray()
            ;
        } catch (FilesystemException) {
            // We can't catch 'not found'-like exception for each adapter,
            // so we normalize the result
            $this->stopwatch?->stop('colllection_list');

            return [];
        }

        if ($colllectionsMetadata === []) {
            $this->stopwatch?->stop('colllection_list');

            return [];
        }

        // Get typed colllection for each folder
        $colllections = [];
        foreach ($colllectionsMetadata as $colllectionMetadata) {
            $colllectionMetadata = Metadata::standardize([
                'path' => $colllectionMetadata->path(),
                'type' => $colllectionMetadata->type(),
                'timestamp' => $colllectionMetadata->lastModified(),
            ]);
            $colllections[] = new Colllection($colllectionMetadata);
        }

        $colllectionList = array_values($colllections);

        $this->stopwatch?->stop('colllection_list');

        return $colllectionList;
    }

    /**
     * Create a colllection.
     *
     * @throws FilesystemException
     */
    public function create(Request $request): Colllection|FormInterface
    {
        $this->stopwatch?->start('colllection_create');

        $colllection = new Colllection();
        $form = $this->formFactory->create(ColllectionType::class, $colllection);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            $this->stopwatch?->stop('colllection_create');

            return $form;
        }

        $path = ColllectionPath::COLLLECTIONS_FOLDER . '/' . $colllection->getName();
        $this->filesystem->createDirectory($path);

        $this->stopwatch?->stop('colllection_create');

        return $colllection;
    }

    /**
     * Update a colllection by path.
     *
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     */
    public function update(string $encodedColllectionPath, Request $request): Colllection|FormInterface
    {
        $this->stopwatch?->start('colllection_update');

        $colllection = $this->get($encodedColllectionPath);

        $renamedColllection = new Colllection();
        $form = $this->formFactory->create(ColllectionType::class, $renamedColllection);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            $this->stopwatch?->stop('colllection_update');

            return $form;
        }

        $path = ColllectionPath::COLLLECTIONS_FOLDER . '/' . $colllection->getName();
        $renamedPath = ColllectionPath::COLLLECTIONS_FOLDER . '/' . $renamedColllection->getName();
        $this->filesystem->renameDir($path, $renamedPath);

        $this->stopwatch?->stop('colllection_update');

        return $renamedColllection;
    }

    /**
     * Get a colllection by path.
     *
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     */
    public function get(string $encodedColllectionPath): Colllection
    {
        $this->stopwatch?->start('colllection_get');

        $colllectionPath = ColllectionPath::decode($encodedColllectionPath);

        // FIXME: check if Colllection actually exists
//        if (!$this->filesystem->fileExists($colllectionPath)) {
//            $this->stopwatch?->stop('colllection_get');
//
//            throw new NotFoundHttpException('error.colllection_not_found');
//        }

        $meta = [
            'path' => $colllectionPath,
            'type' => 'dir',
        ];

        $standardizedMeta = Metadata::standardize($meta, $colllectionPath);

        $colllection = new Colllection($standardizedMeta);

        $this->stopwatch?->stop('colllection_get');

        return $colllection;
    }

    /**
     * Delete a colllection based on base 64 encoded basename.
     *
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     *
     * @throws FilesystemException
     */
    public function delete(string $encodedColllectionPath): void
    {
        $this->stopwatch?->start('colllection_delete');

        $colllectionPath = ColllectionPath::decode($encodedColllectionPath);

        $this->filesystem->deleteDirectory($colllectionPath);

        $this->stopwatch?->stop('colllection_delete');
    }
}
