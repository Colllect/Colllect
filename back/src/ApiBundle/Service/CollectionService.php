<?php

namespace ApiBundle\Service;

use ApiBundle\EnhancedFlysystemAdapter\EnhancedFilesystemInterface;
use ApiBundle\FilesystemAdapter\FilesystemAdapterManager;
use ApiBundle\Form\Type\CollectionType;
use ApiBundle\Model\Collection;
use ApiBundle\Util\CollectionPath;
use ApiBundle\Util\Metadata;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CollectionService
{
    /**
     * @var EnhancedFilesystemInterface
     */
    private $filesystem;

    /**
     * @var FormFactory
     */
    private $formFactory;


    public function __construct(TokenStorage $tokenStorage, FilesystemAdapterManager $flysystemAdapters, FormFactory $formFactory)
    {
        $user = $tokenStorage->getToken()->getUser();

        $this->filesystem = $flysystemAdapters->getFilesystem($user);
        $this->formFactory = $formFactory;
    }

    /**
     * Get an array of all user Collections
     *
     * @return Collection[]
     */
    public function list(): array
    {
        try {
            $collectionsMetadata = $this->filesystem->listContents(CollectionPath::COLLECTIONS_FOLDER);
        } catch (\Exception $e) {
            // We can't catch 'not found'-like exception for each adapter,
            // so we normalize the result
            return [];
        }

        if (count($collectionsMetadata) === 0) {
            return [];
        }

        // Get typed collection for each folder
        $collections = [];
        foreach ($collectionsMetadata as $collectionMetadata) {
            if ($collectionMetadata['type'] !== 'dir') {
                continue;
            }

            $collectionMetadata = Metadata::standardize($collectionMetadata);
            $collections[] = new Collection($collectionMetadata);
        }

        // Sort collections by name
        uasort(
            $collections,
            function (Collection $a, Collection $b) {
                return $a->getName() < $b->getName() ? -1 : 1;
            }
        );

        return array_values($collections);
    }

    /**
     * Create a collection
     *
     * @param Request $request
     * @return Collection|FormInterface
     */
    public function create(Request $request)
    {
        $collection = new Collection();
        $form = $this->formFactory->create(CollectionType::class, $collection);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }

        $path = CollectionPath::COLLECTIONS_FOLDER . '/' . $collection->getName();
        $this->filesystem->createDir($path);

        return $collection;
    }

    /**
     * Get a collection by path
     *
     * @param string $encodedCollectionPath Base 64 encoded collection path
     * @return Collection
     * @throws FileNotFoundException
     */
    public function get(string $encodedCollectionPath): Collection
    {
        $collectionPath = CollectionPath::decode($encodedCollectionPath);

        $meta = $this->filesystem->getMetadata($collectionPath);

        if (!$meta) {
            throw new NotFoundHttpException('error.collection_not_found');
        }

        $standardizedMeta = Metadata::standardize($meta, $collectionPath);

        $collection = new Collection($standardizedMeta);

        return $collection;
    }

    /**
     * Update a collection by path
     *
     * @param string $encodedCollectionPath Base 64 encoded collection path
     * @param Request $request
     * @return Collection|FormInterface
     * @throws FileNotFoundException
     */
    public function update(string $encodedCollectionPath, Request $request)
    {
        $collection = $this->get($encodedCollectionPath);

        $renamedCollection = new Collection();
        $form = $this->formFactory->create(CollectionType::class, $renamedCollection);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }

        $path = CollectionPath::COLLECTIONS_FOLDER . '/' . $collection->getName();
        $renamedPath = CollectionPath::COLLECTIONS_FOLDER . '/' . $renamedCollection->getName();
        $this->filesystem->renameDir($path, $renamedPath);

        return $renamedCollection;
    }

    /**
     * Delete a collection based on base 64 encoded basename
     *
     * @param string $encodedCollectionPath Base 64 encoded collection path
     */
    public function delete(string $encodedCollectionPath)
    {
        $collectionPath = CollectionPath::decode($encodedCollectionPath);

        try {
            $this->filesystem->deleteDir($collectionPath);
        } catch (FileNotFoundException $e) {
            // If file does not exists or was already deleted, it's OK
        }
    }
}
