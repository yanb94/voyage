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

use App\Form\ChangeEmailType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sulu\Bundle\SecurityBundle\Entity\UserRepository;
use Sulu\Bundle\CommunityBundle\Controller\SaveMediaTrait;
use Sulu\Bundle\CommunityBundle\Controller\AbstractController;
use Sulu\Bundle\CommunityBundle\DependencyInjection\Configuration;
use Sulu\Component\Security\Authentication\UserRepositoryInterface;

/**
 * Handle change email page.
 */
class ChangeEmailController extends AbstractController
{
    use SaveMediaTrait {
        getSubscribedServices as getSubscribedServicesOfSaveMediaTrait;
    }

    const TYPE = "email_change";

    /**
     * Handle profile form.
     * @Route("/change-email", name="email_change")
     */
    public function indexAction(Request $request): Response
    {
        $communityManager = $this->getCommunityManager($this->getWebspaceKey());

        $user = $this->getUser();

        // Create Form
        $form = $this->createForm(
            ChangeEmailType::class,
            $user,
        );

        $form->handleRequest($request);

        // Handle Form Success
        if ($form->isSubmitted() && $form->isValid()) {

            if (!$user->getLocale()) {
                $user->setLocale($request->getLocale());
            }

            $this->saveMediaFields($form, $user, $request->getLocale());

            // Register User
            $user->getContact()->setMainEmail($user->getEmail());
            $communityManager->saveProfile($user);
            $this->saveEntities();

            $this->addFlash('success',"Votre email a bien été modifié");

            return $this->redirectToRoute("email_change");
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
