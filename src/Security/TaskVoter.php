<?php
// src/Security/TaskVoter.php

namespace App\Security;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskVoter extends Voter
{
    public const DELETE = 'TASK_DELETE';

    public function __construct(private Security $security) {}

    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === self::DELETE
            && $subject instanceof Task;
    }

    /**
     * @param Task $task
     */
    protected function voteOnAttribute(string $attribute, $task, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // pas d’utilisateur connecté
        if (!$user instanceof User) {
            return false;
        }

        // Si admin → toujours OK
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Si la tâche a un auteur (autre que “anonyme”) et qu’il est le même objet User
        if ($task->getAuthor() instanceof User && $task->getAuthor() === $user) {
            return true;
        }

        // auteur “anonyme” ou auteur différent → interdit
        return false;
    }
}
