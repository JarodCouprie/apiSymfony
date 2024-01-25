<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Repository\BrandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/api')]
class BrandController extends AbstractController
{
    #[Route('/brands', name: 'app_brand', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne toutes les marques.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Brand::class, groups: ['product:read']))
        )
    )]
    #[OA\Tag(name: 'Marques')]
    #[Security(name: 'Bearer')]
    public function index(BrandRepository $brandRepository): Response
    {
        try {
            $brand = $brandRepository->findAll();
            return $this->json([
                'brands' => $brand
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

    #[Route('/brand/{id}', name: 'app_brand_get', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne une marque.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Brand::class, groups: ['product:read']))
        )
    )]
    #[OA\Tag(name: 'Marques')]
    public function get(Brand $brand): JsonResponse
    {
        try {
            return $this->json($brand, context: [
                'groups' => ['product:read']
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }

    }

    #[Route('/brands', name: 'app_brand_add', methods: ['POST'])]
    #[OA\Post(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Brand::class,
                    groups: ['brand:create']
                )
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Retourne la marque.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Brand::class, groups: ['product:read']))
        )
    )]
    #[OA\Tag(name: 'Marques')]
    public function add(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $brand = new Brand();
            $brand->setName($data['name']);


            $entityManager->persist($brand);
            $entityManager->flush();


            return $this->json($brand, context: [
                'groups' => ['product:read']
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/brand/{id}', name: 'app_brand_update', methods: ['PUT'])]
    #[OA\Put(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Brand::class,
                    groups: ['brand:update']
                )
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Retourne la marque.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Brand::class, groups: ['product:read'])),
        )
    )]
    #[OA\Tag(name: 'Marques')]
    public function update(Brand $brand, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $brand->setName($data['name']);

            $entityManager->persist($brand);
            $entityManager->flush();


            return $this->json($brand, context: [
                'groups' => ['product:read']
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/brand/{id}', name: 'app_brand_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Marques')]
    public function delete(Brand $brand, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $entityManager->remove($brand);
            $entityManager->flush();
            return $this->json([
                'code' => 200,
                'message' => 'La marque a été supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }

    }

}
