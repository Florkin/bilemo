<?php

namespace App\Controller\Manager;

use App\Entity\Brand;
use App\Form\BrandType;
use App\Repository\BrandRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/manage_brand")
 */
class ManageBrandController extends AbstractController
{
    /**
     * @Route("/", name="manage_brand_index", methods={"GET"})
     * @param PaginatorInterface $paginator
     * @param BrandRepository $brandRepository
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, BrandRepository $brandRepository, Request $request): Response
    {
        $brands = $paginator->paginate(
            $brandRepository->findAll(),
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('brand/index.html.twig', [
            'brands' => $brands,
        ]);
    }

    /**
     * @Route("/new", name="manage_brand_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $brand = new Brand();
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($brand);
            $entityManager->flush();

            return $this->redirectToRoute('manage_brand_index');
        }

        return $this->render('brand/new.html.twig', [
            'brand' => $brand,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="manage_brand_show", methods={"GET"})
     */
    public function show(Brand $brand): Response
    {
        return $this->render('brand/show.html.twig', [
            'brand' => $brand,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="manage_brand_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Brand $brand): Response
    {
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('manage_brand_index');
        }

        return $this->render('brand/edit.html.twig', [
            'brand' => $brand,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="manage_brand_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Brand $brand): Response
    {
        if ($this->isCsrfTokenValid('delete'.$brand->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($brand);
            $entityManager->flush();
        }

        return $this->redirectToRoute('manage_brand_index');
    }
}
