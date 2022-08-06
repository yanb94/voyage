<?php
namespace App\DataFixtures;

use Sulu\Component\DocumentManager\DocumentManager;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Sulu\Bundle\MediaBundle\Collection\Manager\CollectionManagerInterface;
use Sulu\Bundle\DocumentManagerBundle\DataFixtures\DocumentFixtureInterface;
use Sulu\Bundle\SecurityBundle\Entity\User;
use Sulu\Bundle\SecurityBundle\Entity\UserRole;
use Sulu\Component\Security\Authentication\UserRepositoryInterface;

class ArticlesFixtures implements DocumentFixtureInterface
{
    const LOCALE = 'fr';

    public function __construct(
        private MediaManagerInterface $mediaManager, 
        private CollectionManagerInterface $collectionManager,
        private UserRepositoryInterface $userRepository
    )
    {
        
    }

    public function getGroups(): array
    {
        return ['ArticlesFixtures'];
    }

    public function getOrder()
    {
        return 21;
    }

    public function load(DocumentManager $documentManager)
    {
        $nbArticles = $this->collectionManager->getByKey('articles', self::LOCALE)->getMediaCount();

        $author = $this->userRepository->findOneBy(['username' => 'admin']);

        for ($i=0; $i < $nbArticles; $i++) { 
            $t = $i + 1;
            $this->createAndPersistAPage(
                $documentManager,
                "Article $t",
                "/articles/article-$t",
                file_get_contents(__DIR__. '/../../articleContentFixtures/articleContent.txt'),
                $t,
                $author
            );
        }

        $documentManager->flush();
    }

    private function createAndPersistAPage(DocumentManager $documentManager, string $title, string $url, string $content, int $index, User $author): void 
    {
        $t = $index;

        /** @var \Sulu\Bundle\PageBundle\Document\PageDocument $document */
        $document = $documentManager->create('page');

        $document->setLocale(static::LOCALE);

        $document->setTitle($title);

        $document->setStructureType('article');

        $document->setResourceSegment($url);

        $document->setAuthor($author->getId());

        $document->getStructure()->bind(
            [
                "content" => $content,
                "image" => ["id" => $this->mediaManager->get(self::LOCALE, ["search" => "article$t"])[0]->getId()]
            ]
        );

        $document->setExtension(
            "seo",
            [
                "title" => $title,
                "description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ligula justo, fringilla eu sem ac, fermentum scelerisque lorem. Quisque a luctus dui. Phasellus sed leo mi. Proin sed sem tortor."                
            ]
        );

        $document->setExtension(
            "excerpt",
            [
                "title" => $title,
                "description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ligula justo, fringilla eu sem ac, fermentum scelerisque lorem. Quisque a luctus dui. Phasellus sed leo mi. Proin sed sem tortor.",
                "images" => ["ids" => [$this->mediaManager->get(self::LOCALE, ["search" => "article$t"])[0]->getId()]]                
            ]
        );

        $documentManager->persist(
            $document,
            static::LOCALE,
            [
                'parent_path' => '/cmf/blog-voyage/contents/nos-articles',
            ]
        );

        $documentManager->publish($document, static::LOCALE);
    }
}