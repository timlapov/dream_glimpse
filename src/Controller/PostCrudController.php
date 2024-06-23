<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Service\FileUploader;
use App\Service\Text2ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/post')]
class PostCrudController extends AbstractController
{
    #[Route('/', name: 'app_post_crud_index', methods: ['GET'])]
    public function index(PostRepository $postRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $postRepository->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('post_crud/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_post_crud_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        Text2ImageService $text2ImageService,
        FileUploader $fileUploader,
    ): Response
    {
        $post = new Post();
        $post->setCreatedAt(new \DateTime());
        $post->setUser($this->getUser());
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            $content = $post->getContent();
//            $imageUrl = $text2ImageService->generateImage($content);
//
//            if ($imageUrl) {
//                $post->setImageUrl($imageUrl);
//            }
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageUrl')->getData();

            if ($imageFile) {
                $imageUrl = $fileUploader->upload($imageFile);
                $post->setImageUrl($imageUrl);
            }

            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'Votre article a été publié. Vous pouvez le modifier ou revenir à la page d\'accueil');
            //return $this->redirectToRoute('app_post_crud_edit', ['id' => $post->getId()], Response::HTTP_SEE_OTHER);
            return $this->redirectToRoute('app_index', (array)Response::HTTP_SEE_OTHER);
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
    public function edit(
        Request $request,
        Post $post,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader,
    ): Response
    {
        $this->denyAccessUnlessGranted('POST_EDIT', $post);
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageUrl')->getData();
            if ($imageFile) {
                $imageUrl = $fileUploader->upload($imageFile);
                $post->setImageUrl($imageUrl);
            }
            $entityManager->flush();
            $this->addFlash('success', 'Votre article a été modifié. Bonne journée');
            return $this->redirectToRoute('app_post_crud_show', ['id' => $post->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post_crud/edit.html.twig', [
            'post' => $post,
            'form' => $form
        ]);
    }

    #[Route('/delete/{id}', name: 'app_post_crud_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('POST_DELETE', $post);

        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
            $this->addFlash('success', 'Votre article a été supprimé. Bonne journée');
        }

        return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);
    }
}
