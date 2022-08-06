<?php

namespace App\Form\DataTransformer;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CommentToIdTransformer implements DataTransformerInterface
{
    public function __construct(private CommentRepository $commentRepository)
    {

    }

    public function transform($comment)
    {
        if(is_null($comment))
        {
            return "";
        }

        return $comment->getId();
    }

    public function reverseTransform($id)
    {
        if(!$id)
        {
            return;
        }

        $comment = $this->commentRepository->findOneBy(['id' => $id]);

        if(is_null($comment))
        {
            throw new TransformationFailedException(sprintf(
                'A comment with id "%s" does not exist!',
                $id
            ));
        }

        return $comment;
    }
}