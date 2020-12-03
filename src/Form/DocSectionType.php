<?php

namespace App\Form;

use App\Entity\DocSection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocSectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre'
            ])
            ->add('requestMethod', TextType::class, [
                'label' => 'Methode de la requète'
            ])
            ->add('requestUrl', TextType::class, [
                'label' => 'Url de la requète'
            ])
            ->add('contentType', TextType::class, [
                'label' => 'Type de contenu'
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DocSection::class,
        ]);
    }
}
