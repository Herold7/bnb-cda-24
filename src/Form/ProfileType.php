<?php

namespace App\Form;

use App\Entity\User;// importer la classe User
use Symfony\Component\Form\AbstractType;// importer la classe AbstractType qui permet de définir le formulaire par héritage
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;// importer la classe ChoiceType qui permet de gérer les choix
use Symfony\Component\Form\FormBuilderInterface;// importer la classe FormBuilderInterface qui permet de construire le formulaire
use Symfony\Component\Validator\Constraints\File;// importer la classe File qui permet de gérer les fichiers par contraintes
use Symfony\Component\OptionsResolver\OptionsResolver;// importer la classe OptionsResolver qui permet de définir les options du formulaire
use Symfony\Component\Form\Extension\Core\Type\FileType;// importer la classe FileType qui permet de gérer les fichiers
use Symfony\Component\Form\Extension\Core\Type\TextType;// importer la classe TextType qui permet de gérer les champs de texte
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void// construire le formulaire de profil utilisateur
    {
        $builder// ajouter les champs du formulaire
            ->add('roles', ChoiceType::class, [// champ pour définir le rôle de l'utilisateur
                'label' => 'I am a:',
                'mapped' => false,
                'choices' => [
                    'Traveler' => 'user',
                    'Host' => 'host',
                    'Both' => 'host'
                ],
                'attr' => [
                    'class' => 'form-control  mb-2'
                ]
            ])
            ->add('firstname', TextType::class, [// champ pour le prénom de l'utilisateur
                'label' => 'First Name',
                'attr' => [
                    'class' => 'form-control  mb-2'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Last Name',
                'attr' => [
                    'class' => 'form-control  mb-2'
                ]
            ])
            ->add('birthyear', NumberType::class, [
                'label' => 'Your birth Year',
                'attr' => [
                    'class' => 'form-control  mb-2'
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Your billing address',
                'attr' => [
                    'class' => 'form-control  mb-2'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Your city',
                'attr' => [
                    'class' => 'form-control  mb-2'
                ]
            ])
            ->add('country', TextType::class, [
                'label' => 'Your country',
                'attr' => [
                    'class' => 'form-control  mb-2'
                ]
            ])
            ->add('job', TextType::class, [
                'label' => 'Your job',
                'attr' => [
                    'class' => 'form-control  mb-2'
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Your profile picture',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control-file mb-2'
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'maxSizeMessage' => 'The file is too large ({{ size }} {{ suffix }}). Allowed maximum size is {{ limit }} {{ suffix }}.',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ],
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void// définir les options du formulaire
    {
        $resolver->setDefaults([
            'data_class' => User::class,// définir la classe de données du formulaire
        ]);
    }
}
