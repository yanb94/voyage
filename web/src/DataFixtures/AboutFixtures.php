<?php
namespace App\DataFixtures;

use Sulu\Component\DocumentManager\DocumentManager;
use Sulu\Bundle\DocumentManagerBundle\DataFixtures\DocumentFixtureInterface;
use Sulu\Bundle\MediaBundle\Collection\Manager\CollectionManagerInterface;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;

class AboutFixtures implements DocumentFixtureInterface
{
    const LOCALE = 'fr';

    public function __construct(
        private MediaManagerInterface $mediaManager, 
        private CollectionManagerInterface $collectionManager
    )
    {
        
    }

    public function getGroups(): array
    {
        return ['AboutFixtures'];
    }

    public function getOrder()
    {
        return 18;
    }

    public function load(DocumentManager $documentManager)
    {
        /** @var \Sulu\Bundle\PageBundle\Document\PageDocument $document */
        $document = $documentManager->create('page');

        $document->setLocale(static::LOCALE);

        $document->setTitle('A propos');

        $document->setStructureType('about');

        $document->setResourceSegment('/a-propos');

        $document->getStructure()->bind(
            [
                "question" => "Qui sommes nous ?",
                "description" => "Type something Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean tincidunt magna finibus convallis ultricies. Suspendisse sed quam eu ante scelerisque pharetra. Donec lacinia congue metus vulputate dictum. Proin tincidunt vulputate lacus nec hendrerit. Mauris cursus, massa a tempor vestibulum, quam diam suscipit nibh, vel aliquam mi mi vitae nisi. Donec venenatis, eros nec euismod porttitor, odio eros egestas eros, ac varius orci elit et ante. Pellentesque faucibus mi sem, a mollis velit semper id. Donec mollis auctor mauris sed posuere. Sed diam arcu, interdum eu arcu sed, elementum dapibus nibh. Etiam eget pellentesque urna, non facilisis neque.",
                "team_title" => "Notre équipe",
                "teamMembers" => $this->createTeamMembers()
            ]
        );

        $document->setExtension(
            "seo",
            [
                "description" => "Découvrez l'équipe de Blog Voyage"
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

    private function createTeamMembers(): array
    {
        $nbPerson = $this->collectionManager->getByKey('person', self::LOCALE)->getMediaCount();
        $teamMembers = []; 

        for ($i=0; $i < $nbPerson; $i++) { 
            $t = $i + 1;
            $teamMembers[] =  [
                "type" => "member",
                "name" => "Person $t",
                "image" => ["id" => $this->mediaManager->get(self::LOCALE, ["search" => "person$t"])[0]->getId()],
                "description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
            ];
        }

        return $teamMembers;
    }
}