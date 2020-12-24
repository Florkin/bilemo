<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Handlers\ApiPaginatorHandler;
use App\Handlers\Forms\FormErrorsHandler;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Annotations as OA;

/**
 * @Route("/api")
 */
class UserController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var UserRepository
     */
    private $UserRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var SymfonySerializer
     */
    private $deserializer;

    /**
     * ProductController constructor.
     * @param SerializerInterface $serializer
     * @param SymfonySerializer $deserializer
     * @param UserRepository $UserRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(SerializerInterface $serializer, SymfonySerializer $deserializer, UserRepository $UserRepository, EntityManagerInterface $entityManager)
    {
        $this->serializer = $serializer;
        $this->UserRepository = $UserRepository;
        $this->entityManager = $entityManager;
        $this->deserializer = $deserializer;
    }

    /**
     * @Route("/users", name="user_index", methods={"GET"}, options={"expose" = true})
     * @param PaginatorInterface $pager
     * @param Request $request
     * @param ApiPaginatorHandler $apiPaginatorHandler
     * @return Response
     * @OA\Response(
     *     response=200,
     *     description="Return first page of user list (Default: ?page=1&limit=12). Only users from your account.",
     * )
     */
    public function index(PaginatorInterface $pager, Request $request, ApiPaginatorHandler $apiPaginatorHandler): Response
    {
        $query = $this->UserRepository->findUsersFromClientId($this->getUser()->getId());
        $paginatedCollection = $apiPaginatorHandler->paginate($request, $query);

        if ($paginatedCollection) {
            $json = $this->serializer->serialize($paginatedCollection, 'json');
            return new Response($json, 200, array('Content-Type' => 'application/json'));
        }

        return new JsonResponse(["error" => "Il n'y a aucun utilisateur"], 404);
    }

    /**
     * @Route("/users/{id}", name="user_show", methods={"GET"}, options={"expose" = true})
     * @OA\Response(
     *     response=200,
     *     description="Return single user details.",
     * )
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $user = $this->UserRepository->find($id);

        if (!$user) {
            return new JsonResponse(["error" => "Cet utilisateur n'existe pas"], 404);
        }

        $this->denyAccessUnlessGranted('SHOW_USER', $user);
        $json = $this->serializer->serialize($user, 'json');
        return new Response($json, 200, array('Content-Type' => 'application/json'));


    }

    /**
     * @Route("/users", name="user_new", methods={"POST"}, options={"expose" = true})
     * @param Request $request
     * @param ClientRepository $clientRepository
     * @param ValidatorInterface $validator
     * @OA\Response(
     *     response=200,
     *     description="Add new user.",
     * )
     * @return Response
     */
    public function new(Request $request, ClientRepository $clientRepository, ValidatorInterface $validator): Response
    {
        $user = $this->deserializer->deserialize($request->getContent(), User::class, "json");
        $user->setClient($this->getUser());

        $errors = $validator->validate($user);

        if (!count($errors) > 0) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $json = $this->serializer->serialize(["success" => "Utilisateur enregistré", 'item' => $user], 'json');
            return new Response($json, 200, array('Content-Type' => 'application/json'));
        }
        $json = $this->serializer->serialize(["errors" => $errors], 'json');
        return new Response($json, 202, array('Content-Type' => 'application/json'));
    }

    /**
     * @Route("/users/{id}", name="user_edit", methods={"PATCH"}, options={"expose" = true})
     * @param int $id
     * @param Request $request
     * @param FormErrorsHandler $errorsHandler
     * @param ValidatorInterface $validator
     * @OA\Response(
     *     response=200,
     *     description="Edit user with id_user = {id}.",
     * )
     * @return Response
     */
    public function edit(int $id, Request $request, FormErrorsHandler $errorsHandler, ValidatorInterface $validator): Response
    {
        $user = $this->UserRepository->find($id);

        if (!$user) {
            return new JsonResponse(["Error" => "L'utilisateur n'existe pas"], 404);
        }
        $this->denyAccessUnlessGranted('EDIT_USER', $user);

        $this->deserializer->deserialize($request->getContent(), User::class, "json", [
            'object_to_populate' => $user,
        ]);

        $errors = $validator->validate($user);
        if (!count($errors) > 0) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $json = $this->serializer->serialize(["success" => "Modifications enregistrées", 'item' => $user], 'json');
            return new Response($json, 200, array('Content-Type' => 'application/json'));
        }
        $json = $this->serializer->serialize(["errors" => $errors, 'item' => $user], 'json');
        return new Response($json, 202, array('Content-Type' => 'application/json'));
    }


    /**
     * @Route("/users/{id}", name="delete_user", methods={"DELETE"}, options={"expose" = true})
     * @param int $id
     * @param Request $request
     * @OA\Response(
     *     response=200,
     *     description="Delete user with id_user = {id}.",
     * )
     * @return Response
     */
    public function delete(int $id, Request $request): Response
    {
        $user = $this->UserRepository->find($id);
        if (!$user) {
            return new JsonResponse(["error" => "L'utilisateur n'existe pas"], 404);
        }
        $this->denyAccessUnlessGranted('DELETE_USER', $user);
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        return new JsonResponse(["success" => "L'utilisateur a bien été supprimé"], 200);
    }
}
