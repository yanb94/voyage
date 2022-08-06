<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Sulu\Bundle\SecurityBundle\Entity\User;
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
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ChangePasswordType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("oldPassword",PasswordType::class,[
            "label" => "Votre ancien mot de passe",
            "mapped" => false,
            "attr" => [
                "placeholder" => "Renseigner votre ancien mot de passe"
            ]
        ]);
        
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
                    new NotBlank(null,"Vous devez indiquer un mot de passe"),
                    new Length(min: 8,max: 32, minMessage: "Le mot de passe doit faire au moins 8 caractères", maxMessage: "Le mot de passe ne dois pas dépasser 32 caractères"),
                    new Regex("/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$/", "Votre mot de passe doit contenir au moins une minuscule, une majuscule, un chiffre et un caractère spéciaux")
                ]
            ],
            'second_options' => [
                'label' => 'Confirmer votre nouveau mot de passe',
                "attr" => [
                    "placeholder" => "Confirmer votre nouveau mot de passe"
                ]
            ],
            'mapped' => false
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
                // 'validation_groups' => ['profile'],
            ]
        );
    }
}