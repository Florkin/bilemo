<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Form\UserType;
use App\Handlers\Forms\FormErrorsHandler;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * ProductController constructor.
     * @param SerializerInterface $serializer
     * @param UserRepository $UserRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(SerializerInterface $serializer, UserRepository $UserRepository, EntityManagerInterface $entityManager)
    {
        $this->serializer = $serializer;
        $this->UserRepository = $UserRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/users", name="user_index", methods={"GET"}, options={"expose" = true})
     * @return Response
     */
    public function index(): Response
    {
        $users = $this->UserRepository->findAll();

        if ($users) {
            $usersJSON = $this->serializer->serialize($users, 'json');
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
            $userJSON = $this->serializer->serialize($user, 'json');
            return new Response($userJSON, 200, array('Content-Type' => 'application/json'));
        }

        return new JsonResponse(["error" => "Cet utilisateur n'existe pas"], 404);
    }

    /**
     * @Route("/users", name="new_user", methods={"POST"}, options={"expose" = true})
     * @param Request $request
     * @param ClientRepository $clientRepository
     * @return Response
     */
    public function new(Request $request, ClientRepository $clientRepository): Response
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        // TODO get the current client
        $client = $clientRepository->find(25);
        $user->setClient($client);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return new JsonResponse(["success" => "L'utilisateur a bien été créé"], 200);
    }

    /**
     * @Route("/users/{id}", name="edit_user", methods={"PATCH"}, options={"expose" = true})
     * @param int $id
     * @param Request $request
     * @param FormErrorsHandler $errorsHandler
     * @return Response
     */
    public function edit(int $id, Request $request, FormErrorsHandler $errorsHandler): Response
    {
        $user = $this->UserRepository->find($id);
        $data = $this->serializer->deserialize($request->getContent(), "array", 'json');
        $form = $this->createForm(UserType::class, $user, [
            'csrf_protection' => false,
            'method' => "PATCH"
        ]);
        $form->submit($data);
        if ($form->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return new JsonResponse(["success" => "L'utilisateur a bien été modifié"], 200);
        }

        $errors = $errorsHandler->getErrors($form);
        return new JsonResponse($errors, 400);
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
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        return new JsonResponse(["success" => "L'utilisateur a bien été supprimé"], 200);
    }
}
