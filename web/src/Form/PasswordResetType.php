<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Sulu\Bundle\SecurityBundle\Entity\User;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

/**
 * Create the password reset form type.
 */
class PasswordResetType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // $builder->add(
        //     'plainPassword',
        //     PasswordType::class,
        //     [
        //         'mapped' => false,
        //     ]
        // );

        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Vos mot de passe doivent être identique',
            'options' => ['attr' => ['class' => 'password-field']],
            'required' => true,
            'first_options'  => [
                'label' => 'Votre nouveau mot de passe',
                "attr" => [
                    "placeholder" => "Entrez votre nouveau mot de passe"
                ],
                "constraints" => [
                    new NotBlank(null,"Vous devez indiquer votre nouveau mot de passe", groups: ['password_reset']),
                    new Length(min: 8,max: 32, minMessage: "Le mot de passe doit faire au moins 8 caractères", maxMessage: "Le mot de passe ne dois pas dépasser 32 caractères", groups: ['password_reset']),
                    new Regex("/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$/", "Votre mot de passe doit contenir au moins une minuscule, une majuscule, un chiffre et un caractère spéciaux", groups: ['password_reset'])
                ]
            ],
            'second_options' => [
                'label' => 'Confirmer votre nouveau mot de passe',
                "attr" => [
                    "placeholder" => "Confirmer votre nouveau mot de passe"
                ]
            ],
            'mapped' => false,
        ]);

        $builder->add('submit', SubmitType::class, [
            "label" => "Réinitialiser"
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
                'validation_groups' => ['password_reset'],
            ]
        );
    }
}