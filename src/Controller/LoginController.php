<?php
declare(strict_types=1);

namespace App\Controller;

use App\Exception\AlreadyLoggedException;
use App\Provider\Ovh;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class LoginController extends AbstractController
{
    /** @var SessionInterface */
    private $session;

    /** @var RouterInterface */
    private $router;

    /** @var Ovh */
    private $ovh;

    public function __construct(SessionInterface $session, RouterInterface $router, Ovh $ovh)
    {
        $this->session = $session;
        $this->router = $router;
        $this->ovh = $ovh;
    }

    /**
     * @Route("/login")
     */
    public function login(Request $request): Response
    {
        try {
            $redirectUri = $request->getUriForPath($this->router->generate('app_app_me'));
            $credentials = $this->ovh->login($redirectUri);
        } catch (AlreadyLoggedException $alreadyLoggedException) {
            return $this->redirectToRoute('app_app_me');
        }

        return $this->render(
            'login.html.twig',
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
        $this->session->clear();

        return $this->redirectToRoute('app_login_login');
    }
}
