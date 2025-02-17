<?php

namespace App\Form;

use App\Entity\Film;
use App\Entity\Genre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints as Assert;

class FilmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Title is required.'])
                ]
            ])
            ->add('description', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Description is required.'])
                ]
            ])
            ->add('releaseDate', null, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Release date is required.'])
                ]
            ])
            ->add('imagePath')
            ->add('genre', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'name',
                'placeholder' => 'Choose a genre',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Film::class,
            'csrf_protection' => true,
            'csrf_token_id' => 'film_form',
        ]);
    }
}
