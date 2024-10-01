<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product', methods: 'GET')]
    public function index(ProductRepository $productRepository): Response
    {

        $products = $productRepository->findAll();

        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'products' => $products
        ]);
    }

    #[Route('/product/insert', name: 'app_product_insert', methods: 'GET')]
    public function insert(ProductRepository $productRepository): Response
    {

        $product = new Product("Dell XPS");

        $productRepository->save($product);

        return $this->render('product/insert.html.twig', [
            'controller_name' => 'ProductController',
            'insertion' => 'Good'
        ]);
    }

    #[Route('/product/update/{id}', name: 'app_product_update', methods: 'GET')]
    public function update(int $id, EntityManagerInterface $entityManager, ProductRepository $productRepository){
        // Récupérer product id (post / get) : DONE

        // vérifier au niveau de la base de données si le product ayant le id $id existe

        $product = $productRepository->find($id);


        // Si ça existe => mise à jour des données
        if (!$product) {
            return $this->render('product/update.html.twig', [
                'controller_name' => 'ProductController',
                'update' => 'Failed, Product not found'
            ]);
        }

        $product->setName("Dell XPS 2023");

        // Envoyer à la base de données les modifications

        $entityManager->flush();

        return $this->render('product/update.html.twig', [
            'controller_name' => 'ProductController',
            'update' => 'Good'
        ]);
    }

    #[Route('/product/delete/{id}', name: 'app_product_delete', methods: 'GET')]
    public function delete(int $id, ProductRepository $productRepository){
        // Verifier si le product est dans la base de données
        $product = $productRepository->find($id);
        // SI oui , on supprime
        if (!$product) {
            return $this->render('product/delete.html.twig', [
                'controller_name' => 'ProductController',
                'delete' => 'Failed, Product not found'
            ]);
        }

        $productRepository->delete($product);

        return $this->render('product/delete.html.twig', [
            'controller_name' => 'ProductController',
            'delete' => 'Good'
        ]);
    }
}
