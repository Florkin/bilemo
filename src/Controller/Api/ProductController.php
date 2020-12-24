<?php

namespace App\Controller\Api;

use App\Handlers\ApiPaginatorHandler;
use App\Repository\BrandRepository;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

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
     * @OA\Response(
     *     response=200,
     *     description="Return first page of product list (Default: ?page=1&limit=12). Filter by brand by adding ?brand={brand_id}",
     * )
     * @param Request $request
     * @param PaginatorInterface $pager
     * @param ApiPaginatorHandler $apiPaginatorHandler
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $pager, ApiPaginatorHandler $apiPaginatorHandler): Response
    {
        $brandId = $request->query->get('brand');
        if (null != $brandId && null === $this->brandRepository->find($brandId)) {
            return new JsonResponse(["error" => "Cette marque n'existe pas"], 404);
        };
        $query = $this->productRepository->findAllQueryBuilder($brandId);
        $paginatedCollection = $apiPaginatorHandler->paginate($request, $query);

        if ($paginatedCollection) {
            $json = $this->serializer->serialize($paginatedCollection, 'json');
            return new Response($json, 200, array('Content-Type' => 'application/json'));
        }

        return new JsonResponse(["error" => "Il n'y a aucun produit"], 404);
    }

    /**
     * @Route("/products/{id}", name="product_show", methods={"GET"}, options={"expose" = true})
     * @param int $id
     * @OA\Response(
     *     response=200,
     *     description="Return single product details from his ID",
     * )
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
