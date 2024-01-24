<?php

namespace App\Controller;

use App\Repository\PenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PenController extends AbstractController
{
    #[Route('/pens', name: 'app_pens')]
    public function index(PenRepository $PenRepository): JsonResponse
    {

        $pens = $PenRepository->findAll();

        return $this->json([
            'pens' => $pens,
        ]);
    }
}
