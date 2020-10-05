<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\AlreadyLoggedException;
use App\Provider\Ovh;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class LoginController extends AbstractController
{
    private RouterInterface $router;
    private Ovh $ovh;

    public function __construct(RouterInterface $router, Ovh $ovh)
    {
        $this->router = $router;
        $this->ovh = $ovh;
    }

    /**
     * @Route("/login", methods={"GET"})
     */
    public function login(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_app_me');
        }

        return $this->render('login.html.twig');
    }

    /**
     * @Route("/register", methods={"GET"})
     */
    public function register(): Response
    {
        return $this->render('register.html.twig');
    }

    /**
     * @Route("/login/provider/ovh", methods={"GET"})
     */
    public function loginProviderOvh(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        try {
            $redirectUri = $request->getUriForPath($this->router->generate('app_app_me'));
            $credentials = $this->ovh->login($redirectUri);
        } catch (AlreadyLoggedException $alreadyLoggedException) {
            return $this->redirectToRoute('app_app_me');
        }

        return $this->render('loginProviderOvh.html.twig', ['validationUrl' => $credentials]);
    }

    /**
     * @Route("/logout")
     */
    public function logout(): Response
    {
        return $this->redirectToRoute('app_login_login');
    }
}
