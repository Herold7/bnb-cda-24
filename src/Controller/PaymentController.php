<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Booking;
use App\Entity\Invoice;
use Stripe\Checkout\Session;
use App\Repository\RoomRepository;
use App\Repository\BookingRepository;
use App\Repository\InvoiceRepository;
use App\Service\InvoiceService;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
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
                    'product_data' => ['name' => $request->request->get('title')],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('payment_success', [
                'number' => $request->request->get('number'),
            ], 0),
            'cancel_url' => $this->generateUrl('payment_cancel', [
                'number' => $request->request->get('number'),
            ], 0),
        ]);

        return $this->redirect($checkout_session->url, 303);
    }

    // Route de redirection en cas de paiement réussi
    #[Route('/payment/success', name: 'payment_success', methods: ['GET'])]
    public function paymentSuccess(
        Request $request,
        BookingRepository $bookingRepository,
        InvoiceRepository $invoiceRepository,
        EntityManagerInterface $em,
        NotificationService $notificationService,
        InvoiceService $invoiceService
    ): Response {
        /**
         * Le choix  entre [findOneBy, find] et [findBy, findAll] dépend de la situation
         * [findOneBy, find] : retourne un objet (ce qui permet de manipuler les méthodes de l'objet)
         * [findBy, findAll] : retourne un tableau (suffit pour un affichage de données)
         */
        $booking = $bookingRepository->findOneBy(['number' => $request->query->get('number')]);

        // Si la réservation n'existe pas
        if (!$booking) {
            return $this->render('payment/success.html.twig');
        } else {
            // On vérifie si la réservation est déjà payée
            if ($booking->isIsPaid() === true) {
                $invoice = $invoiceRepository->findOneBy(['booking' => $booking]);
            } else {
                $booking->setIsPaid(true);
                $em->persist($booking);
                $em->flush();

                // On notifie l'hôte de la réservation
                $notificationService->sendNewBooking($booking);
                // On crée la facture
                $invoiceService->createInvoice($booking);
                // On récupère la facture
                $invoice = $invoiceRepository->findOneBy(['booking' => $booking]);
            }

            return $this->render('payment/success.html.twig', [
                'booking' => $booking,
                'invoice' => $invoice,
            ]);
        }
    }

    // Route de redirection en cas d'annulation de paiement
    #[Route('/payment/cancel', name: 'payment_cancel', methods: ['GET'])]
    public function paymentCancel(): Response
    {
        return $this->render('payment/cancel.html.twig');
    }

    // Route pour la confirmation de réservation
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

            return $this->render('payment/confirmation.html.twig', [
                'confirmation' => $confirmation, // retourne l'objet booking à la vue
                'dateDiff' => $dateDiff, // retourne la durée du séjour à la vue
                'totalAmount' => $totalAmount // retourne le montant total à la vue
            ]);
        } else { // Si la méthode est GET
            return $this->render('payment/confirmation.html.twig'); // retourne la vue sans objet
        }
    }

    // Route pour accèder à la facture
    #[Route('/invoice/{slug}', name: 'invoice', methods: ['GET'])]
    public function invoice(
        Invoice $invoice,
    ): Response {
        
        if($invoice->getBooking()->getTraveler() !== $this->getUser()) {
            return $this->redirectToRoute('account');
        } else {
        return $this->render('payment/invoice.html.twig', [
            'invoice' => $invoice,
        ]);
        }
    }
}
