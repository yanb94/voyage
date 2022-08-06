<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Sulu\Bundle\SecurityBundle\Entity\User;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ProfileType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // $builder->add('plainPassword', PasswordType::class, [
        //     'mapped' => false,
        //     'required' => false,
        // ]);

        $builder->add('lastName', TextType::class, [
            'property_path' => 'contact.lastName',
            'label' => "Votre nom",
            "constraints" => [
                new Regex("/^[a-zA-Z-'À-ÖØ-öø-ÿ]+$/",message: "Vous devez entrez un nom de famille correct", groups: ['profile']),
                new Length(min:2, max: 50, minMessage: "Le nom de famille doit faire au moins 2 caractères", maxMessage: "Le nom de famille ne dois pas dépasser 50 caractères", groups: ['profile'])
            ]
       ]);
        
        $builder->add('firstName', TextType::class, [
            'property_path' => 'contact.firstName',
            'label' => "Votre prénom",
            "constraints" => [
                new Regex("/^[a-zA-Z-'À-ÖØ-öø-ÿ]+$/",message: "Vous devez entrez un prénom correct", groups: ['profile']),
                new Length(min:2, max: 50, minMessage: "Le prénom doit faire au moins 2 caractères", maxMessage: "Le prénom ne dois pas dépasser 50 caractères", groups: ['profile'])
            ]
        ]);

        // $builder->add('mainEmail', EmailType::class, [
        //     'property_path' => 'contact.mainEmail',
        //     'label' => "Votre email"
        // ]);       
        
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
                'validation_groups' => ['profile'],
            ]
        );
    }
}