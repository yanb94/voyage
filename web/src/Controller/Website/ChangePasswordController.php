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

use App\Form\ChangePasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sulu\Bundle\CommunityBundle\Controller\SaveMediaTrait;
use Sulu\Bundle\CommunityBundle\Controller\AbstractController;
use Sulu\Bundle\CommunityBundle\DependencyInjection\Configuration;

/**
 * Handle change password.
 */
class ChangePasswordController extends AbstractController
{
    use SaveMediaTrait {
        getSubscribedServices as getSubscribedServicesOfSaveMediaTrait;
    }

    const TYPE = "password_change";

    /**
     * Handle profile form.
     * @Route("/change-password", name="password_change")
     */
    public function indexAction(Request $request): Response
    {
        $communityManager = $this->getCommunityManager($this->getWebspaceKey());

        $user = $this->getUser();

        // Create Form
        $form = $this->createForm(
            ChangePasswordType::class,
            $user,
        );

        $form->handleRequest($request);

        // Handle Form Success
        if ($form->isSubmitted() && $form->isValid()) {
            
            if($this->getUserPasswordEncoder()->isPasswordValid($user,$form->get('oldPassword')->getData()))
            {
                // Set Password and Salt
                $user = $this->setUserPasswordAndSalt($form->getData(), $form);

                if (!$user->getLocale()) {
                    $user->setLocale($request->getLocale());
                }

                $this->saveMediaFields($form, $user, $request->getLocale());

                // Register User
                $communityManager->saveProfile($user);
                $this->saveEntities();

                // // Redirect
                // $redirectTo = $communityManager->getConfigTypeProperty(self::TYPE, Configuration::REDIRECT_TO);

                // if ($redirectTo) {
                //     return $this->redirect($redirectTo);
                // }

                $this->addFlash("success","Vos infos ont bien été modifié");
            }
            else
            {
                $this->addFlash("error", "Votre ancien mot de passe n'est pas correct");
            }

            return $this->redirectToRoute("password_change");
        }

        return $this->render(
            "community/".self::TYPE.".html.twig",
            [
                'form' => $form->createView(),
                
            ]
        );
    }

    /**
     * @return array<string|int, string>
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), self::getSubscribedServicesOfSaveMediaTrait());
    }
}
