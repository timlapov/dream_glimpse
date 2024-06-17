<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/users', name: 'app_user_list')]
    public function list(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy([], ['username' => 'ASC']);
        return $this->render('user/list.html.twig', [
            'users' => $users,
        ]);
    }
}
