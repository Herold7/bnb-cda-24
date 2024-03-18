<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\Invoice;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * InvoiceService
 * Service pour pour la gestion des factures
 * @method createInvoice(Booking $booking): void
 * @method downloadInvoice(Booking $booking): Invoice
 */
class InvoiceService
{
    private InvoiceRepository $invoiceRepository;
    private EntityManagerInterface $em;

    /**
     * Le constructeur permet d'initaliser la classe avec les dépendances nécessaires
     * ce qui la rend disponible dans toutes méthodes de InvoiceService
     */
    public function __construct(
        InvoiceRepository $invoiceRepository,
        EntityManagerInterface $em
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->em = $em;
    }

    // Vérication du dernier numéro de facture pour incrémenter
    public function getLastInvoiceNumber(): string
    {
        // Récupère la dernière facture
        $lastInvoice = $this->invoiceRepository->findOneBy([], ['id' => 'DESC']);
        // Si il n'y a pas de facture, on retourne 0001
        if (!$lastInvoice) {
            return '0001';
        }
        // Sinon on récupère le numéro de la dèrnière facture
        $lastNumber = $lastInvoice->getNumber();
        // Puis on incrémente le numéro de facture
        $number = intval($lastNumber) + 1;
        // On retourne le numéro de facture avec un total de 4 chiffres maximum
        return str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    // Création de la facture
    public function createInvoice(Booking $booking): Invoice
    {
        $invoice = new Invoice();
        $invoice->setNumber($this->getLastInvoiceNumber())
            ->setBooking($booking)
            ->setAddress($booking->getTraveler()->getFullAddress());

        $this->em->persist($invoice);
        $this->em->flush();

        return $invoice;
    }

    // Récupération de la facture


}
