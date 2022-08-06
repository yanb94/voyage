<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller\Website;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\AnswerCommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sulu\Component\Content\Compat\StructureInterface;
use Sulu\Bundle\WebsiteBundle\Controller\WebsiteController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Default Controller for rendering templates, uses the themes from the ClientWebsiteBundle.
 */
class ArticleController extends WebsiteController
{
    /**
     * Loads the content from the request (filled by the route provider) and creates a response with this content and
     * the appropriate cache headers.
     *
     * @param bool $preview
     * @param bool $partial
     *
     * @return Response
     */
    public function indexAction(StructureInterface $structure, Request $request, EntityManagerInterface $em, $preview = false, $partial = false)
    {
        /** @var Sulu\Component\Content\Compat\Structure\PageBridge */
        $pageData = $structure;

        $locale = $pageData->getLanguageCode();
        $currentUrl = $pageData->getUrls()[$locale];

        $comment = new Comment();
        $comment->setAuthor($this->getUser());
        $comment->setIdPage($structure->getUuid());

        $form = $this->createForm(CommentType::class,$comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $comment->setCreatedAt(new \DateTime("now"));

            $em->persist($comment);
            $em->flush();

            return $this->redirect($currentUrl);
        }

        $response = $this->renderStructure(
            $structure,
            [
                "form" => $form->createView()
            ],
            $preview,
            $partial
        );

        return $response;
    }

    public function listAction(string $idPage, CommentRepository $commentRepository)
    {
        $comments = $commentRepository->findBy([
            "idPage" => $idPage,
            "parentComment" => null
        ]);
        
        return $this->render("items/list-comment.html.twig",[
            "comments" => $comments
        ]);
    }

    public function countComment(string $idPage, CommentRepository $commentRepository)
    {
        $count = $commentRepository->count(["idPage" => $idPage]);

        return new Response($count);
    }

    /**
     * @Route("/answer-comment-form/save", name="saveAnswerCommentForm")
     */
    public function saveAnswerCommentForm(Request $request, EntityManagerInterface $em)
    {
        $refer = $request->headers->get('referer');

        $comment = new Comment();
        $form = $this->createForm(AnswerCommentType::class,$comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $comment->setCreatedAt(new \DateTime('now'));
            $comment->setAuthor($this->getUser());

            $em->persist($comment);
            $em->flush();
        }

        return $this->redirect($refer);
    }

    /**
     * @Route("/answer-comment-form/{parentCommentId}", name="answerCommentForm")
     */
    public function getAnswerCommentForm(int $parentCommentId, CommentRepository $commentRepository)
    {
        $comment = new Comment();

        $parentComment = $commentRepository->findOneBy(['id' => $parentCommentId]);

        if($parentComment == null)
        {
            throw new NotFoundHttpException("Le commentaire parent n'existe pas");
        }

        if($parentComment->getParentComment() ==  null)
            $comment->setParentComment($parentComment);
        else
            $comment->setParentComment($parentComment->getParentComment());

        $comment->setIdPage($parentComment->getIdPage());
        $comment->setContent("@".$parentComment->getAuthor()->getUsername()." ");

        $form = $this->createForm(AnswerCommentType::class,$comment,[
            "action" => $this->generateUrl("saveAnswerCommentForm")
        ]);

        return $this->render("items/answer-comment-form.html.twig",[
            "form" => $form->createView(),
            "usernameOther" => $parentComment->getAuthor()->getUsername()
        ]);
    }
}
