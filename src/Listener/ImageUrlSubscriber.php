<?php

namespace App\Listener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use JMS\Serializer\GenericSerializationVisitor;

class ImageUrlSubscriber implements EventSubscriberInterface
{
    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;
    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * ImageUrlSubscriber constructor.
     * @param UploaderHelper $uploaderHelper
     * @param CacheManager $cacheManager
     */
    public function __construct(UploaderHelper $uploaderHelper, CacheManager $cacheManager)
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->cacheManager = $cacheManager;
    }

    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event' => 'serializer.post_serialize',
                'method' => 'onPostSerialize',
                'class' => 'App\\Entity\\Product', // if no class, subscribe to every serialization
                'format' => 'json', // optional format
                'priority' => 0, // optional priority
            ),
        );
    }

    public function onPostSerialize(ObjectEvent $event)
    {
//        $context = $event->getContext()->getAttribute('groups');
//
//        if (
//            in_array('details_product', $context)
//            || in_array('list_product', $context)
//        ) {
            $pictures = $event->getObject()->getPictures();
            $picturesPaths = [];
            foreach ($pictures as $picture) {
                $urls = [];
                $path = $this->uploaderHelper->asset($picture, 'imageFile');
                foreach (['mid_size_formated', 'thumb'] as $pattern) {
                    $urls[$pattern] = $this->cacheManager->getBrowserPath($path, sprintf('%s', $pattern));
                }
                $picturesPaths[] = $urls;
            }
            $event->getVisitor()->visitProperty(new StaticPropertyMetadata ('', 'images', null), $picturesPaths);
        }
//    }
}