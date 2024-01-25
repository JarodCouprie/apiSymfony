<?php

namespace App\Controller;

use App\Entity\Color;
use App\Repository\ColorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ColorController extends AbstractController
{
    #[Route('/colors', name: 'app_colors', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne toutes les couleurs des produits.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Color::class, groups: ['product:read']))
        )
    )]
    #[OA\Tag(name: 'Couleurs')]
    #[Security(name: 'Bearer')]
    public function index(ColorRepository $colorRepository): JsonResponse
    {
        try {
            $color = $colorRepository->findAll();
            return $this->json([
                'colors' => $color
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

    #[Route('/color/{id}', name: 'app_color_get', methods: ['GET'])]
    #[OA\Tag(name: 'Couleurs')]
    public function get(Color $color): JsonResponse
    {
        try {
            return $this->json($color, context: [
                'groups' => ['product:read']
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }

    }

    #[Route('/colors', name: 'app_color_add', methods: ['POST'])]
    #[OA\Tag(name: 'Couleurs')]
    public function add(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $color = new Color();
            $color->setName($data['name']);


            $entityManager->persist($color);
            $entityManager->flush();


            return $this->json($color, context: [
                'groups' => ['product:read']
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/color/{id}', name: 'app_color_update', methods: ['PUT'])]
    #[OA\Tag(name: 'Couleurs')]
    public function update(Color $color, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $color->setName($data['name']);

            $entityManager->persist($color);
            $entityManager->flush();


            return $this->json($color, context: [
                'groups' => ['product:read']
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/color/{id}', name: 'app_color_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Couleurs')]
    public function delete(Color $color, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $entityManager->remove($color);
            $entityManager->flush();
            return $this->json([
                'code' => 200,
                'message' => 'La couleur a été supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }

    }
}
