<?php

namespace App\Controller\Api;

use App\Handlers\ApiPaginatorHandler;
use App\Repository\BrandRepository;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use App\Entity\Brand;

/**
 * @Route("/api")
 */
class BrandController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var BrandRepository
     */
    private $brandRepository;

    /**
     * brandController constructor.
     * @param SerializerInterface $serializer
     * @param BrandRepository $brandRepository
     */
    public function __construct(SerializerInterface $serializer, BrandRepository $brandRepository)
    {
        $this->serializer = $serializer;
        $this->brandRepository = $brandRepository;
    }

    /**
     * @Route("/brands", name="brand_index", methods={"GET"}, options={"expose" = true})
     * @param PaginatorInterface $pager
     * @param Request $request
     * @param ApiPaginatorHandler $apiPaginatorHandler
     * @return Response
     * @OA\Response(
     *     response=200,
     *     description="Return brands list",
     *     @Model(type=Brand::class)
     * )
     * @OA\Response(
     *     response=404,
     *     description="No brand found",
     * )
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Number of items per page",
     *     @OA\Schema(type="int")
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Page number to query",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Brands")
     * @Security(name="Bearer")
     */
    public function index(PaginatorInterface $pager, Request $request, ApiPaginatorHandler $apiPaginatorHandler): Response
    {
        $query = $this->brandRepository->findAllQueryBuilder();
        $paginatedCollection = $apiPaginatorHandler->paginate($request, $query);

        if ($paginatedCollection) {
            $json = $this->serializer->serialize($paginatedCollection, 'json');
            return new Response($json, 200, array('Content-Type' => 'application/json'));
        }

        return new JsonResponse(["error" => "Il n'y a aucune marque"], 404);
    }

    /**
     * @Route("/brands/{id}", name="brand_show", methods={"GET"}, options={"expose" = true})
     * @OA\Response(
     *     response=200,
     *     description="Return brand details",
     *     @Model(type=Brand::class)
     * )
     * @OA\Response(
     *     response=404,
     *     description="Brand not found for this ID",
     * )
     * @OA\Tag(name="Brands")
     * @Security(name="Bearer")
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $brand = $this->brandRepository->find($id);

        if ($brand) {
            $brandJSON = $this->serializer->serialize($brand, 'json');
            return new Response($brandJSON, 200, array('Content-Type' => 'application/json'));
        }

        return new JsonResponse(["error" => "Cette marque n'existe pas"], 404);
    }
}
