<?php

namespace App\Tests;

use App\Repository\BookingRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConfirmationTest extends WebTestCase
{
    public function testBookRoomWhenLoggedIn(): void
    {
        // Kernel éteint et client HTTP créé
        self::ensureKernelShutdown();
        $client = static::createClient();

        // Récupération de l'utilisateur traveler et on le connecte
        $userRepository = static::getContainer()->get(UserRepository::class);
        $traveler = $userRepository->findOneBy(['email' => 'user72@user.fr']);
        $client->loginUser($traveler);

        // Envoi de la requête POST pour créer une réservation
        $client->request('POST', '/confirmation', [
            'room' => 1,
            'checkin' => '2024-04-01',
            'checkout' => '2024-04-02',
            'guests' => 1
        ]);

        // Récupération de la dernière réservation
        $booking = static::getContainer()
            ->get(BookingRepository::class)
            ->findOneBy([], ['id' => 'DESC']);

        // Assertions
        $this->assertEquals(
            $traveler->getEmail(), 
            $booking->getTraveler()->getEmail()
        );
    }
}
