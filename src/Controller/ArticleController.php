<?php
namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/article')] // Pour les routes API
class ArticleController extends AbstractController
{
    // Index - Pour récupérer tous les articles
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

    // Création d'un nouvel article
    #[Route('/new', name: 'api_article_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer les données JSON envoyées avec la requête
        $data = json_decode($request->getContent(), true);

        // Valider les données (ici tu peux ajouter des vérifications supplémentaires)
        if (!isset($data['title'], $data['content'], $data['slug'], $data['publishedAt'])) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }

        // Créer un nouvel article à partir des données JSON
        $article = new Article();
        $article->setTitle($data['title']);
        $article->setContent($data['content']);
        $article->setSlug($data['slug']);
        $article->setPublishedAt(new \DateTimeImmutable($data['publishedAt']));

        // Sauvegarder dans la base de données
        $entityManager->persist($article);
        $entityManager->flush();

        // Retourner une réponse JSON avec les détails du nouvel article
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

    // Montrer un article spécifique
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

    // Editer un article
    #[Route('/{id}/edit', name: 'api_article_edit', methods: ['PUT'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Valider les données
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

        // Sauvegarder les modifications
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

    // Supprimer un article
    #[Route('/{id}', name: 'api_article_delete', methods: ['DELETE'])]
    public function delete(Article $article, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($article);
        $entityManager->flush();

        return new JsonResponse(['status' => 'success', 'message' => 'Article deleted successfully']);
    }
}
