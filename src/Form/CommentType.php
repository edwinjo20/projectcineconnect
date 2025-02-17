<?php
namespace App\Form;

use App\Entity\Comment;
use App\Entity\Review;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content')  // The content field remains for user input
            ->add('review', EntityType::class, [
                'class' => Review::class,
                'choice_label' => 'id',
                'disabled' => true,  // No need for user to select a review; it will be set in the controller
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'disabled' => true,  // No need for user to select themselves; it will be set automatically
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
