<?php

namespace App\Controller\Api;

use App\Repository\BrandRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @return Response
     */
    public function index(): Response
    {
        $brands = $this->brandRepository->findAll();

        if ($brands) {
            $context = SerializationContext::create()->setGroups(['list_brand']);
            $brandsJSON = $this->serializer->serialize($brands, 'json', $context);
            return new Response($brandsJSON, 200, array('Content-Type' => 'application/json'));
        }

        return new JsonResponse(["error" => "Il n'y a aucune marque"], 404);
    }
}
