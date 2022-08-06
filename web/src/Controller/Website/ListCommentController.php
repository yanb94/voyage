<?php

namespace App\Controller\Website;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\SecurityBundle\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ListCommentController extends AbstractController
{

    /**
     * @Route("/my-list-comment", name="my-list-comment")
     */
    public function myListComment(EntityManagerInterface $em)
    {
        /** @var User */
        $user = $this->getUser();
        $comments = $em->getRepository(Comment::class)->findBy(['author' => $user],['createdAt' => "desc"]);

        return $this->render("community/my-list-comment.html.twig",[
            "comments" => $comments
        ]);
    }

    /**
     * @Route("/my-list-comment/delete/{id}", name="my-list-comment-delete")
     */
    public function deleteComment(int $id, EntityManagerInterface $em)
    {
        $comment = $em->getRepository(Comment::class)->find($id);

        if(is_null($comment) || $this->getUser() != $comment->getAuthor())
            throw new AccessDeniedHttpException();
        
        $em->remove($comment);
        $em->flush();
        
        $this->addFlash('success',"Votre commentaire a bien été surpprimé");

        return $this->redirect("/my-list-comment");
    }
}