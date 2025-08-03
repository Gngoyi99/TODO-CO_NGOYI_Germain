<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création de l'utilisateur Admin
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        // Hash du mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            'adminpassword'
        );
        $admin->setPassword($hashedPassword);
        $manager->persist($admin);

        // Création de 3 tâches associées à l'Admin
        for ($i = 1; $i <= 3; $i++) {
            $task = new Task();
            $task->setTitle(sprintf('Tâche %d', $i));
            $task->setContent(sprintf('Contenu de la tâche %d', $i));
            $task->setAuthor($admin);
            $manager->persist($task);
        }

        // Envoi en base
        $manager->flush();
    }
}
