<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('username', TextType::class, [
            'label' => 'Потребителско Име',
              'help' => 'Потребителското име трябва да е между 5 и 25 символа.',
          ])
          ->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Паролите трябва да съвпадат.',
            'first_options' => [
              'label' => 'Парола',
              'help' => 'Паролата трябва да бъде поне 8 символа.',
            ],
            'second_options' => [
              'label' => 'Повторете Паролата',
              'help' => 'Паролите трябва да съвпадат.',
            ],
            'required' => false,
          ])
          ->add('register', SubmitType::class, [
            'label' => 'Регистрация',
            'attr' => ['class' => 'btn-primary'],
          ]);

        $builder->addEventListener(
          FormEvents::PRE_SET_DATA,
          function (FormEvent $event) {
              $user = $event->getData();
              $form = $event->getForm();

              if (null !== $user->getId()) {
                  $form->remove('register');
                  $form->add('submit', SubmitType::class, [
                    'label' => 'Редактиране',
                    'attr' => ['class' => 'btn-primary'],
                  ]);
              }
          });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
          'data_class' => User::class,
          'validation_groups' => function (FormInterface $form) {
              $user = $form->getData();

              if (null === $user->getId()) {
                  return ['Default', 'edit'];
              }

              return ['edit'];
          },
        ]);
    }
}
