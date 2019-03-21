<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\ElementFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ElementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, ['required' => false])
            ->add('url', UrlType::class, ['required' => false])
            ->add('type', TextType::class, ['required' => false])
            ->add('name', TextType::class, ['required' => false])
            ->add('tags', CollectionType::class, ['required' => false, 'allow_add' => true, 'allow_delete' => true])
            ->add('content', TextType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => ElementFile::class,
            ]
        );
    }
}
