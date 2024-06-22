<?php
namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/users')]
class AdminController extends AbstractController
{
#[Route('/', name: 'admin_user_index', methods: ['GET'])]
public function index(UserRepository $userRepository): Response
{
$this->denyAccessUnlessGranted('ROLE_ADMIN');
return $this->render('admin/users/index.html.twig', [
'users' => $userRepository->findAll(),
]);
}

// TODO Добавить методы для редактирования и удаления пользователей

}
