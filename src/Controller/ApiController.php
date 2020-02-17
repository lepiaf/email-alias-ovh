<?php
declare(strict_types=1);

namespace App\Controller;

use App\Dto\EmailRedirection;
use App\Provider\Ovh;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ApiController extends AbstractController
{
    /** @var Ovh */
    private $ovh;

    /** @var NormalizerInterface */
    private $normalizer;

    public function __construct(Ovh $ovh, NormalizerInterface $normalizer)
    {
        $this->ovh = $ovh;
        $this->normalizer = $normalizer;
    }

    /**
     * @Route("/api/domains")
     */
    public function listDomains(): Response
    {
        $listEmail = $this->ovh->listDomains();

        return new JsonResponse($listEmail);
    }

    /**
     * @Route("/api/email/{domain}/redirections")
     */
    public function listEmailRedirection(Request $request): Response
    {
        $listEmailRedirection = $this->ovh->listEmailRedirection($request->get('domain'));

        return new JsonResponse($listEmailRedirection);
    }

    /**
     * @Route("/api/email/{domain}/redirection/{redirectionId}", methods={"GET"})
     */
    public function getEmailRedirection(Request $request): Response
    {
        $emailRedirectionDetail = $this->ovh->getEmailRedirection($request->get('domain'), $request->get('redirectionId'));

        return new JsonResponse($this->normalizer->normalize($emailRedirectionDetail, null, ['groups' => ['detail']]));
    }

    /**
     * @Route("/api/email/{domain}/redirection/{redirectionId}", methods={"POST", "DELETE"})
     */
    public function deleteEmailRedirection(Request $request): Response
    {
        $submittedToken = $request->request->get('token');

        if (!$this->isCsrfTokenValid('delete-email-redirection', $submittedToken)) {
            throw $this->createAccessDeniedException();
        }

        $emailRedirection = new EmailRedirection(null, null, $request->get('domain'), (int) $request->get('redirectionId'));
        $this->ovh->deleteEmailRedirection($emailRedirection);

        return $this->redirectToRoute('app_app_listemailredirection', ['domain' => $emailRedirection->getDomain()]);
    }
}

