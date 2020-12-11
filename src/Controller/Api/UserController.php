<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Handlers\Forms\FormErrorsHandler;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface as Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @var Serializer
     */
    private $deserializer;

    /**
     * ProductController constructor.
     * @param SerializerInterface $serializer
     * @param Serializer $deserializer
     * @param UserRepository $UserRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(SerializerInterface $serializer, Serializer $deserializer, UserRepository $UserRepository, EntityManagerInterface $entityManager)
    {
        $this->serializer = $serializer;
        $this->UserRepository = $UserRepository;
        $this->entityManager = $entityManager;
        $this->deserializer = $deserializer;
    }

    /**
     * @Route("/users", name="user_index", methods={"GET"}, options={"expose" = true})
     * @return Response
     */
    public function index(): Response
    {
        $users = $this->UserRepository->findUsersFromClientId($this->getUser()->getId());

        if ($users) {
            $context = SerializationContext::create()->setGroups(['list_user']);
            $usersJSON = $this->serializer->serialize($users, 'json', $context);
            return new Response($usersJSON, 200, array('Content-Type' => 'application/json'));
        }

        return new JsonResponse(["error" => "Il n'y a aucun utilisateur"], 404);
    }

    /**
     * @Route("/users/{id}", name="show_user", methods={"GET"}, options={"expose" = true})
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $user = $this->UserRepository->find($id);

        if ($user) {
            if ($this->isGranted('SHOW_USER', $user)) {
                $context = SerializationContext::create()->setGroups(['details_user']);
                $userJSON = $this->serializer->serialize($user, 'json', $context);
                return new Response($userJSON, 200, array('Content-Type' => 'application/json'));
            }
            return new JsonResponse(["Error" => "Accès refusé à cet utilisateur"], 403);
        }

        return new JsonResponse(["error" => "Cet utilisateur n'existe pas"], 404);
    }

    /**
     * @Route("/users", name="new_user", methods={"POST"}, options={"expose" = true})
     * @param Request $request
     * @param ClientRepository $clientRepository
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function new(Request $request, ClientRepository $clientRepository, ValidatorInterface $validator): Response
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setClient($this->getUser());
        $errors = $validator->validate($user);
        if (!count($errors) > 0) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return new JsonResponse(["success" => "L'utilisateur a bien été créé"], 200);
        }
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = $error->getMessage();
        }
        return new JsonResponse($errorMessages, 202);
    }

    /**
     * @Route("/users/{id}", name="edit_user", methods={"PATCH"}, options={"expose" = true})
     * @param int $id
     * @param Request $request
     * @param FormErrorsHandler $errorsHandler
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function edit(int $id, Request $request, FormErrorsHandler $errorsHandler, ValidatorInterface $validator): Response
    {
        $user = $this->UserRepository->find($id);
        if ($user) {
            if ($this->isGranted('EDIT_USER', $user)) {
                $userToCompare = clone $user;
                $this->deserializer->deserialize($request->getContent(), User::class, "json", [
                    'object_to_populate' => $user,
                ]);

                if ($userToCompare != $user) {
                    $errors = $validator->validate($user);
                    if (!count($errors) > 0) {
                        $this->entityManager->persist($user);
                        $this->entityManager->flush();
                        return new JsonResponse(["success" => "L'utilisateur a bien été modifié"], 200);
                    }

                    $errorMessages = [];
                    foreach ($errors as $error) {
                        $errorMessages[] = $error->getMessage();
                    }
                    return new JsonResponse($errorMessages, 202);
                }
                return new JsonResponse(["Error" => "Aucun changement detecté"], 202);
            }
            return new JsonResponse(["Error" => "Accès refusé à cet utilisateur"], 403);
        }
        return new JsonResponse(["error" => "L'utilisateur n'existe pas"], 404);
    }


    /**
     * @Route("/users/{id}", name="delete_user", methods={"DELETE"}, options={"expose" = true})
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function delete(int $id, Request $request): Response
    {
        $user = $this->UserRepository->find($id);
        if ($user) {
            if ($this->isGranted('DELETE_USER', $user)) {
                $this->entityManager->remove($user);
                $this->entityManager->flush();
                return new JsonResponse(["success" => "L'utilisateur a bien été supprimé"], 200);
            }
            return new JsonResponse(["Error" => "Accès refusé à cet utilisateur"], 403);
        }
        return new JsonResponse(["error" => "L'utilisateur n'existe pas"], 404);
    }
}
