<?php

namespace App\Controller;

//use App\Factory\TrackFactory;
use App\Entity\Track;
use App\Service\AuthSpotifyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TrackController extends AbstractController
{
    private string $token;

    public function __construct(private readonly AuthSpotifyService  $authSpotifyService,
                                private readonly HttpClientInterface $httpClient,
//                                private readonly TrackFactory         $trackFactory
    )
    {
        $this->token = $this->authSpotifyService->auth();
    }

    #[Route('/track/{id}', name: 'track_id')]
    public function getTrack($id): Response
    {
        $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/tracks/'.$id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ]
        ]);

        $track_info = $response->toArray();
        $track = new Track();
        $track->setName($track_info['name'])
            ->setPictureLink($track_info['album']['images'][0]['url'])
            ->setType($track_info['album']['album_type'])
            ->setId($track_info['id'])
            ->setArtist($track_info['album']['artists'][0]['name']);

        return $this->render('track/index.html.twig', [
            'track' => $track,
        ]);
    }

    #[Route('/search', name: 'app_search')]
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
                $track = new Track();
                $track->setName($item['name'])
                    ->setPictureLink($item['album']['images'][0]['url'])
                    ->setType($item['type'])
                    ->setId($item['id'])
                    ->setArtist($item['artists'][0]['name'])
                    ->setPreviewUrl($item['preview_url'] ?? null);

                $results[] = $track;
            }
        }

        return $this->render('search/results.html.twig', [
            'results' => $results,
            'query' => $query
        ]);
    }

    #[Route('/favorite', name: 'app_favorite')]
    public function favorite(Request $request): ?Response
    {
        return null;
    }

}
