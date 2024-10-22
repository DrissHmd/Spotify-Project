<?php

namespace App\Controller;

use App\Entity\FavoriteArtist;
use App\Repository\FavoriteArtistRepository;
use App\Service\AuthSpotifyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/artist')]
class ArtistController extends AbstractController
{
    private string $token;

    public function __construct(
        private readonly AuthSpotifyService  $authSpotifyService,
        private readonly HttpClientInterface $httpClient
    )
    {
        $this->token = $this->authSpotifyService->auth();
    }



    #[Route('/search', name: 'app_search_artist')]
    public function searchArtist(Request $request): Response
    {
        $query = $request->query->get('query');
        $results = [];

        if ($query) {
            $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/search', [
                'query' => [
                    'q' => $query,
                    'type' => 'artist',
                    'market' => 'FR'
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                ]
            ]);

            $items = $response->toArray()['artists']['items'];


            foreach ($items as $item) {
                $results[] = [
                    'artistId' => $item['id'],
                    'name' => $item['name'],
                    'image' => $item['images'][0]['url'] ?? null,
                    'followers' => $item['followers']['total'] ?? 0,
                    'genres' => $item['genres'],
                ];
            }
        }

        return $this->render('search/resultsartist.html.twig', [
            'results' => $results,
            'query' => $query
        ]);
    }

    #[Route('/favorite/add/{id}', name: 'app_favorite_artist_add')]
    public function addFavoriteArtist(string $id, FavoriteArtistRepository $favoriteRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_user_login');
        }

        $existingFavorite = $favoriteRepository->findOneBy(['artistId' => $id]);

        if ($existingFavorite) {
            return $this->redirectToRoute('app_favorite_artist');
        }

        $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/artists/'.$id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ]
        ]);

        $artist_info = $response->toArray();

        if ($artist_info !== []) {
            $favoriteArtist = new FavoriteArtist();
            $favoriteArtist->setArtistId($id);
            $favoriteArtist->setName($artist_info['name']);
            $favoriteArtist->setFollowers($artist_info['followers']['total']);
            $favoriteArtist->setGenres($artist_info['genres']);
            $favoriteArtist->setPicture($artist_info['images'][0]['url']);

            $entityManager->persist($favoriteArtist);
            $user->addArtistFavorite($favoriteArtist);
            $entityManager->flush();

            $this->addFlash('success', 'Artiste ajouté aux favoris.');
        } else {
            $this->addFlash('error', 'Artiste introuvable.');
        }

        return $this->redirectToRoute('app_favorite_artist');
    }

    #[Route('/favorite/remove/{id}', name: 'app_favorite_artist_remove')]
    public function removeFavoriteArtist(string $id, FavoriteArtistRepository $favoriteArtistRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_user_login');
        }

        $artist = $favoriteArtistRepository->findOneBy(['artistId' => $id]);

        if ($artist) {
            $user->removeArtistFavorite($artist);
            $entityManager->remove($artist);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Artiste supprimé des favoris.');
        } else {
            $this->addFlash('error', 'Artiste introuvable.');
        }

        return $this->redirectToRoute('app_favorite_artist');
    }

    #[Route('/favorite', name: 'app_favorite_artist')]
    public function favoriteArtists(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_user_login');
        }

        $favoriteArtists = $user->getArtistFavorites();

        return $this->render('artist/favorite.html.twig', [
            'favorites' => $favoriteArtists,
        ]);
    }

    #[Route('/{id}', name: 'artist_id')]
    public function getArtist(string $id): Response
    {
        $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/artists/'.$id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ]
        ]);

        $artist_info = $response->toArray();
        $artist = new FavoriteArtist();
        $artist->setArtistId($id);
        $artist->setName($artist_info['name']);
        $artist->setFollowers($artist_info['followers']['total']);
        $artist->setGenres($artist_info['genres']);
        $artist->setPicture($artist_info['images'][0]['url']);

        return $this->render('artist/index.html.twig', [
            'artist' => $artist,
        ]);
    }

}
