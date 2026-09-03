<?php

namespace App\Controller;

use App\Repository\PhotographerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class PhotographerController extends AbstractController
{
    #[Route('/photographers', name: 'photographers_list', methods: ['GET'])]
    public function index(PhotographerRepository $repository, Request $request): JsonResponse
    {
        // Query parametrelerini al (örn: /api/photographers?city=Ankara&style=Düğün)
        $city = $request->query->get('city');
        $style = $request->query->get('style');
        $maxPrice = $request->query->get('maxPrice');

        $criteria = [];
        if ($city) {
            $criteria['city'] = $city;
        }
        if ($style) {
            $criteria['style'] = $style;
        }

        // Temel sorgu
        $photographers = $repository->findBy($criteria);

        // Fiyat filtresi (eğer maxPrice gönderildiyse)
        if ($maxPrice) {
            $photographers = array_filter($photographers, function($p) use ($maxPrice) {
                return $p->getPrice() <= (float)$maxPrice;
            });
            $photographers = array_values($photographers); // İndeksleri sıfırla
        }

        // JSON dizisine dönüştür
        $data = array_map(function($p) {
            return [
                'id' => $p->getId(),
                'name' => $p->getName(),
                'style' => $p->getStyle(),
                'city' => $p->getCity(),
                'price' => $p->getPrice(),
            ];
        }, $photographers);

        return $this->json($data, 200, [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type',
        ]);
    }
}