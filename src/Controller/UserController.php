<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class UserController extends AbstractController
{

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    public function listAction(ManagerRegistry $doctrine): Response
    {
        // Vérifie si l'utilisateur a le rôle admin
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas accès à cette page.');
            return $this->redirectToRoute('task_list');
        }

        $users = $doctrine->getRepository(User::class)->findAll();

        return $this->render('user/list.html.twig', [
            'users' => $users
        ]);
    }


    public function createAction(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les rôles depuis le formulaire
            $roles = $form->get('roles')->getData();
            $user->setRoles($roles);

            // Hash le mot de passe
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            );
            $user->setPassword($hashedPassword);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('task_list');
        }


        return $this->render('user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     *  @IsGranted("ROLE_ADMIN")
     */
    public function editAction(int $id, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Si la case "Modifier le mot de passe" est cochée
            if ($form->get('changePassword')->getData()) {
                $newPassword = $form->get('password')->getData();
                if (!empty($newPassword)) {
                    $hashedPassword = $passwordHasher->hashPassword(
                        $user,
                        $newPassword
                    );
                    $user->setPassword($hashedPassword);
                }
            }

            // Flush et redirection après TOUTES les modifs (même email/roles)
            $em->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié.");
            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}
