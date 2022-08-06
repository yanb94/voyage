<?php
namespace App\DataFixtures;

use Sulu\Component\DocumentManager\DocumentManager;
use Sulu\Bundle\DocumentManagerBundle\DataFixtures\DocumentFixtureInterface;

class LegalFixtures implements DocumentFixtureInterface
{
    const LOCALE = 'fr';

    public function __construct() {
        
    }

    public function getGroups(): array
    {
        return ['LegalFixtures'];
    }

    public function getOrder()
    {
        return 15;
    }

    public function load(DocumentManager $documentManager)
    {
        $this->createAndPersistAPage(
            $documentManager,
            "Mention Légale",
            "Mention Légale",
            "/mention-legale",
            file_get_contents(__DIR__. '/../../legalContentFixtures/mention-legal.txt')
        );

        $this->createAndPersistAPage(
            $documentManager,
            "Politique de confidentialité",
            "Confidentialité",
            "/confidentialite",
            file_get_contents(__DIR__. '/../../legalContentFixtures/confidentialite.txt')
        );

        $documentManager->flush();
    }

    private function createAndPersistAPage(DocumentManager $documentManager, string $title, string $label, string $url, string $content): void 
    {
        $document = $documentManager->create('page');

        $document->setLocale(static::LOCALE);

        $document->setTitle($title);

        $document->setStructureType('legal');

        $document->setNavigationContexts(['legal']);

        $document->setResourceSegment($url);

        $document->getStructure()->bind(
            [
                "label" => $label,
                "content" => $content
            ]
        );

        $document->setExtension(
            "excerpt",
            [
                "title" => $label
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
    }
}