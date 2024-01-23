<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('roles')
            ->add('firstname')
            ->add('lastname')
            ->add('birthyear')
            ->add('address')
            ->add('city')
            ->add('country')
            // ->add('image', FileType::class, [
            //     'label' => 'Profile picture',
            //     'mapped' => false,
            //     'required' => true,
            //     'attr' => [
            //         'class' => 'form-control-file'
            //     ],
            //     'constraints' => [
            //         new File([
            //             'maxSize' => '1024k',
            //             'mimeTypes' => [
            //                 'image/*',
            //             ],
            //             'mimeTypesMessage' => 'Please upload a valid image',
            //         ])
            //     ],
            // ])
            ->add('job')
            ->add('submit', SubmitType::class, [
                'label' => 'Save',
                'attr' => [
                    'class' => 'btn btn-custom btn-block'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
