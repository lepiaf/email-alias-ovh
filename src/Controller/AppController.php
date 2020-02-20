<?php
declare(strict_types=1);

namespace App\Controller;

use App\Dto\EmailRedirection;
use App\Form\Type\EmailRedirectionType;
use App\Provider\Ovh;
use App\Webauthn\PublicKeyCredentialSourceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\Server;

class AppController extends AbstractController
{
    /** @var SessionInterface */
    private $session;

    /** @var Ovh */
    private $ovh;

    public function __construct(SessionInterface $session, Ovh $ovh)
    {
        $this->session = $session;
        $this->ovh = $ovh;
    }

    /**
     * @Route("/")
     */
    public function home(): Response
    {
        return $this->redirectToRoute('app_app_me');
    }

    /**
     * @Route("/login/passwordless")
     */
    public function loginPasswordless(): Response
    {
        $rpEntity = new PublicKeyCredentialRpEntity(
            'Webauthn Server',
            'localhost'
        );
        $publicKeyCredentialSourceRepository = new PublicKeyCredentialSourceRepository();

        $server = new Server(
            $rpEntity,
            $publicKeyCredentialSourceRepository,
            null
        );
        return $this->render('register.html.twig');
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
                'consumerKey' => $this->session->get('consumerKey')
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

