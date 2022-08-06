<?php
namespace App\DataFixtures;

use Sulu\Component\DocumentManager\DocumentManager;
use Sulu\Bundle\DocumentManagerBundle\DataFixtures\DocumentFixtureInterface;
use Sulu\Component\DocumentManager\PathBuilder;

class HomeFixtures implements DocumentFixtureInterface
{
    const LOCALE = 'fr';

    public function __construct(private PathBuilder $pathBuilder)
    {
        
    }

    public function getGroups(): array
    {
        return ['HomeFixtures'];
    }

    public function getOrder()
    {
        return 25;
    }

    public function load(DocumentManager $documentManager)
    {
        $homePath = $this->pathBuilder->build(['%base%', "blog-voyage", '%content%']);
        $routesPath = $this->pathBuilder->build(['%base%', "blog-voyage", '%route%']) . '/' . self::LOCALE;

        /** @var \Sulu\Bundle\PageBundle\Document\HomeDocument $document */
        $document = $documentManager->find($homePath,self::LOCALE,[
            'load_ghost_content' => false,
        ]);

        $document->setTitle("Blog Voyage");

        $document->setExtension(
            "seo",
            [
                "title" => "Blog Voyage",
                "description" => "Découvrez Blog Voyage, le blog du voyage et des voyageurs"
            ]
        );


        $articlesSource = $documentManager->find('/cmf/blog-voyage/contents/nos-articles', self::LOCALE);

        $document->getStructure()->bind(
            [
                'title' => "Blog Voyage",
                'desc' => 'Le blog du voyage et des voyageurs',
                "pages" => [
                    "audienceTargeting"=> null,
                    "categories"=> null,
                    "categoryOperator"=> null,
                    "dataSource" => $articlesSource->getUuid(),
                    "includeSubFolders" => false,
                    "limitResult"=> 6,
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
                'load_ghost_content' => false,
                'auto_create' => true,
                "path" => $homePath
            ]
        );
        $documentManager->publish($document, static::LOCALE);

        $routeDocument = $documentManager->find($routesPath);
        $routeDocument->setTargetDocument($document);

        $documentManager->persist($routeDocument,self::LOCALE, [
            'path' => $routesPath,
            'auto_create' => true,
        ]);
        $documentManager->publish($routeDocument, self::LOCALE);

        $documentManager->flush();
    }
}