<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/complete-profile', name: 'complete_profile')]
    public function index(
        Request $request,
        EntityManagerInterface $em
    ): Response
    {
        $form = $this->createForm(ProfileType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $user->setFirstname($form->get('firstname')->getData());
            $user->setLastname($form->get('lastname')->getData());
            $user->setBirthyear($form->get('birthyear')->getData());
            $user->setAddress($form->get('address')->getData());
            $user->setCity($form->get('city')->getData());
            $user->setCountry($form->get('country')->getData());
            $user->setJob($form->get('job')->getData());
            
            $em->persist($user);
            $em->flush();
            
            $this->addFlash('success', 'Your profile has been updated');
            return $this->redirectToRoute('account');
        }
        return $this->render('registration/profile.html.twig', [
            'form' => $form,
        ]);
    }
}
