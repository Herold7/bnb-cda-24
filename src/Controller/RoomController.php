<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Room;
use App\Entity\Booking;
use Stripe\Checkout\Session;
use App\Repository\RoomRepository;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/r')] // prefix all room routes with /r
class RoomController extends AbstractController
{
    #[Route('/', name: 'app_room', methods: ['GET'])]
    public function index(
        RoomRepository $roomRepository,
        PaginatorInterface $paginator,
        Request $request,
    ): Response {
        $pagination = $paginator->paginate(
            $roomRepository->findAll(), // All rooms
            $request->query->getInt('page', 1), // Check page number
            12 // Items per page
        );

        return $this->render('room/index.html.twig', [
            'rooms' => $pagination,
            'hostRooms' => $roomRepository->findBy(
                ['host' => $this->getUser()]
            )
        ]);
    }

    #[Route('/{city}', name: 'app_room_city', methods: ['GET'])]
    public function city(
        RoomRepository $roomRepository,
        PaginatorInterface $paginator,
        Request $request,
    ): Response {
        $pagination = $paginator->paginate(
            $roomRepository->findBy(['city' => $request->attributes->get('city')]),
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('room/index.html.twig', [
            'rooms' => $pagination,
            'hostRooms' => $roomRepository->findBy(
                ['host' => $this->getUser()]
            )
        ]);
    }

    #[Route('/details/{id}', name: 'room', methods: ['GET', 'POST'])]
    public function details(
        Room $room,
    ): Response {
        return $this->render('room/details.html.twig', [
            'room' => $room,
        ]);
    }

    #[Route('/confirmation', name: 'book_room', methods: ['GET', 'POST'])]
    public function bookRoom(
        Request $request,
        EntityManagerInterface $em,
        RoomRepository $roomRepository,
    ): Response {
        if ($request->isMethod('POST')) { // Si la méthode est POST
            $data = $request->request->all(); // Toutes les données du formulaire sont récupérées
            $confirmation = new Booking(); // Crée un nouvel objet booking
            $confirmation->setCheckIn(new \DateTime($data['checkin'])) // Objet datetime attendu
                ->setCheckOut(new \DateTime($data['checkout'])) // Objet datetime attendu
                ->setOccupants($data['guests']) // Nombre attendu
                ->setTraveler($this->getUser()) // Objet user attendu
                ->setRoom($roomRepository->find($data['room'])) // Objet room attendu
            ;
            $em->persist($confirmation); // Prépare l'insertion en BDD
            $em->flush(); // Exécute l'insertion en BDD

            $dateDiff = $confirmation->getCheckIn()->diff($confirmation->getCheckOut()); // Calcul de la durée du séjour
            $totalAmount = $dateDiff->days * $confirmation->getRoom()->getPrice(); // Calcul du montant total

            return $this->render('room/confirmation.html.twig', [
                'confirmation' => $confirmation, // retourne l'objet booking à la vue
                'dateDiff' => $dateDiff, // retourne la durée du séjour à la vue
                'totalAmount' => $totalAmount // retourne le montant total à la vue
            ]);
        } else { // Si la méthode est GET
            return $this->render('room/confirmation.html.twig'); // retourne la vue sans objet
        }
    }

    #[Route('/payment', name: 'payment', methods: ['POST'])]
    public function payment(
        Request $request,
        BookingRepository $bookingRepository,
    ) {
        $booking = $bookingRepository->find($request->request->get('number')); // on récupère la réservation
        Stripe::setApiKey($this->getParameter('STRIPE_SECRET_KEY')); // on récupère la clé secrète

        header('Content-Type: application/json');
        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => $request->request->get('total') * 100, // Stripe utilise des centimes
                        'product_data' => [ // Les informations du produit sont personnalisables
                            'name' => $request->request->get('title')],
                        ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $this->generateUrl('payment_success', [], 0),
                'cancel_url' => $this->generateUrl('payment_cancel', [], 0),
            ]);

        return $this->redirect($checkout_session->url, 303);
    }

    #[Route('/payment/success', name: 'payment_success', methods: ['GET'])]
    public function paymentSuccess(): Response
    {
        return $this->render('room/payment_success.html.twig');
    }

    #[Route('/payment/cancel', name: 'payment_cancel', methods: ['GET'])]
    public function paymentCancel(): Response
    {
        return $this->render('room/payment_cancel.html.twig');
    }
}
