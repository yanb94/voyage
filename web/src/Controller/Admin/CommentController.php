<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use FOS\RestBundle\View\View;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Component\Rest\RestHelperInterface;
use FOS\RestBundle\View\ViewHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sulu\Component\Rest\AbstractRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Sulu\Component\Content\Mapper\ContentMapperInterface;
use Sulu\Component\DocumentManager\DocumentManagerInterface;
use Sulu\Component\Rest\ListBuilder\PaginatedRepresentation;
use Sulu\Component\Webspace\Analyzer\RequestAnalyzerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use HandcraftedInTheAlps\RestRoutingBundle\Routing\ClassResourceInterface;
use Sulu\Component\Rest\ListBuilder\Metadata\FieldDescriptorFactoryInterface;
use Sulu\Component\Rest\ListBuilder\Doctrine\DoctrineListBuilderFactoryInterface;

/**
 * @RouteResource("comment")
 */
class CommentController extends AbstractRestController implements ClassResourceInterface
{
    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var FieldDescriptorFactoryInterface
     */
    private $fieldDescriptorFactory;

    /**
     * @var DoctrineListBuilderFactoryInterface
     */
    private $listBuilderFactory;

    /**
     * @var RestHelperInterface
     */
    private $restHelper;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        FieldDescriptorFactoryInterface $fieldDescriptorFactory,
        DoctrineListBuilderFactoryInterface $listBuilderFactory,
        RestHelperInterface $restHelper,
        private EntityManagerInterface $entityManager,
        private DocumentManagerInterface $contentMapper
    ) {
        $this->viewHandler = $viewHandler;
        $this->fieldDescriptorFactory = $fieldDescriptorFactory;
        $this->listBuilderFactory = $listBuilderFactory;
        $this->restHelper = $restHelper;
    }

    public function cgetAction(): Response
    {
        $fieldDescriptors = $this->fieldDescriptorFactory->getFieldDescriptors(Comment::RESOURCE_KEY);
        $listBuilder = $this->listBuilderFactory->create(Comment::class);
        $this->restHelper->initializeListBuilder($listBuilder, $fieldDescriptors);

        $originData = $listBuilder->execute();

        foreach ($listBuilder->execute() as $key => $array) {
            $originData[$key]['idPage'] = $this->contentMapper->find($array['idPage'])->getTitle();
        }

        $listRepresentation = new PaginatedRepresentation(
            $originData,
            Comment::RESOURCE_KEY,
            $listBuilder->getCurrentPage(),
            $listBuilder->getLimit(),
            $listBuilder->count()
        );

        return $this->viewHandler->handle(View::create($listRepresentation));
    }

    public function getAction(int $id): Response
    {
        /** @var Comment */
        $comment = $this->entityManager->getRepository(Comment::class)->findOneBy(["id" =>$id]);

        $comment->setTitleArticle($this->contentMapper->find($comment->getIdPage())->getTitle());

        if (!$comment) {
            throw new NotFoundHttpException();
        }

        return $this->viewHandler->handle($this->view($comment));
    }

    public function deleteAction(int $id): Response
    {
        /** @var Comment $comment */
        $comment = $this->entityManager->getRepository(Comment::class)->findOneBy(["id" =>$id]);
        $this->entityManager->remove($comment);
        $this->entityManager->flush();

        return $this->viewHandler->handle($this->view(null, 204));
    }

    public function putAction(Request $request, int $id): Response
    {
        /** @var Comment $comment */
        $comment = $this->entityManager->getRepository(Comment::class)->findOneBy(["id" =>$id]);

        if (!$comment) {
            throw new NotFoundHttpException();
        }

        $comment->setModerate($request->request->all()['moderate']);
        
        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $this->viewHandler->handle($this->view($comment));
    }
}