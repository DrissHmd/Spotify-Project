<?php

namespace App\Controller;

use App\Entity\FavoriteTrack;
use App\Repository\FavoriteTrackRepository;
use App\Service\AuthSpotifyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/track')]
class TrackController extends AbstractController
{
    private string $token;

    public function __construct(private readonly AuthSpotifyService  $authSpotifyService,
                                private readonly HttpClientInterface $httpClient,
    )
    {
        $this->token = $this->authSpotifyService->auth();
    }

    #[Route('/search', name: 'app_search_track')]
    public function search(Request $request): Response
    {
        $query = $request->query->get('query');

        $results = [];

        if ($query) {
            $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/search', [
                'query' => [
                    'q' => $query,
                    'type' => 'track',
                    'market' => 'FR'
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                ]
            ]);

            $items = $response->toArray()['tracks']['items'];

            foreach ($items as $item) {
                $track = new FavoriteTrack();
                $track->setTrackName($item['name']);
                $track->setPicture($item['album']['images'][0]['url']);
                $track->setType($item['album']['album_type']);
                $track->setTrackId($item['id']);
                $track->setArtistName($item['album']['artists'][0]['name']);

                $results[] = $track;
            }
        }

        return $this->render('search/resultstrack.html.twig', [
            'results' => $results,
            'query' => $query
        ]);
    }

    #[Route('/favorite', name: 'app_favorite')]
    public function favorite(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_user_login');
        }

        $favorites = $user->getTrackFavorites();

        return $this->render('track/favorite.html.twig', [
            'favorites' => $favorites,
        ]);
    }

    #[Route('/favorite/add/{id}', name: 'app_favorite_add')]
    public function addFavorite(string $id, FavoriteTrackRepository $favoriteRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user){
            return $this->redirectToRoute('app_user_login');
        }

        $existingFavorite = $favoriteRepository->findOneBy(['trackId' => $id]);

        if ($existingFavorite) {
            return $this->redirectToRoute('app_favorite');
        }

        $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/tracks/'.$id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ]
        ]);

        $track_info = $response->toArray();

        if ($track_info !== []) {
            $track = new FavoriteTrack();
            $track->setTrackName($track_info['name'])
                ->setTrackId($track_info['id'])
                ->setArtistName($track_info['artists'][0]['name'])
                ->setPicture($track_info['album']['images'][0]['url'])
                ->setType($track_info['album']['album_type']);

            $entityManager->persist($track);
            $user->addTrackFavorite($track);
            $entityManager->flush();

            $this->addFlash('success', 'Piste ajoutée aux favoris.');
        } else {
            $this->addFlash('error', 'Piste introuvable.');
        }

        return $this->redirectToRoute('app_favorite');
    }

    #[Route('/favorite/remove/{id}', name: 'app_favorite_remove')]
    public function removeFavorite(string $id, FavoriteTrackRepository $favoriteRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user){
            return $this->redirectToRoute('app_user_login');
        }

        $track = $favoriteRepository->find($id);

        if ($track) {
            $user->removeTrackFavorite($track);
            $entityManager->remove($track);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Piste supprimée des favoris.');
        } else {
            $this->addFlash('error', 'Piste introuvable.');
        }

        return $this->redirectToRoute('app_favorite');
    }

    #[Route('/{id}', name: 'track_id')]
    public function getTrack($id): Response
    {
        $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/tracks/'.$id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ]
        ]);

        $track_info = $response->toArray();
        $track = new FavoriteTrack();
        $track->setTrackName($track_info['name']);
        $track->setPicture($track_info['album']['images'][0]['url']);
        $track->setType($track_info['album']['album_type']);
        $track->setTrackId($track_info['id']);
        $track->setArtistName($track_info['album']['artists'][0]['name']);

        return $this->render('track/index.html.twig', [
            'track' => $track,
        ]);
    }


}
