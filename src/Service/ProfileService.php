<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;// import the ParameterBagInterface class

class ProfileService// Service to update user profile
{
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function updateProfile($form, $user, $em)// update user profile
    {// set user details(hydrate the user object with the form data)
        $user->setFirstname($form->get('firstname')->getData());
        $user->setLastname($form->get('lastname')->getData());
        $user->setBirthyear($form->get('birthyear')->getData());
        $user->setAddress($form->get('address')->getData());
        $user->setCity($form->get('city')->getData());
        $user->setCountry($form->get('country')->getData());
        $user->setJob($form->get('job')->getData());

        // Upload image
        if($form->get('image')->getData()) {
            $file = $form->get('image')->getData();
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->parameterBag->get('upload_dir_user'), $filename);
            $user->setImage($filename);
        } else {
            if($user->getImage() == null) {
                $user->setImage('default.png');
            } else {
                $user->setImage($user->getImage());
            }
        }

        // Define role
        if($form->get('roles')->getData() == 'host') {
            $user->setRoles(['ROLE_HOST']);
        } else {
            $user->setRoles(['ROLE_USER']);
        }
        
        $em->persist($user);// save the changes
        $em->flush();// commit the changes
    }
}