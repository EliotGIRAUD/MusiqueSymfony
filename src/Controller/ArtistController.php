<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Form\ArtistType;
use App\Service\SpotifyApi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArtistController extends AbstractController
{
    #[Route('/artist/new', name: 'artist_new', methods: ['GET','POST'])]
    public function new(Request $request, SpotifyApi $spotifyApi, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ArtistType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $spotifyId = $form->get('spotifyId')->getData();
            $data = $spotifyApi->fetchArtist($spotifyId);

            $artist = new Artist();
            $artist->setName($data['name']);
            $artist->setSpotifyId($spotifyId);
            $artist->setImageUrl($data['images'][0]['url'] ?? null);
            $artist->setGenres($data['genres'] ?? []);

            $em->persist($artist);
            $em->flush();

            return $this->redirectToRoute('artist_show', ['id' => $artist->getId()]);
        }

        return $this->render('artist/new.html.twig', [
            'artistForm' => $form->createView(),
        ]);
    }

    #[Route('/artist/{id}', name: 'artist_show', methods: ['GET'])]
    public function show(Artist $artist): Response
    {
        return $this->render('artist/show.html.twig', [
            'artist' => $artist,
        ]);
    }
}
