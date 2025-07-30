<?php
// src/Controller/TaskController.php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;


class TaskController extends AbstractController
{
    // Liste des tâches
    public function listAction(ManagerRegistry $doctrine): Response
    {
        $tasks = $doctrine->getRepository(Task::class)->findAll();

        return $this->render('task/list.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    // Création d'une nouvelle tâche
    public function createAction(Request $request, ManagerRegistry $doctrine): Response
    {
        $task = new Task();
        $task->setAuthor($this->getUser()); // null si anonyme

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a bien été ajoutée.');
            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Édition d'une tâche existante
    public function editAction(int $id, Request $request, ManagerRegistry $doctrine): Response
    {
        $task = $doctrine->getRepository(Task::class)->find($id);
        if (!$task) {
            throw $this->createNotFoundException("La tâche #{$id} n'existe pas.");
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getManager()->flush();
            $this->addFlash('success', 'La tâche a bien été modifiée.');
            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    // Basculer l'état "fait / non fait" d'une tâche
    public function toggleTaskAction(int $id, ManagerRegistry $doctrine): Response
    {
        $task = $doctrine->getRepository(Task::class)->find($id);
        if (!$task) {
            throw $this->createNotFoundException("La tâche #{$id} n'existe pas.");
        }

        $task->toggle(! $task->is_Done());
        $doctrine->getManager()->flush();

        $this->addFlash('success', sprintf(
            'La tâche "%s" a bien été marquée comme %s.',
            $task->getTitle(),
            $task->is_Done() ? 'faite' : 'non faite'
        ));

        return $this->redirectToRoute('task_list');
    }

    // Suppression d'une tâche
    public function deleteTaskAction(int $id, Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $task = $em->getRepository(Task::class)->find($id);

        if (!$task) {
            throw $this->createNotFoundException("La tâche avec l'ID {$id} n'existe pas.");
        }

        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }

        if ($task->getAuthor() === null && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Seul un administrateur peut supprimer cette tâche anonyme.');
        }

        if ($task->getAuthor() !== null && $task->getAuthor() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous ne pouvez supprimer que vos propres tâches.');
        }

        $em->remove($task);
        $em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }

}
