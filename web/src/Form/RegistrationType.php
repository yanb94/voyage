<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Sulu\Bundle\SecurityBundle\Entity\User;
use Sulu\Bundle\ContactBundle\Entity\Contact;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('lastName', TextType::class, [
            'property_path' => 'contact.lastName',
            "attr" => [
                "placeholder" => "Entrez votre nom"
            ],
            "label" => "Votre nom",
            "constraints" => [
                new Regex("/^[a-zA-Z-'À-ÖØ-öø-ÿ]+$/",message: "Vous devez entrez un nom correct", groups: ['registration']),
                new Length(min:2, max: 50, minMessage: "Le nom de famille doit faire au moins 2 caractères", maxMessage: "Le nom de famille ne dois pas dépasser 50 caractères", groups: ['registration'])
            ]
            
        ]);

        $builder->add('firstName', TextType::class, [
            'property_path' => 'contact.firstName',
            "label" => "Votre prénom",
            "attr" => [
                "placeholder" => "Entrez votre prénom"
            ],
            "constraints" => [
                new Regex("/^[a-zA-Z-'À-ÖØ-öø-ÿ]+$/",message: "Vous devez entrez un prénom correct", groups: ['registration']),
                new Length(min:2, max: 50, minMessage: "Le prénom doit faire au moins 2 caractères", maxMessage: "Le prénom ne dois pas dépasser 50 caractères", groups: ['registration'])
            ]
        ]);
        
        $builder->add('username', TextType::class,[
            "label" => "Votre nom d'utilisateur",
            "attr" => [
                "placeholder" => "Entrez votre nom d'utilisateur"
            ],
            "constraints" => [
                new Length(min: 4,max: 20, minMessage: "Le nom d'utilisateur doit faire au moins 4 caractères", maxMessage: "Le nom d'utilisateur ne dois pas dépasser 20 caractères", groups: ['registration']),
            ]
        ]);
    
        $builder->add('email', EmailType::class,[
            "label" => "Votre email",
            "attr" => [
                "placeholder" => "Entrez votre email"
            ],
            "constraints" => [
                new NotBlank(message: "Vous devez renseigner un email", groups: ['registration']),
                new Email(message: "Vous devez entrez un email correct", groups: ['registration'])
            ]
        ]);


        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Vos mot de passe doivent être identique',
            'options' => ['attr' => ['class' => 'password-field']],
            'required' => true,
            'first_options'  => [
                'label' => 'Votre mot de passe',
                "attr" => [
                    "placeholder" => "Entrez votre mot de passe"
                ],
                "constraints" => [
                    new NotBlank(null,"Vous devez indiquer un mot de passe", groups: ['registration']),
                    new Length(min: 8,max: 32, minMessage: "Le mot de passe doit faire au moins 8 caractères", maxMessage: "Le mot de passe ne dois pas dépasser 32 caractères", groups: ['registration']),
                    new Regex("/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$/", "Votre mot de passe doit contenir au moins une minuscule, une majuscule, un chiffre et un caractère spéciaux", groups: ['registration'])
                ]
            ],
            'second_options' => [
                'label' => 'Confirmer votre mot de passe',
                "attr" => [
                    "placeholder" => "Confirmer votre mot de passe"
                ]
            ],
            'mapped' => false,
        ]);

        // $builder->add('plainPassword', PasswordType::class, [
        //     'mapped' => false,
        // ]);

        $builder->add(
            'terms',
            CheckboxType::class,
            [
                'mapped' => false,
                'required' => true,
                "label" => "J'accepte les conditions générales d'utilisations"
            ]
        );

        $builder->add('submit', SubmitType::class,[
            "label" => "S'inscrire"
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
                'validation_groups' => ['registration'],
                'empty_data' => function(FormInterface $form) {
                    $user = new User();
                    $user->setContact(new Contact());

                    return $user;
                }
            ]
        );
    }
}