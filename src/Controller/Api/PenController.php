<?php

namespace App\Controller\Api;

use Faker\Factory;
use App\Entity\Pen;
use OpenApi\Attributes as OA;
use App\Repository\PenRepository;
use App\Repository\TypeRepository;
use App\Repository\BrandRepository;
use App\Repository\ColorRepository;
use App\Repository\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name: 'api_')]
class PenController extends AbstractController
{
    #[Route('/pens', name: 'app_pens', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns all pen',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Pen::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'pens')]
    #[Security(name: 'Bearer')]
    public function all_pens(PenRepository $penRepository): JsonResponse
    {
        $pens = $penRepository->findAll();

        return $this->json([
            'pens' => $pens,
        ], context: [
            'groups' => ['pens:read']
        ]);
    }

    #[Route('/pens/{id}', name: 'app_pen_by_id', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns pen of id',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Pen::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'pens')]
    #[Security(name: 'Bearer')]
    public function pen_by_id(Pen $pen): JsonResponse
    {
        return $this->json([$pen], context: [
            'groups' => ['pens:read'],
        ]);
    }

    #[Route('/pens', name: 'app_pens_add', methods: ['POST'])]
    #[OA\Post(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Pen::class,
                    groups: ['pens:create', 'pens:read']
                )
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns result of add pen',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Pen::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'pens')]
    #[Security(name: 'Bearer')]
    public function add(EntityManagerInterface $em, Request $request, TypeRepository $typeRepository, MaterialRepository $materialRepository, ColorRepository $colorRepository, BrandRepository $brandRepository): JsonResponse
    {
        try {
            $content = $request->getContent();

            if ($content === null) {
                throw new \Exception("La requête ne contient pas de corps");
            }
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            try {
                $data = json_decode($request->getContent(), true);
            } catch (\JsonException $e) {
                throw new \Exception('Erreur de décodage JSON : ' . $e->getMessage());
            }

            if ($data === null) {
                throw new \Exception("Le corps de la requête n'est pas au format JSON ou est mal formé");
            }
            $faker = Factory::create();

            // On traite les données pour créer un nouveau Stylo
            $pen = new Pen();
            $pen->setName($data["name"]);
            $pen->setPrice($data["price"]);
            $pen->setDescription($data["description"]);
            $pen->setRef($faker->unique()->ean13);

            // Récupération du type de stylo
            if (!empty($data['type'])) {
                $type = $typeRepository->find($data["type"]);
                if (!$type)
                    throw new \Exception("Le type renseigné n'existe pas");

                $pen->setType($type);
            }

            // Récupération du matériel
            if (!empty($data["material"])) {
                $material = $materialRepository->find($data["material"]);

                if (!$material)
                    throw new \Exception("Le matériel renseigné n'existe pas");

                $pen->setMaterial($material);
            }
            // Récupération du color
            if (!empty($data['color'])) {
                foreach ($data['color'] as $colorId) {
                    
                    $color = $colorRepository->find($colorId);
                    if (!$color) {
                        throw new \Exception("La couleur renseignée n'existe pas");
                    }
                    $pen->addColor($color);
                }
            }
            // Récupération du marque
            if (!empty($data['brand'])) {
                $brand = $brandRepository->find($data["brand"]);

                if (!$brand)
                    throw new \Exception("Le matériel renseigné n'existe pas");

                $pen->setBrand($brand);
            }
            $em->persist($pen);
            $em->flush();

            return $this->json($pen, context: [
                'groups' => ['pens:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    #[Route('/pens/{id}', name: 'app_pens_update', methods: ['PUT', 'PATCH'])]
    #[OA\Response(
        response: 200,
        description: 'Returns result of update pen',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Pen::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'pens')]
    #[OA\Put(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Pen::class,
                    groups: ['pens:create', 'pens:read']
                )
            )
        )
    )]
    #[OA\Patch(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Pen::class,
                    groups: ['pens:create', 'pens:read']
                )
            )
        )
    )]
    #[Security(name: 'Bearer')]
    public function update(Pen $pen, EntityManagerInterface $em, Request $request, TypeRepository $typeRepository, MaterialRepository $materialRepository, ColorRepository $colorRepository, BrandRepository $brandRepository): JsonResponse
    {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Stylo
            
            $pen->setName($data['name']);
            $pen->setPrice($data['price']);
            $pen->setDescription($data['description']);
            // Récupération du type de stylo
            if (!empty($data['type'])) {
                $type = $typeRepository->find($data['type']);

                if (!$type)
                    throw new \Exception("Le type renseigné n'existe pas");

                $pen->setType($type);
            }

            // Récupération du matériel
            if (!empty($data['material'])) {
                $material = $materialRepository->find($data['material']);

                if (!$material)
                    throw new \Exception("Le matériel renseigné n'existe pas");

                $pen->setMaterial($material);
            }

            if (!empty($data['color'])) {
                foreach ($data['color'] as $color) {
                    $color = $colorRepository->find($color);
                    if (!$color) {
                        throw new \Exception("La couleur renseigné n'existe pas");
                    }


                    $pen->addColor($color);
                }
            }

            if (!empty($data['brand'])) {
                $brand = $brandRepository->find($data['brand']);

                if (!$brand)
                    throw new \Exception("L'entreprise renseigné n'existe pas");

                $pen->setBrand($brand);
            }

            $em->flush();

            return $this->json($pen, context: [
                'groups' => ['pens:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/pens/{id}', name: 'app_pens_delete', methods: ['DELETE'])]
    #[OA\Response(
        response: 200,
        description: 'Returns result of delete pen',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Pen::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'pens')]
    #[Security(name: 'Bearer')]
    public function delete(Pen $pen, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($pen);
        $em->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Le stylo a bien été supprimé',
        ]);
    }
}
