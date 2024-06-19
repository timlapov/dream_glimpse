<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostRepository;
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

    #[Route('/users/{id}', name: 'app_user_show')]
    public function postsByUser(
        User $user,
        PostRepository $postRepository,
    ): Response
    {
        $posts = $postRepository->findBy(['user' => $user->getId()]);

        return $this->render('user/list_by_user.html.twig', [
            'user' => $user->getUsername(),
            'posts' => $posts,
        ]);
    }
}
