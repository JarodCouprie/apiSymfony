<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\BrandRepository;
use App\Repository\ColorRepository;
use App\Repository\MaterialRepository;
use App\Repository\ProductRepository;
use App\Repository\TypeRepository;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;


#[Route('/api')]
class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_products', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne tous les produits.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Product::class, groups: ['product:read']))
        )
    )]
    #[OA\Tag(name: 'Produits')]
    #[Security(name: 'Bearer')]
    public function index(ProductService $productService): JsonResponse
    {
        try {
            $products = $productService->getAllProducts();
            return $this->json([
                'products' => $products
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

    #[Route('/product/{id}', name: 'app_product_get', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne un produit.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Product::class, groups: ['product:read']))
        )
    )]
    #[OA\Tag(name: 'Produits')]
    public function get(Product $product): JsonResponse
    {
        try {
            return $this->json($product, context: [
                'groups' => ['product:read']
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }

    }

    #[Route('/products', name: 'app_product_add', methods: ['POST'])]
    #[OA\Post(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Product::class,
                    groups: ['product:create']
                )
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Retourne le produit.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Product::class, groups: ['product:read']))
        )
    )]
    #[OA\Tag(name: 'Produits')]
    public function add(Request $request, ProductService $productService): JsonResponse
    {
        try {
            // On récupère les données du corpps de la requête
            // Que l'on transforme ensuite en tableau assoficatif
            $data = json_decode($request->getContent(), true);
            $product = $productService->create($data);

            return $this->json($product, context: [
                'groups' => ['product:read']
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/product/{id}', name: 'app_product_update', methods: ['PUT'])]
    #[OA\Put(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Product::class,
                    groups: ['product:update']
                )
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Retourne le produit.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Product::class, groups: ['product:read'])),
        )
    )]
    #[OA\Tag(name: 'Produits')]
    public function update(Product $product, Request $request, ProductService $productService): JsonResponse
    {
        try {
            // On récupère les données du corpps de la requête
            // Que l'on transforme ensuite en tableau assoficatif
            $data = json_decode($request->getContent(), true);

            $productService->update($product, $data);

            return $this->json($product, context: [
                'groups' => ['product:read']
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/product/{id}', name: 'app_product_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Produits')]
    public function delete(Product $product, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $entityManager->remove($product);
            $entityManager->flush();
            return $this->json([
                'code' => 200,
                'message' => 'Le produit a été supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }

    }
}
