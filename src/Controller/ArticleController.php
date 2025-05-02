<?php
namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/article')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'api_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): JsonResponse
    {
        $articles = $articleRepository->findAll();
        $data = [];

        foreach ($articles as $article) {
            $data[] = [
                'id' => $article->getId(),
                'title' => $article->getTitle(),
                'content' => $article->getContent(),
                'slug' => $article->getSlug(),
                'publishedAt' => $article->getPublishedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/new', name: 'api_article_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['title'], $data['content'], $data['slug'], $data['publishedAt'])) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }

        $article = new Article();
        $article->setTitle($data['title']);
        $article->setContent($data['content']);
        $article->setSlug($data['slug']);
        $article->setPublishedAt(new \DateTimeImmutable($data['publishedAt']));

        $entityManager->persist($article);
        $entityManager->flush();

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Article created successfully',
            'article' => [
                'id' => $article->getId(),
                'title' => $article->getTitle(),
                'content' => $article->getContent(),
                'slug' => $article->getSlug(),
                'publishedAt' => $article->getPublishedAt()->format('Y-m-d H:i:s'),
            ]
        ], 201);
    }

    #[Route('/{id}', name: 'api_article_show', methods: ['GET'])]
    public function show(Article $article): JsonResponse
    {
        return new JsonResponse([
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
            'slug' => $article->getSlug(),
            'publishedAt' => $article->getPublishedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}/edit', name: 'api_article_edit', methods: ['PUT'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['title'])) {
            $article->setTitle($data['title']);
        }

        if (isset($data['content'])) {
            $article->setContent($data['content']);
        }

        if (isset($data['slug'])) {
            $article->setSlug($data['slug']);
        }

        if (isset($data['publishedAt'])) {
            $article->setPublishedAt(new \DateTimeImmutable($data['publishedAt']));
        }

        $entityManager->flush();

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Article updated successfully',
            'article' => [
                'id' => $article->getId(),
                'title' => $article->getTitle(),
                'content' => $article->getContent(),
                'slug' => $article->getSlug(),
                'publishedAt' => $article->getPublishedAt()->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    #[Route('/{id}', name: 'api_article_delete', methods: ['DELETE'])]
    public function delete(Article $article, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($article);
        $entityManager->flush();

        return new JsonResponse(['status' => 'success', 'message' => 'Article deleted successfully']);
    }
}
