<?php
namespace App\DataFixtures;

use Sulu\Component\DocumentManager\DocumentManager;
use Sulu\Bundle\DocumentManagerBundle\DataFixtures\DocumentFixtureInterface;
use Sulu\Component\DocumentManager\Exception\MetadataNotFoundException;

class ListArticlesFixtures implements DocumentFixtureInterface
{
    const LOCALE = 'fr';

    public function getGroups(): array
    {
        return ['ListArticlesFixtures'];
    }

    public function getOrder()
    {
        return 20;
    }

    public function load(DocumentManager $documentManager)
    {
        /** @var \Sulu\Bundle\PageBundle\Document\PageDocument $document */
        $document = $documentManager->create('page');
        
        $document->setLocale(static::LOCALE);

        $document->setTitle('Nos articles');

        $document->setStructureType('articles-list');

        $document->setResourceSegment('/articles');

        $document->setExtension(
            "seo",
            [
                "description" => "Découvrez l'ensemble des articles de blog voyage."
            ]
        );

        $document->setNavigationContexts(['main']);

        $documentManager->persist(
            $document,
            static::LOCALE,
            [
                'parent_path' => '/cmf/blog-voyage/contents',
            ]
        );

        $document->getStructure()->bind(
            [
                "pages" => [
                    "audienceTargeting"=> null,
                    "categories"=> null,
                    "categoryOperator"=> null,
                    "dataSource" => $document->getUuid(),
                    "includeSubFolders" => true,
                    "limitResult"=> null,
                    "sortBy" => 'published',
                    "sortMethod" => 'desc',
                    "tagOperator" => null,
                    "tags" => null,
                    "types" => ['article'],
                    "presentAs" => null
                ]
            ]
        );

        

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