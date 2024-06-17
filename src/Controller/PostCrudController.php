<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Service\Text2ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/post')]
class PostCrudController extends AbstractController
{
    #[Route('/', name: 'app_post_crud_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post_crud/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_post_crud_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Text2ImageService $text2ImageService): Response
    {
        $post = new Post();
        $post->setCreatedAt(new \DateTime());
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            $content = $post->getContent();
//            $imageUrl = $text2ImageService->generateImage($content);
//
//            if ($imageUrl) {
//                $post->setImageUrl($imageUrl);
//            }

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_post_crud_edit', ['id' => $post->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post_crud/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_crud_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('post_crud/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_post_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post_crud/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_crud_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
            $this->addFlash('success', 'Votre article a été supporimé. Bonne journée');
        }

        return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);
    }
}
