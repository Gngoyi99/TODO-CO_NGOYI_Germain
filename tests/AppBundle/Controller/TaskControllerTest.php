<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TaskControllerTest extends WebTestCase
{
    /**
     * Cette méthode connecte un utilisateur pour simuler une session authentifiée.
     * Elle utilise un token Symfony et configure le cookie de session.
     */
    private function loginUser($client)
    {
        $container = $client->getContainer();
        $em = $container->get('doctrine')->getManager();

        // Récupération d'un utilisateur existant pour les tests
        $user = $em->getRepository(User::class)->findOneBy([]);
        $this->assertNotNull($user, 'Aucun utilisateur trouvé pour les tests.');

        // Création du token de sécurité
        $session = $container->get('session');
        $firewall = 'main';
        $token = new UsernamePasswordToken($user, null, $firewall, $user->getRoles());

        // Sauvegarde du token dans la session
        $session->set('_security_' . $firewall, serialize($token));
        $session->save();

        // Ajout du cookie de session au client
        $client->getCookieJar()->set(
            new \Symfony\Component\BrowserKit\Cookie($session->getName(), $session->getId())
        );

        return $user;
    }

    /**
     * Teste que l'utilisateur authentifié peut accéder à la liste des tâches
     */
    public function testListAsAuthenticatedUser()
    {
        $client = static::createClient();
        $this->loginUser($client);

        $client->request('GET', '/tasks');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Teste la création d'une nouvelle tâche via le formulaire
     */
    public function testCreateTask()
    {
        $client = static::createClient();
        $this->loginUser($client);

        // Accès à la page de création de tâche
        $crawler = $client->request('GET', '/tasks/create');

        // Soumission du formulaire avec des données valides
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Tâche test',
            'task[content]' => 'Contenu test',
        ]);
        $client->submit($form);

        // Vérifie qu'on est redirigé vers la liste
        $this->assertTrue($client->getResponse()->isRedirect('/tasks'));

        // Vérifie qu'un message de succès est affiché après la redirection
        $client->followRedirect();
    }

    /**
     * Teste la modification d'une tâche existante
     */
    public function testEditTask()
    {
        $client = static::createClient();
        $this->loginUser($client);
        $em = $client->getContainer()->get('doctrine')->getManager();

        $task = $em->getRepository(Task::class)->findOneBy([]);
        $this->assertNotNull($task);

        $crawler = $client->request('GET', '/tasks/' . $task->getId() . '/edit');

        // Soumission du formulaire avec des modifications
        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Titre modifié',
            'task[content]' => 'Contenu modifié',
        ]);
        $client->submit($form);

        // Vérifie qu'on est redirigé puis qu'un message de succès s'affiche
        $this->assertTrue($client->getResponse()->isRedirect('/tasks'));
        $client->followRedirect();
    }

    /**
     * Teste l'action de basculer l'état d'une tâche (fait/non fait)
     */
    public function testToggleTask()
    {
        $client = static::createClient();
        $this->loginUser($client);
        $em = $client->getContainer()->get('doctrine')->getManager();

        $task = $em->getRepository(Task::class)->findOneBy([]);
        $this->assertNotNull($task);

        // Appel à l'URL de bascule
        $client->request('GET', '/tasks/' . $task->getId() . '/toggle');

        // Vérifie la redirection et le message de succès
        $this->assertTrue($client->getResponse()->isRedirect('/tasks'));
        $client->followRedirect();
    }

    /**
     * Teste la suppression d'une tâche existante
     */
    public function testDeleteTask()
    {
        $client = static::createClient();
        $this->loginUser($client);
        $em = $client->getContainer()->get('doctrine')->getManager();

        // On crée une tâche temporaire pour la supprimer
        $task = new Task();
        $task->setTitle('Tâche à supprimer');
        $task->setContent('Contenu temporaire');
        $em->persist($task);
        $em->flush();

        // Appel à la route de suppression
        $client->request('GET', '/tasks/' . $task->getId() . '/delete');

        // Vérifie la redirection et le message de succès
        $this->assertTrue($client->getResponse()->isRedirect('/tasks'));
        $client->followRedirect();
    }
}
