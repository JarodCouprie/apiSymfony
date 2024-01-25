<?php

namespace App\Controller;

use App\Entity\Material;
use App\Repository\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class MaterialController extends AbstractController
{
    #[Route('/materials', name: 'app_materials', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne toutes les matières des produits.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Material::class, groups: ['product:read']))
        )
    )]
    #[OA\Tag(name: 'Matières')]
    #[Security(name: 'Bearer')]
    public function index(MaterialRepository $materialRepository): JsonResponse
    {
        try {
            $material = $materialRepository->findAll();
            return $this->json([
                'materials' => $material
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

    #[Route('/material/{id}', name: 'app_material_get', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne une matière.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Material::class, groups: ['product:read']))
        )
    )]
    #[OA\Tag(name: 'Matières')]
    public function get(Material $material): JsonResponse
    {
        try {
            return $this->json($material, context: [
                'groups' => ['product:read']
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }

    }

    #[Route('/materials', name: 'app_material_add', methods: ['POST'])]
    #[OA\Post(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Material::class,
                    groups: ['material:create']
                )
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Retourne la matière.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Material::class, groups: ['product:read']))
        )
    )]
    #[OA\Tag(name: 'Matières')]
    public function add(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $material = new Material();
            $material->setName($data['name']);


            $entityManager->persist($material);
            $entityManager->flush();


            return $this->json($material, context: [
                'groups' => ['product:read']
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/material/{id}', name: 'app_material_update', methods: ['PUT'])]
    #[OA\Put(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Material::class,
                    groups: ['material:update']
                )
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Retourne la matière.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Material::class, groups: ['product:read'])),
        )
    )]
    #[OA\Tag(name: 'Matières')]
    public function update(Material $material, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $material->setName($data['name']);

            $entityManager->persist($material);
            $entityManager->flush();


            return $this->json($material, context: [
                'groups' => ['product:read']
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/material/{id}', name: 'app_material_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Matières')]
    public function delete(Material $material, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $entityManager->remove($material);
            $entityManager->flush();
            return $this->json([
                'code' => 200,
                'message' => 'La matière a été supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }

    }
}
