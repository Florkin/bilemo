<?php

namespace App\Controller;

use App\Entity\DocSection;
use App\Form\DocSectionType;
use App\Handlers\Forms\EntityFormHandler;
use App\Repository\DocSectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/documentation")
 */
class DocSectionController extends AbstractController
{
    /**
     * @var EntityFormHandler
     */
    private $formHandler;

    /**
     * DocSectionController constructor.
     * @param EntityFormHandler $formHandler
     */
    public function __construct(EntityFormHandler $formHandler)
    {
        $this->formHandler = $formHandler;
    }

    /**
     * @Route("/", name="doc_section_index", methods={"GET"})
     * @param DocSectionRepository $docSectionRepository
     * @return Response
     */
    public function index(DocSectionRepository $docSectionRepository): Response
    {
        return $this->render('doc_section/index.html.twig', [
            'doc_sections' => $docSectionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="doc_section_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $docSection = new DocSection();

        if ($this->formHandler->handle($request, $docSection, DocSectionType::class)) {
            $this->addFlash("success", "La section " . $docSection->getTitle() . " a bien été ajoutée");
            return $this->redirectToRoute("doc_section_index");
        }

        return $this->render('doc_section/new.html.twig', [
            'doc_section' => $docSection,
            'form' => $this->formHandler->createView(),
        ]);
    }

//    /**
//     * @Route("/{id}", name="doc_section_show", methods={"GET"})
//     * @param DocSection $docSection
//     * @return Response
//     */
//    public function show(DocSection $docSection): Response
//    {
//        return $this->render('doc_section/show.html.twig', [
//            'doc_section' => $docSection,
//        ]);
//    }

    /**
     * @Route("/{id}/edit", name="doc_section_edit", methods={"GET","POST"})
     * @param Request $request
     * @param DocSection $docSection
     * @return Response
     */
    public function edit(Request $request, DocSection $docSection): Response
    {
        if ($this->formHandler->handle($request, $docSection, DocSectionType::class)) {
            $this->addFlash("success", "La section " . $docSection->getTitle() . " a bien été modifiée");
            return $this->redirectToRoute("doc_section_index");
        }

        return $this->render('doc_section/edit.html.twig', [
            'doc_section' => $docSection,
            'form' => $this->formHandler->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="doc_section_delete", methods={"DELETE"})
     * @param Request $request
     * @param DocSection $docSection
     * @return Response
     */
    public function delete(Request $request, DocSection $docSection): Response
    {
        if ($this->isCsrfTokenValid('delete'.$docSection->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($docSection);
            $entityManager->flush();

            $this->addFlash("success", "La section " . $docSection->getTitle() . " a bien été supprimée");
        }

        return $this->redirectToRoute('doc_section_index');
    }
}
