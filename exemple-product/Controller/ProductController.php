<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product', methods: 'GET')]
    public function index(): Response
    {
        $product1 = new Product(1, 'dell xps');
        $product2 = new Product(2, 'Macbook air 2020');
        $product3 = new Product(3, 'dell latitude');

        $products = [$product1, $product2, $product3];

        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'products' => $products
        ]);
    }
}
