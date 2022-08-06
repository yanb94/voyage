<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Sulu\Bundle\MediaBundle\Entity\Collection;
use Sulu\Bundle\MediaBundle\Entity\CollectionMeta;
use Sulu\Bundle\MediaBundle\Entity\CollectionType;

class CollectionFixtures extends Fixture
{
    const LOCALE = 'fr';

    public function getOrder()
    {
        return 16;
    }

    public function load(ObjectManager $manager)
    {
        // $mediaType;
        $collection = new Collection();
        $collection->setKey("person");

        $collectionType = new CollectionType();
        $collectionType->setName("Person");
        $collectionType->setDescription("Photo de l'équipe");

        $collectionMeta = new CollectionMeta();
        $collectionMeta->setDescription("Photo de l'équipe");
        $collectionMeta->setLocale(self::LOCALE);
        $collectionMeta->setTitle("Person");
        $collectionMeta->setCollection($collection);

        $collection->addMeta($collectionMeta);
        $collection->setType($collectionType);

        $manager->persist($collection);
        $manager->persist($collectionType);
        $manager->persist($collectionMeta);

        $manager->flush();
    }

}