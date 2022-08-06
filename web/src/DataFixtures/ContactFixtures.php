<?php
namespace App\DataFixtures;

use Doctrine\ORM\EntityManagerInterface;
use Sulu\Component\DocumentManager\DocumentManager;
use Sulu\Bundle\DocumentManagerBundle\DataFixtures\DocumentFixtureInterface;
use Sulu\Bundle\FormBundle\Entity\Form;

class ContactFixtures implements DocumentFixtureInterface
{
    const LOCALE = 'fr';

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getGroups(): array
    {
        return ['ContactFixtures'];
    }

    public function getOrder()
    {
        return 14;
    }

    public function load(DocumentManager $documentManager)
    {
        /** @var \Sulu\Bundle\PageBundle\Document\PageDocument $document */
        $document = $documentManager->create('page');
        
        $document->setLocale(static::LOCALE);

        $document->setTitle("Contactez nous");

        $document->setStructureType('contact');

        $document->setResourceSegment('/contact');

        $document->setExtension(
            "seo",
            [
                "description" => "Contactez l'équipe de blog voyage, le blog du voyage et des voyageurs."
            ]
        );

        $document->setExtension(
            "excerpt",
            [
                "title" => "Contact"
            ]
        );

        $document->setNavigationContexts(['legal']);

        $document->getStructure()->bind(
            [
                "form" => $this->getFormWithTitle('Formulaire de contact')->getId()
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

    private function getFormWithTitle(?string $title): Form
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb
            ->from(Form::class, 'form')
            ->innerJoin('form.translations', 'translation')
            ->select('form')
            ->where($qb->expr()->eq('translation.title', ':title'))
            ->setParameter('title', $title)
        ;

        return $qb->getQuery()->getSingleResult();
    }
}