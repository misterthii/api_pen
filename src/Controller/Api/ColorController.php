<?php

namespace App\Controller\Api;

use App\Entity\Color;
use OpenApi\Attributes as OA;
use App\Repository\ColorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name: 'api_')]
class ColorController extends AbstractController
{
    #[Route('/color', name: 'app_colors', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns all color',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Color::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'colors')]
    #[Security(name: 'Bearer')]
    public function all_color(ColorRepository $colorRepository): JsonResponse
    {
        $color = $colorRepository->findAll();

        return $this->json([
            'colors' => $color,
        ], context: [
            'groups' => ['pens:read']
        ]);
    }

    #[Route('/colors/{id}', name: 'app_color_by_id', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns color of id',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Color::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'colors')]
    #[Security(name: 'Bearer')]
    public function color_by_id(Color $color): JsonResponse
    {
        return $this->json([$color], context: [
            'groups' => ['pens:read'],
        ]);
    }

    #[Route('/colors', name: 'app_color_add', methods: ['POST'])]
    #[OA\Post(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Color::class,
                    groups: ['pens:create', 'pens:read']
                )
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns result of add color',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Color::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'colors')]
    #[Security(name: 'Bearer')]
    public function add(EntityManagerInterface $em, Request $request): JsonResponse
    {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Stylo
            $color = new Color();
            $color->setName($data['name']);

            $em->persist($color);
            $em->flush();

            return $this->json($color, context: [
                'groups' => ['pens:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    #[Route('/colors/{id}', name: 'app_color_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Color::class,
                    groups: ['pens:create', 'pens:read']
                )
            )
        )
    )]
    #[OA\Patch(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Color::class,
                    groups: ['pens:create', 'pens:read']
                )
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns result of update color',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Color::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'colors')]
    #[Security(name: 'Bearer')]
    public function update(Color $color, EntityManagerInterface $em, Request $request): JsonResponse
    {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Stylo
            $color = new Color();
            $color->setName($data['name']);

            $em->flush();

            return $this->json($color, context: [
                'groups' => ['pens:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/colors/{id}', name: 'app_color_delete', methods: ['DELETE'])]
    #[OA\Response(
        response: 200,
        description: 'Returns result of delete color',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Color::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'colors')]
    #[Security(name: 'Bearer')]
    public function delete(Color $color, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($color);
        $em->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Le color a bien été supprimé',
        ]);
    }
}
