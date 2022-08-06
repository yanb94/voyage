<?php

namespace App\Document\EventSubscriber;

use App\Repository\CommentRepository;
use Psr\Log\LoggerInterface;
use Sulu\Bundle\PageBundle\Document\PageDocument;
use Sulu\Component\DocumentManager\Event\RemoveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Sulu\Component\DocumentManager\Events;

class RemoveArticleSubscriber implements EventSubscriberInterface
{
    public function __construct(private CommentRepository $repository, private LoggerInterface $logger)
    {
        
    }

    public function handleRemoveEvent(RemoveEvent $event)
    {
        /** @var \Sulu\Bundle\PageBundle\Document\PageDocument $document */
        $document = $event->getDocument();        

        if(!$document instanceof PageDocument || $document->getStructureType() != 'article') {
            return;
        }

        $this->repository->deleteCommentByIdArticle($document->getUuid());
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::REMOVE => ['handleRemoveEvent', 0],
        ];
    }
}
