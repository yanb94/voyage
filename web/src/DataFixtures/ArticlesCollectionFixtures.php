<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Sulu\Bundle\MediaBundle\Entity\Collection;
use Sulu\Bundle\MediaBundle\Entity\CollectionMeta;
use Sulu\Bundle\MediaBundle\Entity\CollectionType;

class ArticlesCollectionFixtures extends Fixture
{
    const LOCALE = 'fr';

    public function getOrder()
    {
        return 18;
    }

    public function load(ObjectManager $manager)
    {
        // $mediaType;
        $collection = new Collection();
        $collection->setKey("articles");

        $collectionType = new CollectionType();
        $collectionType->setName("Articles");
        $collectionType->setDescription("Images illustrants les articles");

        $collectionMeta = new CollectionMeta();
        $collectionMeta->setDescription("Images illustrants les articles");
        $collectionMeta->setLocale(self::LOCALE);
        $collectionMeta->setTitle("Articles");
        $collectionMeta->setCollection($collection);

        $collection->addMeta($collectionMeta);
        $collection->setType($collectionType);

        $manager->persist($collection);
        $manager->persist($collectionType);
        $manager->persist($collectionMeta);

        $manager->flush();
    }

}