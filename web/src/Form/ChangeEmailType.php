<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Sulu\Bundle\SecurityBundle\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangeEmailType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // $builder->add('mainEmail', EmailType::class, [
        //     'property_path' => 'contact.mainEmail',
        //     'label' => "Votre email"
        // ]);  
        
        $builder->add('email', RepeatedType::class, [
            'type' => EmailType::class,
            'invalid_message' => 'Vos email doivent être identique',
            // 'property_path' => 'contact.mainEmail',
            'data' => null,
            'required' => true,
            'first_options'  => [
                'label' => 'Votre nouvel email',
                "attr" => [
                    "placeholder" => "Entrez votre nouvel email"
                ],
                "constraints" => [
                    new NotBlank(message: "Vous devez renseigner un email", groups: ['profile']),
                    new Email(message: "Vous devez entrez un email correct", groups: ['profile'])
                ]
            ],
            'second_options' => [
                'label' => 'Confirmer votre nouvel email',
                "attr" => [
                    "placeholder" => "Confirmer votre nouvel email"
                ]
            ]
        ]);
        
        $builder->add('submit', SubmitType::class,[
            "label" => "Modifier"
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
                'validation_groups' => ['profile']
            ]
        );
    }
}