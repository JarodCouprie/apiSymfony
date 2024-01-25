<?php

namespace App\Controller;

use App\Entity\Type;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/api')]
class TypeController extends AbstractController
{
    #[Route('/types', name: 'app_types', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne tous les types des produits.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Type::class, groups: ['product:read']))
        )
    )]
    #[OA\Tag(name: 'Types')]
    #[Security(name: 'Bearer')]
    public function index(TypeRepository $typeRepository): JsonResponse
    {
        try {
            $type = $typeRepository->findAll();
            return $this->json([
                'types' => $type
            ], context: [
                'groups' => ['product:read']
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/type/{id}', name: 'app_type_get', methods: ['GET'])]
    #[OA\Tag(name: 'Types')]
    public function get(Type $type): JsonResponse
    {
        try {
            return $this->json($type, context: [
                'groups' => ['product:read']
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }

    }

    #[Route('/types', name: 'app_type_add', methods: ['POST'])]
    #[OA\Tag(name: 'Types')]
    public function add(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $type = new Type();
            $type->setName($data['name']);


            $entityManager->persist($type);
            $entityManager->flush();


            return $this->json($type, context: [
                'groups' => ['product:read']
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/type/{id}', name: 'app_type_update', methods: ['PUT'])]
    #[OA\Tag(name: 'Types')]
    public function update(Type $type, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $type->setName($data['name']);

            $entityManager->persist($type);
            $entityManager->flush();


            return $this->json($type, context: [
                'groups' => ['product:read']
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/type/{id}', name: 'app_type_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Types')]
    public function delete(Type $type, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $entityManager->remove($type);
            $entityManager->flush();
            return $this->json([
                'code' => 200,
                'message' => 'Le type a été supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }

    }
}
