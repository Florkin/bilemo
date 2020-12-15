<?php

namespace App\Controller\Api;

use App\Repository\BrandRepository;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * @var BrandRepository
     */
    private $brandRepository;

    /**
     * ProductController constructor.
     * @param SerializerInterface $serializer
     * @param ProductRepository $productRepository
     * @param BrandRepository $brandRepository
     */
    public function __construct(SerializerInterface $serializer, ProductRepository $productRepository, BrandRepository $brandRepository)
    {
        $this->serializer = $serializer;
        $this->productRepository = $productRepository;
        $this->brandRepository = $brandRepository;
    }

    /**
     * @Route("/products", name="product_index", methods={"GET"}, options={"expose" = true})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $brandId = $request->query->get('brand');
        if (null != $brandId && null === $this->brandRepository->find($brandId)) {
            return new JsonResponse(["error" => "Cette marque n'existe pas"], 404);
        };

        $products = $this->productRepository->findAllQueryBuilder($brandId);
        if ($products) {
            $context = SerializationContext::create()->setGroups(['list_product']);
            $productsJSON = $this->serializer->serialize($products, 'json', $context);
            return new Response($productsJSON, 200, array('Content-Type' => 'application/json'));
        }

        return new JsonResponse(["error" => "Il n'y a aucun produit"], 404);
    }

    /**
     * @Route("/products/{id}", name="product_show", methods={"GET"}, options={"expose" = true})
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $product = $this->productRepository->find($id);

        if ($product) {
            $context = SerializationContext::create()->setGroups(['details_product']);
            $productJSON = $this->serializer->serialize($product, 'json', $context);
            return new Response($productJSON, 200, array('Content-Type' => 'application/json'));
        }

        return new JsonResponse(["error" => "Ce produit n'existe pas"], 404);
    }
}
