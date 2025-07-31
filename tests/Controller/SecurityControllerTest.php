<?php
// tests/Controller/SecurityControllerTest.php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests fonctionnels pour le contrôleur de sécurité (login/logout).
 * @covers \App\Controller\SecurityController
 */
class SecurityControllerTest extends WebTestCase
{
    /**
     * Vérifie que la page de login se charge correctement en GET.
     */
    public function testLoginPageIsAccessible(): void
    {
        // Création d'un client HTTP virtuel
        $client = static::createClient();

        // Requête GET vers /login
        $crawler = $client->request('GET', '/login');

        // On s'attend à un code 200 OK
        $this->assertResponseIsSuccessful();

        // Vérifie que le formulaire contient un champ username
        $this->assertGreaterThan(
            0,
            $crawler->filter('input[name="_username"]')->count(),
            'Le champ _username doit exister dans le formulaire.'
        );
        // Vérifie que le formulaire contient un champ password
        $this->assertGreaterThan(
            0,
            $crawler->filter('input[name="_password"]')->count(),
            'Le champ _password doit exister dans le formulaire.'
        );
    }

    /**
     * Vérifie qu'un login avec de mauvais identifiants renvoie une erreur.
     */
    public function testLoginWithInvalidCredentialsShowsError(): void
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/login', [
            '_username' => 'foo',
            '_password' => 'bar',
        ]);

        // 1) On attend un redirect vers /login
        $this->assertResponseRedirects('/login');

        // 2) On suit pour checking de l’alerte
        $crawler = $client->followRedirect();
        $this->assertSelectorExists('div.alert.alert-danger');
    }
}
