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
{// Route to view all bookings
    #[Route('/bookings', name: 'bookings')]
    public function index(BookingRepository $bookingRepository): Response
    {
        if (!$this->getUser()) {// check if user is logged in
            return $this->redirectToRoute('app_login');
        }

        return $this->render('booking/index.html.twig', [
            'bookings' => $bookingRepository->findBy([
                'traveler' => $this->getUser()
            ])// get all bookings for the current user
        ]);
    }

    // Route to make a booking
    #[Route('/book-a-room/{room}', name: 'book_room', methods: ['POST'])]
    public function bookRoom(
        Room $room, 
        Request $request,
        EntityManagerInterface $em
    ): Response
    {
        if (!$this->getUser()) {// check if user is logged in
            return $this->redirectToRoute('app_login');
        }

        $previous = $request->headers->get('referer');// get the previous page
        $user = $this->getUser();// get the current user
        
        $newBooking = new Booking();// create a new booking
        $newBooking->setNumber(uniqid())// set a unique booking number
                ->setTraveler($user)// set the user making the booking
                ->setRoom($room)// set the room to book
                ->setCheckIn(new \DateTime($request->request->get('checkin')))
                ->setCheckOut(new \DateTime($request->request->get('checkout')))
                ->setOccupants($request->request->get('guests'))// set the number of guests
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
