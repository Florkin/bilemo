<?php

namespace App\Controller\Api;

use App\Repository\ProductRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class ProductController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * ProductController constructor.
     * @param SerializerInterface $serializer
     * @param ProductRepository $productRepository
     */
    public function __construct(SerializerInterface $serializer, ProductRepository $productRepository)
    {
        $this->serializer = $serializer;
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/products", name="product_index", methods={"GET"}, options={"expose" = true})
     * @return Response
     */
    public function index(): Response
    {
        $products = $this->productRepository->findAll();

        if ($products) {
            $productsJSON = $this->serializer->serialize($products, 'json');
            return new Response($productsJSON, 200, array('Content-Type' => 'application/json'));
        }

        return new JsonResponse(["error" => "Il n'y a aucun produit"], 404);
    }

    /**
     * @Route("/products/{id}", name="show_product", methods={"GET"}, options={"expose" = true})
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $product = $this->productRepository->find($id);

        if ($product) {
            $productJSON = $this->serializer->serialize($product, 'json');
            return new Response($productJSON, 200, array('Content-Type' => 'application/json'));
        }

        return new JsonResponse(["error" => "Ce produit n'existe pas"], 404);
    }
}
