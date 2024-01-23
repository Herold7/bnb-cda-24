<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Repository\UserRepository;
use App\Service\ProfileUpdate;
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
        ProfileUpdate $profileUpdate,
        EntityManagerInterface $em
    ): Response
    {
        $form = $this->createForm(ProfileType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $profileUpdate->updateProfile($form, $this->getUser(), $em);
            
            $this->addFlash('success', 'Your profile has been updated');
            return $this->redirectToRoute('account');
        }
        return $this->render('user/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
