<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\Booking;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{// Route pour afficher les réservations
    #[Route('/bookings', name: 'bookings')]
    public function index(BookingRepository $bookingRepository): Response
    {
        if (!$this->getUser()) {// vérifier si l'utilisateur est connecté
            return $this->redirectToRoute('app_login');
        }

        return $this->render('booking/index.html.twig', [
            'bookings' => $bookingRepository->findBy([
                'traveler' => $this->getUser()
            ])// récupérer les réservations de l'utilisateur connecté
        ]);
    }

    // Route afin de réserver une chambre
    #[Route('/book-a-room/{room}', name: 'book_room', methods: ['POST'])]
    public function bookRoom(
        Room $room, 
        Request $request,
        EntityManagerInterface $em
    ): Response
    {
        if (!$this->getUser()) {// vérifier si l'utilisateur est connecté
            return $this->redirectToRoute('app_login');
        }

        $previous = $request->headers->get('referer');// récupérer la page précédente
        $user = $this->getUser();// obtenir l'utilisateur connecté
        
        $newBooking = new Booking();// créer une nouvelle réservation
        $newBooking->setNumber(uniqid())// créer un numéro de réservation unique
                ->setTraveler($user)// ajouter l'utilisateur connecté à la réservation
                ->setRoom($room)// ajouter la chambre à la réservation
                ->setCheckIn(new \DateTime($request->request->get('checkin')))
                ->setCheckOut(new \DateTime($request->request->get('checkout')))
                ->setOccupants($request->request->get('guests'))// indiquer le nombre d'occupants
                ->setCreatedAt(new \DateTime('now'))
                ;

        $user->User->addBooking($newBooking);// add the booking to the user's bookings
        $em->persist($newBooking);// save the booking
        $em->flush();// commit the changes

        $this->addFlash('success', 'Room booked successfully.');
        return $this->redirect($previous);
    }
    // Route to view a booking
    #[Route('/bookings/{booking}', name: 'booking_show', methods: ['GET'])]
    public function show(Booking $booking): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
        ]);
    }

}
