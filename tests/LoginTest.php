<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    /**
     * Ce test nous montre comment mettre en place la vérification du statut de la réponse
     * suite à une requête HTTP. Ici, on vérifie que le statut de la réponse est bien 200.
     * Cependant, cette route étant protégée par un firewall, on doit se connecter avant
     * de se rendre sur cette page. Pour cela, on utilise fait appel au repository user
     * puis on le connecte avec la méthode loginUser() du client HTTP de WebTestCase
     */
    public function testAccountRouteWhenLoggedIn(): void
    {
        self::ensureKernelShutdown(); // On s'assure que le kernel est bien éteint
        $client = static::createClient(); // On crée un client HTTP pour faire des requêtes
        $userRepository = static::getContainer()->get(UserRepository::class); // On récupère le repository user
        $admin = $userRepository->findOneBy(['email' => 'admin@admin.fr']); // On récupère l'utilisateur admin
        $client->loginUser($admin); // On connecte l'utilisateur admin
        $client->request('GET', '/account'); // On fait une requête GET sur la route /account/
        $this->assertResponseIsSuccessful(); // On vérifie qu'il y a une réponse
        $this->assertResponseStatusCodeSame(200); // On vérifie que le statut de la réponse est bien 200
        $this->assertSelectorTextContains('h1', $admin->getFullname()); // On vérifie le nom de l'utilisateur
        $this->assertSelectorTextContains('p', $admin->getEmail()); // On vérifie l'email de l'utilisateur
    }
}
