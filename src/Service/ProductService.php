<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\BrandRepository;
use App\Repository\ColorRepository;
use App\Repository\MaterialRepository;
use App\Repository\ProductRepository;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;

class ProductService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MaterialRepository     $materialRepository,
        private readonly TypeRepository         $typeRepository,
        private readonly ColorRepository        $colorRepository,
        private readonly BrandRepository        $brandRepository,
        private readonly ProductRepository      $productRepository
    )
    {
    }

    public function getAllProducts(): array
    {
        return $this->productRepository->findAll();
    }

    public function create(array $data): Product
    {
        $faker = Factory::create();

        // On traite les données pour créer un nouveau Produit
        $product = new Product();
        $product->setName($data['name']);
        $product->setPrice($data['price']);
        $product->setDescription($data['description']);
        $product->setReference($faker->unique()->ean13);

        if (!empty($data['type'])) {
            $type = $this->typeRepository->find($data['type']);

            if (!$type) {
                throw new \Exception("Le type renseigné n'existe pas");
            }
            $product->setType($type);
        }
        if (!empty($data['brand'])) {
            $brand = $this->brandRepository->find($data['type']);

            if (!$brand) {
                throw new \Exception("La marque renseignée n'existe pas");
            }
            $product->setBrand($brand);
        }
        if (!empty($data['material'])) {
            $material = $this->materialRepository->find($data['material']);

            if (!$material) {
                throw new \Exception("Le matériau renseigné n'existe pas");
            }
            $product->setMaterial($material);
        }
        if (!empty($data['color'])) {
            foreach ($data['color'] as $item) {
                $color = $this->colorRepository->find($item);

                if (!$color) {
                    throw new \Exception("La couleur renseignée n'existe pas");
                }
                $product->addColor($color);
            }
        }


        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }

    public function update(Product $product, array $data): void
    {
        if (!empty($data['name'])) {
            $product->setName($data['name']);
        }

        if (!empty($data['price'])) {
            $product->setPrice($data['price']);
        }

        if (!empty($data['description'])) {
            $product->setDescription($data['description']);
        }

        if (!empty($data['type'])) {
            $type = $this->typeRepository->find($data['type']);

            if (!$type) {
                throw new \Exception("Le type renseigné n'existe pas");
            }
            $product->setType($type);
        }
        if (!empty($data['brand'])) {
            $brand = $this->brandRepository->find($data['type']);

            if (!$brand) {
                throw new \Exception("La marque renseignée n'existe pas");
            }
            $product->setBrand($brand);
        }
        if (!empty($data['material'])) {
            $material = $this->materialRepository->find($data['material']);

            if (!$material) {
                throw new \Exception("Le matériau renseigné n'existe pas");
            }
            $product->setMaterial($material);
        }
        if (!empty($data['color'])) {
            $product->resetColor();
            foreach ($data['color'] as $item) {
                $color = $this->colorRepository->find($item);

                if (!$color) {
                    throw new \Exception("La couleur renseignée n'existe pas");
                }
                $product->addColor($color);
            }
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
}