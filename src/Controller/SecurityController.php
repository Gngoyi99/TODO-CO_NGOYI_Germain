<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login', methods: ['GET','POST'])]
    public function login(AuthenticationUtils $authUtils): Response
    {
        // Si déjà connecté, redirige vers la liste des tâches
        if ($this->getUser()) {
            return $this->redirectToRoute('task_list');
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $authUtils->getLastUsername(),
            'error'         => $authUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/logout', name: 'logout', methods: ['GET','POST'])]
    public function logout(): void
    {
        // Ce code n'est jamais exécuté, c'est géré par le firewall.
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
