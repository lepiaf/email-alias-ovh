<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\Type\EmailRedirectionType;
use App\Model\EmailRedirection;
use App\Provider\Ovh;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Webauthn\Bundle\Service\PublicKeyCredentialCreationOptionsFactory;

class AppController extends AbstractController
{
    private Ovh $ovh;
    private ManagerRegistry $managerRegistry;
    private PublicKeyCredentialCreationOptionsFactory $publicKeyCredentialCreationOptionsFactory;

    public function __construct(
        Ovh $ovh,
        ManagerRegistry $managerRegistry,
        PublicKeyCredentialCreationOptionsFactory $publicKeyCredentialCreationOptionsFactory
    ) {
        $this->ovh = $ovh;
        $this->managerRegistry = $managerRegistry;
        $this->publicKeyCredentialCreationOptionsFactory = $publicKeyCredentialCreationOptionsFactory;
    }

    /**
     * @Route("/")
     */
    public function home(): Response
    {
        return $this->redirectToRoute('app_login_login');
    }

    /**
     * @Route("/me")
     */
    public function me(): Response
    {
        $me = $this->ovh->me();

        return $this->render(
            'me.html.twig',
            [
                'firstName' => $me['firstname'],
                'name' => $me['name'],
                'consumerKey' => 'consumerKey'
            ]
        );
    }

    /**
     * @Route("/email/{domain}/redirection/create")
     */
    public function createEmailRedirection(Request $request)
    {
        $form = $this->createForm(
            EmailRedirectionType::class,
            new EmailRedirection(null, null, $request->get('domain'))
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->ovh->createEmailRedirection($form->getData());

            return $this->redirectToRoute(
                'app_app_listemailredirection',
                ['domain' => $request->get('domain')]
            );
        }
        return $this->render('createEmailRedirection.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/email/{domain}/redirections")
     */
    public function listEmailRedirection(Request $request): Response
    {
        return $this->render(
            'listEmailRedirection.html.twig',
            ['domain' => $request->get('domain')]
        );
    }
}

