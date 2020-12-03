<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Repository\BrandRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductFixture extends Fixture implements DependentFixtureInterface
{
    /**
     * @var BrandRepository
     */
    private $brandRepository;
    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * ProductFixture constructor.
     * @param BrandRepository $brandRepository
     * @param CacheManager $cacheManager
     */
    public function __construct(BrandRepository $brandRepository, CacheManager $cacheManager)
    {
        $this->brandRepository = $brandRepository;
        $this->cacheManager = $cacheManager;
    }

    public function load(ObjectManager $manager)
    {
        $this->cleanImagesFolders();

        $brands = $this->brandRepository->findAll();
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 60; $i++) {
            $product = new Product();
            $pictures = $this->fakeUploadPictures();

            $product
                ->setName($faker->words(2, true))
                ->setDescription($faker->sentences(6, true))
                ->setPrice($faker->randomFloat(2, 99, 2000))
                ->setBrand($faker->randomElement($brands))
                ->setPictureFiles($pictures);

            $manager->persist($product);
        }
        $manager->flush();;
    }

    public function getDependencies()
    {
        return array(
            BrandFixture::class,
        );
    }

    private function cleanImagesFolders()
    {
        $this->cacheManager->remove();
        $path = __DIR__ . "/../../public/images/products/*";
        $files = glob($path);
        foreach ($files as $file) {
            if (is_file($file))
                unlink($file);
        }
    }

    private function fakeUploadPictures()
    {
        $numberOfImages = $this->randomNumber(1, 4);

        $pictures = [];

        for ($j = 0; $j < $numberOfImages; $j++) {
            $originalPath = $this->randomPic(__DIR__ . "/FixturesImages");
            $uniqueName = "img" . $j . ".jpg";
            $fileSystem = new Filesystem();
            $targetPath = sys_get_temp_dir() . '/' . $uniqueName;
            $fileSystem->copy($originalPath, $targetPath, false);
            $pictures[$j] = new UploadedFile($targetPath, $uniqueName, "image/jpeg", null, true);
        }
        return $pictures;
    }

    function randomPic($dir)
    {
        $files = glob($dir . '/*.*');
        $file = array_rand($files);
        return $files[$file];
    }

    private function randomNumber($minNumber, $maxNumber)
    {
        try {
            return random_int($minNumber, $maxNumber);
        } catch (\Exception $e) {
            return 1;
        }
    }
}

