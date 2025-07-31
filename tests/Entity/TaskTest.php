<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Test unitaire pour l'entité Task.
 * @covers \App\Entity\Task
 */
class TaskTest extends TestCase
{
    /**
     * Vérifie que le constructeur initialise created_at et is_Done.
     */
    public function testDefaultState(): void
    {
        $task = new Task();

        // created_at doit être une instance de DateTimeInterface
        $this->assertInstanceOf(\DateTimeInterface::class, $task->getcreated_at());

        // is_Done doit être false par défaut
        $this->assertFalse($task->is_Done());
    }

    /**
     * Vérifie l'accès et la mutation du titre.
     */
    public function testTitleAccessors(): void
    {
        $task = new Task();
        $title = 'Titre de test';
        $task->setTitle($title);

        $this->assertSame($title, $task->getTitle());
    }

    /**
     * Vérifie l'accès et la mutation du contenu.
     */
    public function testContentAccessors(): void
    {
        $task = new Task();
        $content = 'Contenu de test.';
        $task->setContent($content);

        $this->assertSame($content, $task->getContent());
    }

    /**
     * Vérifie le fonctionnement de la méthode toggle().
     */
    public function testToggle(): void
    {
        $task = new Task();

        // passe à done = true
        $returned = $task->toggle(true);
        $this->assertTrue($task->is_Done());
        // la méthode doit retourner l'instance pour le chaining
        $this->assertSame($task, $returned);

        // repasse à done = false
        $task->toggle(false);
        $this->assertFalse($task->is_Done());
    }

    /**
     * Vérifie les getters/setters pour author.
     */
    public function testAuthorAccessors(): void
    {
        $task = new Task();
        $user = new User();

        // par défaut, author = null
        $this->assertNull($task->getAuthor());

        // définir un auteur
        $returned = $task->setAuthor($user);
        $this->assertSame($user, $task->getAuthor());
        $this->assertSame($task, $returned);

        // autoriser null (anonyme)
        $task->setAuthor(null);
        $this->assertNull($task->getAuthor());
    }

    /**
     * Vérifie le setter/getter de created_at.
     */
    public function testCreatedAtMutator(): void
    {
        $task = new Task();
        $newDate = new \DateTimeImmutable('2000-01-01 00:00:00');

        $returned = $task->setcreated_at($newDate);
        $this->assertSame($newDate, $task->getcreated_at());
        $this->assertSame($task, $returned);
    }
}
