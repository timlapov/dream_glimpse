<?php

namespace App\Controller;

use App\Entity\NewsletterEmail;
use App\Entity\Post;
use App\Form\NewsletterSubscribeType;
use App\Newsletter\NewsletterNotification;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{

    #[Route('/index', name: 'app_index')]
    public function index(
        Request $request,
        Post $post,
        PostRepository $postRepository,
        EntityManagerInterface $em,
        NewsletterNotification $newsletterNotification
    ): Response
    {
        $newsletterEmail = new NewsletterEmail();
        $form = $this->createForm(NewsletterSubscribeType::class, $newsletterEmail);
        $form->handleRequest($request);

        $posts = $postRepository->findBy([], ['createdAt' => 'DESC'], 9);
//        return $this->render('index/registration.html.twig', [
//            'posts' => $posts,
//            'newsletterForm' => $form
//        ]);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($newsletterEmail);
            $em->flush();
            try {
                $newsletterNotification->sendConfirmationEmail(newEmail: $newsletterEmail);
            } catch (TransportExceptionInterface $e) {
            }
            $this->addFlash('success', 'Votre email a été enregistré, merci');
            return $this->redirectToRoute('app_index');
        }
        return $this->render('index/index.html.twig', [
            'posts' => $posts,
            'newsletterForm' => $form
        ]);
    }
}
