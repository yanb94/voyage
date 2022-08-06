<?php

namespace App\DataFixtures;

use Symfony\Component\Finder\Finder;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sulu\Bundle\MediaBundle\Media\TypeManager\TypeManagerInterface;
use Sulu\Bundle\MediaBundle\Collection\Manager\CollectionManagerInterface;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;

class ArticlesMediaFixtures extends Fixture
{
    const LOCALE = 'fr';

    public function __construct(
        private CollectionManagerInterface $collectionManager, 
        private TypeManagerInterface $typeManager,
        private MediaManagerInterface $mediaManager
    )
    {
        
    }

    public function getOrder()
    {
        return 19;
    }

    public function load(ObjectManager $manager)
    {
        $imageDir = realpath(__DIR__ . '/../../imagesFixtures/article');
        $finder = new Finder();
        $finder->in($imageDir)->name('/\.jpg$|\.png$/');

        $collection = $this->collectionManager->getByKey('articles', self::LOCALE);
        $mediaType = $this->typeManager->get(2);

        foreach ($finder as $file) {
            echo $file->getBasename();
            $uploadedFile = new UploadedFile($file->getPathname(), $file->getPathname(), null, null, false);
            $data = array(
                'id' => null,
                'locale' => self::LOCALE,
                'type' => $mediaType->getId(),
                'collection' => $collection->getId(),
                'name' => $file->getBasename(),
                'title' => substr($file->getBasename(), 0, -(strlen($file->getExtension()) + 1)),
            );
            $this->mediaManager->save($uploadedFile, $data, 1);
        }
    }
}