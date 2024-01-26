<?php
namespace App\Controller\Api;

use App\Entity\Brand;
use OpenApi\Attributes as OA;
use App\Repository\BrandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
#[Route('/api', name: 'api_')]
class BrandController extends AbstractController
{
    #[Route('/brand', name: 'app_brands', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns all brand',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Brand::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'brands')]
    #[Security(name: 'Bearer')]
    public function all_brand(BrandRepository $brandRepository): JsonResponse
    {
        $brand = $brandRepository->findAll();

        return $this->json([
            'brand' => $brand,
        ], context: [
            'groups' => ['pens:read']
        ]);
    }

    #[Route('/brands/{id}', name: 'app_brand_by_id', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns brand of id',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Brand::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'brands')]
    #[Security(name: 'Bearer')]
    public function brand_by_id(Brand $brand): JsonResponse
    {
        return $this->json([$brand], context: [
            'groups' => ['pens:read'],
        ]);
    }

    #[Route('/brands', name: 'app_brands_add', methods: ['POST'])]
    #[OA\Post(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Brand::class, 
                    groups: ['pens:create','pens:read']
                )
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns result of add brand',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Brand::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'brands')]
    #[Security(name: 'Bearer')]
    public function add(EntityManagerInterface $em, Request $request): JsonResponse
    {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Stylo
            $brand = new Brand();
            $brand->setName($data['name']);

            $em->persist($brand);
            $em->flush();

            return $this->json($brand, context: [
                'groups' => ['pens:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    #[Route('/brands/{id}', name: 'app_brands_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Brand::class, 
                    groups: ['pens:create','pens:read']
                )
            )
        )
    )]
    #[OA\Patch(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Brand::class, 
                    groups: ['pens:create','pens:read']
                )
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns result of update brand',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Brand::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'brands')]
    #[Security(name: 'Bearer')]
    public function update(Brand $brand, EntityManagerInterface $em, Request $request): JsonResponse
    {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Stylo
            $brand = new brand();
            $brand->setName($data['name']);

            $em->flush();

            return $this->json($brand, context: [
                'groups' => ['pens:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/brands/{id}', name: 'app_brands_delete', methods: ['DELETE'])]
    #[OA\Response(
        response: 200,
        description: 'Returns result of delete brand',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Brand::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'brands')]
    #[Security(name: 'Bearer')]
    public function delete(Brand $brand, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($brand);
        $em->flush();

        return $this->json([
            'code' => 200,
            'message' => 'La marque a bien été supprimé',
        ]);
    }
}
