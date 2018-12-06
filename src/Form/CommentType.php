<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('content', TextareaType::class, [
            'label' => false,
              'help' => 'Коментара трябва да бъде поне 10 символа.'
          ])
          ->add('submit', SubmitType::class, [
            'label' => 'Добави',
            'attr' => ['class' => 'btn-primary'],
          ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
          'data_class' => Comment::class,
        ]);
    }
}
