<?php
// tests/Controller/UserControllerTest.php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

/**
 * Tests fonctionnels pour le contrôleur UserController (crud utilisateurs).
 * @covers \App\Controller\UserController
 */
class UserControllerTest extends WebTestCase
{
    /**
     * Simule la connexion d'un utilisateur existant pour les tests.
     *
     * @param \Symfony\Bundle\FrameworkBundle\KernelBrowser $client
     * @return User
     */
    private function loginUser(KernelBrowser $client): User
    {
        // 1) Die Boot einmal: lancez d’abord une requête factice pour ne booter qu’UNE fois
        $client->request('GET', '/login');

        // 2) Puis récupérez le conteneur « après » le premier boot
        $container = static::getContainer();
        $em        = $container->get('doctrine')->getManager();
        $user      = $em->getRepository(User::class)->findOneBy([]);
        $this->assertNotNull($user);

        // 3) Préparez la session/ticket
        $session      = $container->get('session');
        $firewallName = 'main';
        $token        = new UsernamePasswordToken($user, '', $user->getRoles(), $firewallName);
        $session->set("_security_{$firewallName}", serialize($token));
        $session->save();

        // 4) Injectez le cookie de session
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $user;
    }

    /**
     * Vérifie que la liste des utilisateurs est accessible pour un admin.
     */
    public function testListUsers(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        // Requête GET /users
        $client->request('GET', '/users');
        // On s'attend à une réponse 200 OK
        $this->assertResponseIsSuccessful();
    }

    /**
     * Teste la création d'un nouvel utilisateur via le formulaire.
     */
    public function testCreateUser(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        // Accède à la page de création
        $crawler = $client->request('GET', '/users/create');
        $this->assertResponseIsSuccessful();

        // Remplit et soumet le formulaire
        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]'                => 'user-test',
            'user[email]'                   => 'test@example.com',
            'user[password][first]'         => 'Password123',
            'user[password][second]'        => 'Password123',
            'user[roles]'                   => ['ROLE_USER'],
        ]);
        $client->submit($form);

        // Vérifie la redirection vers /users
        $this->assertTrue($client->getResponse()->isRedirect('/users'));
        $crawler = $client->followRedirect();

        // Vérifie l'affichage du flash de succès
        $this->assertSelectorExists('div.alert.alert-success');
    }

    /**
     * Teste la modification d'un utilisateur existant.
     */
    public function testEditUser(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        // Récupère un utilisateur existant
        $em = $client->getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneBy([]);
        $this->assertNotNull($user, 'Aucun utilisateur trouvé pour la modification.');

        // Accède à la page d'édition
        $crawler = $client->request('GET', '/users/' . $user->getId() . '/edit');
        $this->assertResponseIsSuccessful();

        // Remplit et soumet le formulaire
        $form = $crawler->selectButton('Modifier')->form([
            'user[username]'                => 'user-modified',
            'user[email]'                   => 'modified@example.com',
            'user[password][first]'         => 'NewPass123',
            'user[password][second]'        => 'NewPass123',
            'user[roles]'                   => ['ROLE_USER'],
        ]);
        $client->submit($form);

        // Vérifie la redirection et le flash de succès
        $this->assertTrue($client->getResponse()->isRedirect('/users'));
        $client->followRedirect();
        $this->assertSelectorExists('div.alert.alert-success');
    }
}
