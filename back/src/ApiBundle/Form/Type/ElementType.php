<?php

namespace ApiBundle\Form\Type;

use ApiBundle\Model\ElementFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ElementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, ['required' => false])
            ->add('url', UrlType::class, ['required' => false])
            ->add('type', TextType::class, ['required' => false])
            ->add('basename', TextType::class, ['required' => false])
            ->add('content', TextType::class, ['required' => false])
            ->add('encodedCollectionPath', TextType::class, ['mapped' => false])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ElementFile::class
        ));
    }
}
