<?php

namespace App\Controller;

use App\Entity\Material;
use OpenApi\Attributes as OA;
use App\Repository\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
#[Route('/api', name: 'api_')]
class MaterialController extends AbstractController
{
    #[Route('/material', name: 'app_materials', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns all material',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Material::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'materials')]
    #[Security(name: 'Bearer')]
    public function all_material(MaterialRepository $materialRepository): JsonResponse
    {
        $material = $materialRepository->findAll();

        return $this->json([
            'materials' => $material,
        ], context: [
            'groups' => ['pens:read']
        ]);
    }

    #[Route('/materials/{id}', name: 'app_material_by_id', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns material of id',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Material::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'materials')]
    #[Security(name: 'Bearer')]
    public function material_by_id(Material $material): JsonResponse
    {
        return $this->json([$material], context: [
            'groups' => ['pens:read'],
        ]);
    }

    #[Route('/materials', name: 'app_material_add', methods: ['POST'])]
    #[OA\Post(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Material::class, 
                    groups: ['pens:create','pens:read']
                )
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns result of add material',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Material::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'materials')]
    #[Security(name: 'Bearer')]
    public function add(EntityManagerInterface $em, Request $request): JsonResponse
    {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Stylo
            $material = new Material();
            $material->setName($data['name']);

            $em->persist($material);
            $em->flush();

            return $this->json($material, context: [
                'groups' => ['pens:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    #[Route('/materials/{id}', name: 'app_material_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Material::class, 
                    groups: ['pens:create','pens:read']
                )
            )
        )
    )]
    #[OA\Patch(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Material::class, 
                    groups: ['pens:create','pens:read']
                )
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns result of update material',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Material::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'materials')]
    #[Security(name: 'Bearer')]
    public function update(Material $material, EntityManagerInterface $em, Request $request): JsonResponse
    {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Stylo
            $material = new Material();
            $material->setName($data['name']);

            $em->flush();

            return $this->json($material, context: [
                'groups' => ['pens:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/materials/{id}', name: 'app_material_delete', methods: ['DELETE'])]
    #[OA\Response(
        response: 200,
        description: 'Returns result of delete material',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Material::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'materials')]
    #[Security(name: 'Bearer')]
    public function delete(Material $material, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($material);
        $em->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Le material a bien été supprimé',
        ]);
    }
}
