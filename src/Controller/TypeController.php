<?php

namespace App\Controller;


use App\Entity\Type;
use OpenApi\Attributes as OA;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;

use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name: 'api_')]
class TypeController extends AbstractController
{
    #[Route('/type', name: 'app_types', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns all type',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Type::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'types')]
    #[Security(name: 'Bearer')]
    public function all_type(TypeRepository $typeRepository): JsonResponse
    {
        $type = $typeRepository->findAll();

        return $this->json([
            'type' => $type,
        ], context: [
            'groups' => ['pens:read']
        ]);
    }

    #[Route('/types/{id}', name: 'app_type_by_id', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns type of id',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Type::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'types')]
    #[Security(name: 'Bearer')]
    public function type_by_id(Type $type): JsonResponse
    {
        return $this->json([$type], context: [
            'groups' => ['pens:read'],
        ]);
    }

    #[Route('/types', name: 'app_types_add', methods: ['POST'])]
    #[OA\Post(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Type::class, 
                    groups: ['pens:create','pens:read']
                )
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns result of add type',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Type::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'types')]
    #[Security(name: 'Bearer')]
    public function add(EntityManagerInterface $em, Request $request): JsonResponse
    {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Stylo
            $type = new Type();
            $type->setName($data['name']);

            $em->persist($type);
            $em->flush();

            return $this->json($type, context: [
                'groups' => ['pens:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    #[Route('/types/{id}', name: 'app_types_update', methods: ['PUT', 'PATCH'])]
    #[OA\Response(
        response: 200,
        description: 'Returns result of update type',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Type::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'types')]
    #[OA\Put(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Type::class, 
                    groups: ['pens:create','pens:read']
                )
            )
        )
    )]
    #[OA\Patch(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Type::class, 
                    groups: ['pens:create','pens:read']
                )
            )
        )
    )]
    #[Security(name: 'Bearer')]
    public function update(Type $type, EntityManagerInterface $em, Request $request): JsonResponse
    {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Stylo
            $type = new Type();
            $type->setName($data['name']);

            $em->flush();

            return $this->json($type, context: [
                'groups' => ['pens:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/types/{id}', name: 'app_types_delete', methods: ['DELETE'])]
    #[OA\Response(
        response: 200,
        description: 'Returns result of delete type',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Type::class, groups: ['pens:read']))
        )
    )]
    #[OA\Tag(name: 'types')]
    #[Security(name: 'Bearer')]
    public function delete(Type $type, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($type);
        $em->flush();

        return $this->json([
            'code' => 200,
            'message' => 'La marque a bien été supprimé',
        ]);
    }
}
