<?php
declare(strict_types=1);

namespace App\Controller;

use App\Exception\AlreadyLoggedException;
use App\Provider\Ovh;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Webauthn\Bundle\Service\PublicKeyCredentialCreationOptionsFactory;

class LoginController extends AbstractController
{
    private RouterInterface $router;
    private Ovh $ovh;
    private ManagerRegistry $managerRegistry;
    private PublicKeyCredentialCreationOptionsFactory $publicKeyCredentialCreationOptionsFactory;

    public function __construct(
        RouterInterface $router,
        Ovh $ovh,
        ManagerRegistry $managerRegistry,
        PublicKeyCredentialCreationOptionsFactory $publicKeyCredentialCreationOptionsFactory
    ) {
        $this->router = $router;
        $this->ovh = $ovh;
        $this->managerRegistry = $managerRegistry;
        $this->publicKeyCredentialCreationOptionsFactory = $publicKeyCredentialCreationOptionsFactory;
    }

    /**
     * @Route("/login", methods={"GET"})
     */
    public function login(Request $request): Response
    {
        return $this->render(
            'login.html.twig',
            [

            ]
        );
    }

    /**
     * @Route("/register", methods={"GET"})
     */
    public function register(Request $request): Response
    {
        return $this->render(
            'register.html.twig',
            [

            ]
        );
    }

    /**
     * @Route("/login/provider/ovh", methods={"GET"})
     */
    public function loginProviderOvh(Request $request): Response
    {
        try {
            $redirectUri = $request->getUriForPath($this->router->generate('app_app_me'));
            $credentials = $this->ovh->login($redirectUri);
        } catch (AlreadyLoggedException $alreadyLoggedException) {
            return $this->redirectToRoute('app_app_me');
        }

        return $this->render(
            'loginProviderOvh.html.twig',
            [
                'validationUrl' => $credentials,
            ]
        );
    }

    /**
     * @Route("/logout")
     */
    public function logout(): Response
    {
        return $this->redirectToRoute('app_login_login');
    }
}
