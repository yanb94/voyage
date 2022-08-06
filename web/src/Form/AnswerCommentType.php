<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\DataTransformer\CommentToIdTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AnswerCommentType extends AbstractType
{
    public function __construct(private CommentToIdTransformer $commentToIdTransformer)
    {
        
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class, [
                "label" => false
            ]);

        $builder->add('parentComment',HiddenType::class);
        $builder->add('idPage',HiddenType::class);
        $builder->add('submit', SubmitType::class, [
            "label" => "Répondre"
        ])
        ;

        $builder->get('parentComment')->addModelTransformer($this->commentToIdTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
