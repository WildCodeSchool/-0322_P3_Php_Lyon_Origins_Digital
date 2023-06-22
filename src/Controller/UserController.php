<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user', name: 'user_')]
class UserController extends AbstractController
{
    #[Route('/{id<\d+>}/dashboard', name: 'dashboard')]
    public function index(
        User $user
    ): Response {
        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }
}
