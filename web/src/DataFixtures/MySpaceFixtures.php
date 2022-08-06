<?php
namespace App\DataFixtures;

use Sulu\Component\DocumentManager\DocumentManager;
use Sulu\Bundle\DocumentManagerBundle\DataFixtures\DocumentFixtureInterface;

class MySpaceFixtures implements DocumentFixtureInterface
{
    const LOCALE = 'fr';

    public function getGroups(): array
    {
        return ['MySpaceFixtures'];
    }

    public function getOrder()
    {
        return 12;
    }

    public function load(DocumentManager $documentManager)
    {
        /** @var \Sulu\Bundle\PageBundle\Document\PageDocument $document */
        $document = $documentManager->create('page');

        $document->setLocale(static::LOCALE);

        $document->setTitle('Mon espace');

        $document->setStructureType('my-space');

        $document->setResourceSegment('/space');

        $documentManager->persist(
            $document,
            static::LOCALE,
            [
                'parent_path' => '/cmf/blog-voyage/contents',
            ]
        );

        $documentManager->publish($document, static::LOCALE);

        $documentManager->flush();
    }
}