<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\UserEditType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

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

    #[Route('/users/{id}', name: 'app_user_show', methods: ['GET'])]
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

    #[Route('/users/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('USER_EDIT', $user);

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();
            $this->addFlash('success', 'Votre profile a été modifié. Bonne journée');
            return $this->redirectToRoute('app_user_edit', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
            }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('users/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authChecker
    ): Response
    {
        $this->denyAccessUnlessGranted('USER_DELETE', $user);

        if ($authChecker->isGranted('ROLE_ADMIN') && $user === $this->getUser()) {
            $this->addFlash('danger', 'Un administrateur ne peut pas se supprimer lui-même. Accédez à la base de données via phpMyAdmin ou la ligne de commande. Je vous souhaite une excellente journée.');
            return $this->redirectToRoute('app_index');
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            if (!$authChecker->isGranted('ROLE_ADMIN')) {
                $tokenStorage->setToken(null);
                $request->getSession()->invalidate();
            }

            $this->addFlash('success', 'Profile a été supprimé. Bonne journée');
        }
        return $this->redirectToRoute('app_index');
    }
}
