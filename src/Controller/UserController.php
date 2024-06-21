<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/users', name: 'app_user_list')]
    public function list(UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $userRepository->createQueryBuilder('u')
            ->orderBy('u.username', 'ASC')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            27
        );

        return $this->render('user/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }
    #[Route('/users/registration', name: 'app_registration')]
    public function registration(
        Request $request,
        EntityManagerInterface $em,
        UserRepository $userRepository,
        FileUploader $fileUploader
    ): Response
    {
        $user = new User();
        $user->setRegDate(new \DateTime);

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('userImage')->getData();

            if ($imageFile) {
                $imageUrl = $fileUploader->upload($imageFile);
                $user->setUserImage($imageUrl);
            }

            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'L\'enregistrement a été effectué avec succès. Vous pouvez vous connecter au système.');
            return $this->redirectToRoute('app_login');
        } else {
            return $this->render('registration/registration.html.twig', [
                "registrationForm" => $form
            ]);
        }
    }

    #[Route('/users/{id}', name: 'app_user_show')]
    public function postsByUser(
        User $user,
        PostRepository $postRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
        $query = $postRepository->createQueryBuilder('p')
            ->where('p.user = :user')
            ->setParameter('user', $user)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            9  // количество постов на странице
        );

        return $this->render('user/list_by_user.html.twig', [
            'user' => $user,
            'pagination' => $pagination,
        ]);
    }
}
