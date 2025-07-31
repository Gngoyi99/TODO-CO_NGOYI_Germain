<?php
// tests/Controller/TaskControllerTest.php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;


/**
 * Tests fonctionnels pour le contrôleur TaskController (CRUD des tâches).
 *  * @covers \App\Controller\TaskController
 */

class TaskControllerTest extends WebTestCase
{
    /**
     * Simule la connexion d'un utilisateur existant pour les tests.
     *
     * @param \Symfony\Bundle\FrameworkBundle\KernelBrowser $client
     * @return User L'utilisateur chargé en session
     */
    private function loginUser(KernelBrowser $client): User
    {
        // 1) On démarre d’abord une simple requête pour booter le kernel une seule fois
        $client->request('GET', '/login');

        // 2) On récupère ensuite le conteneur via la méthode statique (pas de deuxième boot)
        $container = static::getContainer();
        $em        = $container->get('doctrine')->getManager();
        $user      = $em->getRepository(User::class)->findOneBy([]);
        $this->assertNotNull($user, 'Aucun utilisateur pour les tests');

        // 3) Prépare la session et le token
        $session      = $container->get('session');
        $firewallName = 'main';
        $token        = new UsernamePasswordToken($user, '', $user->getRoles(), $firewallName);
        $session->set('_security_' . $firewallName, serialize($token));
        $session->save();

        // 4) On refile le cookie au client (session démarrée)
        $client->getCookieJar()->set(
            new Cookie($session->getName(), $session->getId())
        );

        return $user;
    }

    /**
     * Teste l'accès à la liste des tâches pour un utilisateur authentifié.
     */
    public function testListAsAuthenticatedUser(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        // Requête GET /tasks
        $client->request('GET', '/tasks');

        // On s'attend à un code 200 (OK)
        $this->assertResponseIsSuccessful();
    }

    /**
     * Teste la création d'une nouvelle tâche via le formulaire.
     */
    public function testCreateTask(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        // Accéder à la page de création
        $crawler = $client->request('GET', '/tasks/create');
        $this->assertResponseIsSuccessful();

        // Remplir et soumettre le formulaire
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]'   => 'Tâche test',
            'task[content]' => 'Contenu de la tâche test',
        ]);
        $client->submit($form);

        // Vérifier la redirection vers la liste
        $this->assertTrue($client->getResponse()->isRedirect('/tasks'));
        $client->followRedirect();

        // Confirme qu'un message flash de succès est présent
        $this->assertSelectorExists('div.alert.alert-success');
    }

    /**
     * Teste la modification d'une tâche existante.
     */
    public function testEditTask(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        // Récupère une tâche existante
        $em = $client->getContainer()->get('doctrine')->getManager();
        $task = $em->getRepository(Task::class)->findOneBy([]);
        $this->assertNotNull($task, 'Aucune tâche trouvée pour l’édition.');

        // Accéder à la page d'édition
        $crawler = $client->request('GET', '/tasks/' . $task->getId() . '/edit');
        $this->assertResponseIsSuccessful();

        // Soumettre le formulaire avec modifications
        $form = $crawler->selectButton('Modifier')->form([
            'task[title]'   => 'Titre modifié',
            'task[content]' => 'Contenu modifié',
        ]);
        $client->submit($form);

        // Vérifier la redirection et le flash
        $this->assertTrue($client->getResponse()->isRedirect('/tasks'));
        $client->followRedirect();
        $this->assertSelectorExists('div.alert.alert-success');
    }

    /**
     * Teste la bascule de l'état d'une tâche (faite / non faite).
     */
    public function testToggleTask(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        // Charge une tâche existante
        $em = $client->getContainer()->get('doctrine')->getManager();
        $task = $em->getRepository(Task::class)->findOneBy([]);
        $this->assertNotNull($task, 'Aucune tâche trouvée pour le toggle.');

        // Effectue la requête POST sur /tasks/{id}/toggle
        $client->request('POST', '/tasks/' . $task->getId() . '/toggle');

        // Vérifie la redirection et le flash de succès
        $this->assertTrue($client->getResponse()->isRedirect('/tasks'));
        $client->followRedirect();
        $this->assertSelectorExists('div.alert.alert-success');
    }

    /**
     * Teste la suppression d'une tâche existante.
     */
    public function testDeleteTask(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        // Crée une tâche temporaire à supprimer
        $em = $client->getContainer()->get('doctrine')->getManager();
        $taskToDelete = new Task();
        $taskToDelete->setTitle('Temp à supprimer')
            ->setContent('Contenu temporaire');
        $em->persist($taskToDelete);
        $em->flush();

        // Envoi de la requête POST sur /tasks/{id}/delete
        $client->request('POST', '/tasks/' . $taskToDelete->getId() . '/delete');

        // Vérifie la redirection et le flash de succès
        $this->assertTrue($client->getResponse()->isRedirect('/tasks'));
        $client->followRedirect();
        $this->assertSelectorExists('div.alert.alert-success');
    }
}
