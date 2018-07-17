<?php

namespace ApiBundle\Service;

use ApiBundle\EnhancedFlysystemAdapter\EnhancedFilesystemInterface;
use ApiBundle\FilesystemAdapter\FilesystemAdapterManager;
use ApiBundle\Form\Type\TagType;
use ApiBundle\Model\Element;
use ApiBundle\Model\ElementFile;
use ApiBundle\Model\Tag;
use ApiBundle\Util\Base64;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ColllectionTagService
{
    /**
     * @var ColllectionElementService
     */
    private $colllectionElementService;

    /**
     * @var ColllectionTagFileService
     */
    private $colllectionTagFileService;

    /**
     * @var FormFactory
     */
    private $formFactory;


    public function __construct(
        ColllectionElementService $colllectionElementService,
        ColllectionTagFileService $colllectionTagFileService,
        FormFactory $formFactory
    )
    {
        $this->colllectionElementService = $colllectionElementService;
        $this->colllectionTagFileService = $colllectionTagFileService;
        $this->formFactory = $formFactory;
    }

    /**
     * Get an array of tags from a colllection
     *
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     * @return Tag[]
     */
    public function list(string $encodedColllectionPath): array
    {
        return $this->colllectionTagFileService->getAll($encodedColllectionPath);
    }

    /**
     * Add a tag to a colllection
     *
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     * @param Request $request
     * @return Tag|FormInterface
     */
    public function create(string $encodedColllectionPath, Request $request)
    {
        $tag = new Tag();
        $form = $this->formFactory->create(TagType::class, $tag);
        $requestContent = $request->request->all();
        $form->submit($requestContent, false);

        if (!$form->isValid()) {
            return $form;
        }

        $this->colllectionTagFileService->add($encodedColllectionPath, $tag);
        $this->colllectionTagFileService->save($encodedColllectionPath);

        return $tag;
    }

    /**
     * Get a tag from a colllection
     *
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     * @param string $encodedTagName Base 64 encoded tag name
     * @return Tag
     */
    public function get(string $encodedColllectionPath, string $encodedTagName): Tag
    {
        if (!Base64::isValidBase64($encodedTagName)) {
            throw new BadRequestHttpException('request.badly_encoded_tag_name');
        }
        $tagName = Base64::decode($encodedTagName);

        $tag = $this->colllectionTagFileService->get($encodedColllectionPath, $tagName);

        return $tag;
    }

    /**
     * Update a tag from a colllection
     *
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     * @param string $encodedTagName Base 64 encoded tag name
     * @param Request $request
     * @return Tag|FormInterface
     */
    public function update(string $encodedColllectionPath, string $encodedTagName, Request $request)
    {
        $tag = $this->get($encodedColllectionPath, $encodedTagName);
        $oldTag = clone $tag;

        $form = $this->formFactory->create(TagType::class, $tag);
        $requestContent = $request->request->all();
        $form->submit($requestContent, false);

        if (!$form->isValid()) {
            return $form;
        }

        // If tag has not changed, just return the old one
        if ($oldTag->getName() === $tag->getName()) {
            return $oldTag;
        }

        // Add the new tag (throws if tag name already exists)
        $this->colllectionTagFileService->add($encodedColllectionPath, $tag);

        // Rename all elements which has this tag
        $this->colllectionElementService->batchRename(
            $encodedColllectionPath,
            function (Element $element) use ($oldTag) {
                return in_array($oldTag->getName(), $element->getTags());
            },
            function (ElementFile $elementFile) use ($oldTag, $tag) {
                $elementFile
                    ->removeTag($oldTag->getName())
                    ->addTag($tag->getName());
            }
        );

        // Remove the old one and save the tag file
        $this->colllectionTagFileService->remove($encodedColllectionPath, $oldTag);
        $this->colllectionTagFileService->save($encodedColllectionPath);

        return $tag;
    }

    /**
     * Delete a tag from a colllection
     *
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     * @param string $encodedTagName Base 64 encoded tag name
     */
    public function delete(string $encodedColllectionPath, string $encodedTagName)
    {
        $tag = $this->get($encodedColllectionPath, $encodedTagName);

        // Add the new tag (throws if tag name already exists)
        $this->colllectionTagFileService->remove($encodedColllectionPath, $tag);

        // Rename all elements which has this tag
        $this->colllectionElementService->batchRename(
            $encodedColllectionPath,
            function (Element $element) use ($tag) {
                return in_array($tag->getName(), $element->getTags());
            },
            function (ElementFile $elementFile) use ($tag) {
                $elementFile->removeTag($tag->getName());
            }
        );

        // Save the tag file
        $this->colllectionTagFileService->save($encodedColllectionPath);
    }
}
