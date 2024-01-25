<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\BrandRepository;
use App\Repository\ColorRepository;
use App\Repository\MaterialRepository;
use App\Repository\ProductRepository;
use App\Repository\TypeRepository;
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
    public function index(ProductRepository $productRepository): JsonResponse
    {
        try {
            $products = $productRepository->findAll();
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
    #[OA\Tag(name: 'Produits')]
    public function add(Request $request, EntityManagerInterface $entityManager, TypeRepository $typeRepository, BrandRepository $brandRepository, ColorRepository $colorRepository, MaterialRepository $materialRepository): JsonResponse
    {
        try {
            $faker = Factory::create();
            // On récupère les données du corpps de la requête
            // Que l'on transforme ensuite en tableau assoficatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Produit
            $product = new Product();
            $product->setName($data['name']);
            $product->setPrice($data['price']);
            $product->setDescription($data['description']);
            $product->setReference($faker->unique()->ean13);

            if (!empty($data['type'])) {
                $type = $typeRepository->find($data['type']);

                if (!$type) {
                    throw new \Exception("Le type renseigné n'existe pas");
                }
                $product->setType($type);
            }
            if (!empty($data['brand'])) {
                $brand = $brandRepository->find($data['type']);

                if (!$brand) {
                    throw new \Exception("La marque renseignée n'existe pas");
                }
                $product->setBrand($brand);
            }
            if (!empty($data['material'])) {
                $material = $materialRepository->find($data['material']);

                if (!$material) {
                    throw new \Exception("Le matériau renseigné n'existe pas");
                }
                $product->setMaterial($material);
            }
            if (!empty($data['color'])) {
                foreach ($data['color'] as $item) {
                    $color = $colorRepository->find($item);

                    if (!$color) {
                        throw new \Exception("La couleur renseignée n'existe pas");
                    }
                    $product->addColor($color);
                }
            }


            $entityManager->persist($product);
            $entityManager->flush();


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
    #[OA\Tag(name: 'Produits')]
    public function update(Product $product, Request $request, EntityManagerInterface $entityManager, TypeRepository $typeRepository, BrandRepository $brandRepository, ColorRepository $colorRepository, MaterialRepository $materialRepository): JsonResponse
    {
        try {
            // On récupère les données du corpps de la requête
            // Que l'on transforme ensuite en tableau assoficatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour modifier le produit
            $product->setName($data['name']);
            $product->setPrice($data['price']);
            $product->setDescription($data['description']);

            if (!empty($data['type'])) {
                $type = $typeRepository->find($data['type']);

                if (!$type) {
                    throw new \Exception("Le type renseigné n'existe pas");
                }
                $product->setType($type);
            }
            if (!empty($data['brand'])) {
                $brand = $brandRepository->find($data['type']);

                if (!$brand) {
                    throw new \Exception("La marque renseignée n'existe pas");
                }
                $product->setBrand($brand);
            }
            if (!empty($data['material'])) {
                $material = $materialRepository->find($data['material']);

                if (!$material) {
                    throw new \Exception("Le matériau renseigné n'existe pas");
                }
                $product->setMaterial($material);
            }
            if (!empty($data['color'])) {
                $product->resetColor();
                foreach ($data['color'] as $item) {
                    $color = $colorRepository->find($item);

                    if (!$color) {
                        throw new \Exception("La couleur renseignée n'existe pas");
                    }
                    $product->addColor($color);
                }
            }

            $entityManager->persist($product);
            $entityManager->flush();


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
