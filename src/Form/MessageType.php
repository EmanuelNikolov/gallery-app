<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('name', TextType::class, [
            'label' => 'Вашето Име',
          ])
          ->add('email', EmailType::class, [
            'label' => 'Вашият Имейл',
          ])
          ->add('content', TextareaType::class, [
            'label' => 'Съобщение',
            'help' => 'Съобщението трябва да бъде поне 35 символа.',
          ])
          ->add('submit', SubmitType::class, [
            'label' => 'Изпрати',
            'attr' => ['class' => 'btn-primary'],
          ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
          'data_class' => Message::class,
        ]);
    }
}
