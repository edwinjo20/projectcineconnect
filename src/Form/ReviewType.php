<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Content field (textarea for the review)
            ->add('content', TextareaType::class, [
                'label' => 'Your Review',
                'attr' => ['rows' => 5], // Set the number of rows for the textarea
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your review.']),
                ],
            ])
            // Rating field (numeric input between 1 and 10)
            ->add('ratingGiven', NumberType::class, [
                'label' => 'Rating (1-10)',
                'attr' => ['min' => 1, 'max' => 10], // Set the minimum and maximum value for rating
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a rating.']),
                    new Range([
                        'min' => 1,
                        'max' => 10,
                        'notInRangeMessage' => 'Rating must be between {{ min }} and {{ max }}.',
                    ]),
                ],
            // Submit button
            ]);
    }

    // Configure options for the form
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class, // The entity to bind the form data to
        ]);
    }
}
