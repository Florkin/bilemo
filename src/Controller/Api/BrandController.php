<?php

namespace App\Controller\Api;

use App\Repository\BrandRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @return Response
     */
    public function index(PaginatorInterface $pager, Request $request): Response
    {
        $page = $pager->paginate(
            $this->brandRepository->findAll(),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 12)
        );

        if ($page->count() > 0) {
            $context = SerializationContext::create()->setGroups(['list_brand']);
            $brandsJSON = $this->serializer->serialize($page->getItems(), 'json', $context);
            return new Response($brandsJSON, 200, array('Content-Type' => 'application/json'));
        }

        return new JsonResponse(["error" => "Il n'y a aucune marque"], 404);
    }

    /**
     * @Route("/brands/{id}", name="brand_show", methods={"GET"}, options={"expose" = true})
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $brand = $this->brandRepository->find($id);

        if ($brand) {
            $context = SerializationContext::create()->setGroups(['details_brand']);
            $brandJSON = $this->serializer->serialize($brand, 'json', $context);
            return new Response($brandJSON, 200, array('Content-Type' => 'application/json'));
        }

        return new JsonResponse(["error" => "Cette marque n'existe pas"], 404);
    }
}
