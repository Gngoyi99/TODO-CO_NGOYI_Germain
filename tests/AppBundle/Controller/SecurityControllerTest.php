<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageIsAccessible()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        // Vérifie que la page est accessible
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Vérifie qu'on a un champ username et password dans le formulaire
        $this->assertGreaterThan(0, $crawler->filter('input[name="_username"]')->count());
        $this->assertGreaterThan(0, $crawler->filter('input[name="_password"]')->count());
    }

    public function testLoginWithInvalidCredentialsShowsError()
    {
        $client = static::createClient();

        // Tentative de login avec mauvais identifiants
        $crawler = $client->request('POST', '/login_check', [
            '_username' => 'mauvais_utilisateur',
            '_password' => 'mauvais_mdp',
        ]);

        // Suivre la redirection vers /login (car login_check ne fait que rediriger)
        $crawler = $client->followRedirect();

        // Vérifie que l'erreur s'affiche bien dans la page
        $this->assertGreaterThan(
            0,
            $crawler->filter('div.alert.alert-danger')->count(),
            'Le message d’erreur ne s’affiche pas.'
        );
    }
}
