<?php

namespace App\Controller;

use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    #[Route('/bookings', name: 'bookings')]
    public function index(BookingRepository $bookingRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('booking/index.html.twig', [
            'bookings' => $bookingRepository->findBy([
                'traveler' => $this->getUser()
            ])
        ]);
    }
}
