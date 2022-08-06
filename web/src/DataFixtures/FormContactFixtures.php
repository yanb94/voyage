<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Sulu\Bundle\FormBundle\Entity\Form;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Sulu\Bundle\FormBundle\Entity\FormField;
use Sulu\Bundle\FormBundle\Entity\FormTranslation;
use Sulu\Bundle\FormBundle\Entity\FormFieldTranslation;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FormContactFixtures extends Fixture
{
    const LOCALE = 'fr';

    public function __construct(private ParameterBagInterface $params){

    }

    public function getOrder()
    {
        return 13;
    }

    public function load(ObjectManager $manager)
    {
        $form = new Form();
        $form->setDefaultLocale(self::LOCALE);

        $formTranslation = new FormTranslation();
        $formTranslation->setForm($form);
        $formTranslation->setLocale(self::LOCALE);
        $formTranslation->setTitle('Formulaire de contact');
        $formTranslation->setSubject('Message de contact reçu');
        $formTranslation->setFromEmail('blog@voyage.com');
        $formTranslation->setFromName("Blog voyage");
        $formTranslation->setToEmail($this->params->get('admin_email'));
        $formTranslation->setToName('Administrateur');
        $formTranslation->setSubmitLabel('Envoyer');
        $formTranslation->setSuccessText("Votre message a bien été envoyé. Vous recevrez une réponse de notre part dans les plus brefs délais.");
        $formTranslation->setMailText("Un nouveau message de contact vient d'être reçu");
        $formTranslation->setDeactivateNotifyMails(false);
        $formTranslation->setSendAttachments(false);
        $formTranslation->setReplyTo(true);
        $formTranslation->setDeactivateCustomerMails(true);

        $form->addTranslation($formTranslation);

        $formFields = [
            [
                'key' => 'lastname',
                'type' => 'lastName',
                'width' => 'full',
                'required' => true,
                'default_locale' => self::LOCALE,
                'title' => 'Votre Nom',
                'placeholder' => "Entrez votre nom"
            ],
            [
                'key' => 'firstname',
                'type' => 'firstName',
                'width' => 'full',
                'required' => true,
                'default_locale' => self::LOCALE,
                'title' => 'Votre prénom',
                'placeholder' => "Entrez votre prénom"
            ],
            [
                'key' => 'email',
                'type' => 'email',
                'width' => 'full',
                'required' => true,
                'default_locale' => self::LOCALE,
                'title' => 'Votre email',
                'placeholder' => "Entrez votre email"
            ],
            [
                'key' => 'message',
                'type' => 'textarea',
                'width' => 'full',
                'required' => true,
                'default_locale' => self::LOCALE,
                'title' => 'Votre message',
                'placeholder' => "Entrez votre message"
            ],
        ];

        $i = 0;

        foreach ($formFields as $formField) {
            $fieldObject = $this->createFormField($formField, $i);
            $fieldObject->setForm($form);
            $form->addField($fieldObject);

            $i++;
        }

        $manager->persist($form);
        $manager->flush();
    }

    private function createFormField(array $fieldData, int $order): FormField
    {
        $formField = new FormField();
        $formFieldTranslation = new FormFieldTranslation();
        $formFieldTranslation->setField($formField);
        $formField->addTranslation($formFieldTranslation);

        $formField->setKey($fieldData['key']);
        $formField->setType($fieldData['type']);
        $formField->setWidth($fieldData['width']);
        $formField->setRequired($fieldData['required']);
        $formField->setDefaultLocale($fieldData['default_locale']);
        $formField->setOrder($order);

        $formFieldTranslation->setLocale(self::LOCALE);
        $formFieldTranslation->setTitle($fieldData['title']);
        $formFieldTranslation->setPlaceholder($fieldData['placeholder'] ?? null);

        return $formField;
    }
}