<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserControllerTest extends WebTestCase
{
    private function loginUser($client)
    {
        $container = $client->getContainer();
        $em = $container->get('doctrine')->getManager();

        $user = $em->getRepository(User::class)->findOneBy([]);
        $this->assertNotNull($user, 'Aucun utilisateur trouvé pour les tests.');

        $session = $container->get('session');
        $firewall = 'main';
        $token = new UsernamePasswordToken($user, null, $firewall, $user->getRoles());
        $session->set('_security_' . $firewall, serialize($token));
        $session->save();

        $client->getCookieJar()->set(
            new \Symfony\Component\BrowserKit\Cookie($session->getName(), $session->getId())
        );
    }

    public function testListUsers()
    {
        $client = static::createClient();
        $this->loginUser($client);

        $client->request('GET', '/users');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreateUser()
    {
        $client = static::createClient();
        $this->loginUser($client);

        $crawler = $client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'user-test',
            'user[email]' => 'test@example.com',
            'user[password][first]' => 'motdepasse',
            'user[password][second]' => 'motdepasse',
            'user[roles]' => ['ROLE_USER'],
        ]);

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect('/users'));

        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testEditUser()
    {
        $client = static::createClient();
        $this->loginUser($client);

        $em = $client->getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneBy([]);

        $this->assertNotNull($user, 'Aucun utilisateur trouvé pour la modification.');

        $crawler = $client->request('GET', '/users/' . $user->getId() . '/edit');

        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'GermainTest',
            'user[email]' => 'germain@todo.com',
            'user[password][first]' => 'Password1234',
            'user[password][second]' => 'Password1234',
            'user[roles]' => ['ROLE_USER'], // ou ['ROLE_ADMIN']
        ]);

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect('/users'));

        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
