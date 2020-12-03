<?php


namespace App\Controller\Manager;


use App\Repository\PictureRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Component\Routing\Annotation\Route;

class PictureController extends AbstractController
{
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var PictureRepository
     */
    private $pictureRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * AdminPictureController constructor.
     * @param ProductRepository $productRepository
     * @param PictureRepository $pictureRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ProductRepository $productRepository, PictureRepository $pictureRepository, EntityManagerInterface $entityManager)
    {
        $this->productRepository = $productRepository;
        $this->pictureRepository = $pictureRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin/product/upload-image/{id}", name="ajax_product_img_upload", requirements={"id": "[0-9]*"}, methods="POST", options = {"expose" = true})
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function ajaxUploadImage(int $id, Request $request): Response
    {
        $product = $this->productRepository->findOneBy(['id' => $id]);

        $product->setPictureFiles($request->files->all());

        $date = new \DateTime();

        $this->entityManager->flush();

        $response = [
            'status' => 'success'
        ];

        return $this->json($response);
    }

    /**
     * @Route("/admin/product/get-uploaded-images/{id}", name="ajax_get_uploaded_images", requirements={"id": "[0-9]*"}, methods="POST", options = {"expose" = true})
     * @param int $id
     * @param Request $request
     * @param UploaderHelper $helper
     * @return Response
     */
    public function ajaxGetUploadedImages(int $id, Request $request, UploaderHelper $helper): Response
    {
        $product = $this->productRepository->findOneBy(['id' => $id]);
        $pictures = $product->getPictures();
        $pathArray =[];
        foreach ($pictures as $picture){
            $pathArray[$picture->getId()] =  $helper->asset($picture, 'imageFile');
        }

        return $this->json($pathArray);
    }

    /**
     * @Route("/admin/product/remove-uploaded-image/{id}/{id_picture}", name="ajax_remove_image", requirements={"id": "[0-9]*", "id_picture": "[0-9]*"}, methods="DELETE", options = {"expose" = true})
     * @param int $id
     * @param int $id_picture
     * @return Response
     */
    public function ajaxRemoveImage(int $id, $id_picture = 0): Response
    {
        $product = $this->productRepository->findOneBy(['id' => $id]);
        $picture = $this->pictureRepository->findOneBy(['id' => $id_picture]);

        $product->removePicture($picture);

        $date = new \DateTime();

        $this->entityManager->flush();

        $response = [
            'status' => 'success'
        ];

        return $this->json($response);
    }
}