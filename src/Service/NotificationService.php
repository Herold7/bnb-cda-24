<?php

namespace App\Service;

/**
 * NotificationService
 * Service pour envoyer des notifications par email pour les réservations
 * @method sendNewBooking(Booking $booking): void
 */

use App\Entity\Booking;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class NotificationService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    // Email pour informer l'hôte d'une nouvelle réservation
    public function sendNewBooking(Booking $booking): void
    {
        $title = $booking->getRoom()->getTitle();
        $city = $booking->getRoom()->getCity();
        $occupants = $booking->getOccupants();
        $checkIn = $booking->getCheckIn();
        $checkOut = $booking->getCheckOut();

        $email = (new TemplatedEmail())
            ->from('contact@bnb.fr')
            ->to($booking->getRoom()->getHost()->getEmail())
            ->priority(Email::PRIORITY_HIGH)
            ->subject('New booking : ' . $booking->getNumber())
            ->htmlTemplate('emails/new-booking.html.twig');
        $this->mailer->send($email);
    }






    // Email pour la confirmation de réservation par l'hôte


    // Email pour la confirmation de réservation

    // Email pour la confirmation de paiement
}
