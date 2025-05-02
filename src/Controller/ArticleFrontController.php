<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleFrontController extends AbstractController
{
    #[Route('/article/new', name: 'app_article_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleForm::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('app_article_show', [
                'id' => $article->getId()
            ]);
        }

        return $this->render('article/new.html.twig', [
            'articleForm' => $form->createView(),
        ]);
    }

    #[Route('/article/{id}', name: 'app_article_show')]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/articles', name: 'app_article_index')]
    public function index(ArticleRepository $articleRepository)
    {
        $articles = $articleRepository->findAll();

        // Affichage de la liste des articles dans une vue Twig
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }
}
