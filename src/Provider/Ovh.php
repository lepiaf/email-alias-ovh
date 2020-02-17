<?php
declare(strict_types=1);

namespace App\Provider;

use App\Dto\EmailRedirection;
use App\Exception\ConsumerKeyNotFoundInSessionException;
use Ovh\Api;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class Ovh implements Provider
{
    /** @var string */
    private const API_ENDPOINT = 'ovh-eu';

    /** @var string */
    private $ovhApplicationKey;

    /** @var string */
    private $ovhApplicationSecret;

    /** @var NormalizerInterface */
    private $normalizer;

    /** @var DenormalizerInterface */
    private $denormalizer;

    /** @var SessionInterface */
    private $session;

    public function __construct(
        string $ovhApplicationKey,
        string $ovhApplicationSecret,
        NormalizerInterface $normalizer,
        DenormalizerInterface $denormalizer,
        SessionInterface $session
    ) {
        $this->ovhApplicationKey = $ovhApplicationKey;
        $this->ovhApplicationSecret = $ovhApplicationSecret;
        $this->normalizer = $normalizer;
        $this->denormalizer = $denormalizer;
        $this->session = $session;
    }

    public function login(string $redirectUri): string
    {
        $rights = [
            (object) [
                'method' => 'GET',
                'path' => '/*',
            ],
            (object) [
                'method' => 'POST',
                'path' => '/email/domain/*/redirection',
            ],
            (object) [
                'method' => 'DELETE',
                'path' => '/email/domain/*/redirection/*',
            ]
        ];

        $conn = new Api($this->ovhApplicationKey, $this->ovhApplicationSecret, self::API_ENDPOINT);
        $credentials = $conn->requestCredentials($rights, $redirectUri);
        $this->session->set('consumerKey', $credentials['consumerKey']);

        return $credentials['validationUrl'];
    }

    public function me(): array
    {
        return $this->getOvhConnection()->get('/me');
    }

    public function listDomains(): array
    {
        return $this->getOvhConnection()->get('/email/domain');
    }

    public function listEmailRedirection(string $domain): array
    {
        return $this->getOvhConnection()->get(sprintf('/email/domain/%s/redirection', $domain));
    }

    public function getEmailRedirection(string $domain, string $redirectionId): EmailRedirection
    {
        $response = $this->getOvhConnection()->get(sprintf('/email/domain/%s/redirection/%d', $domain, $redirectionId));

        return $this->denormalizer->denormalize($response, EmailRedirection::class);
    }

    public function createEmailRedirection(EmailRedirection $emailRedirection): EmailRedirection
    {
        $response = $this->getOvhConnection()
            ->post(
                sprintf('/email/domain/%s/redirection', $emailRedirection->getDomain()),
                $this->normalizer->normalize($emailRedirection, null, ['groups' => ['create']]),
            );
        dump($response);
        $emailRedirection->setId($response['id']);

        return $emailRedirection;
    }

    public function deleteEmailRedirection(EmailRedirection $emailRedirection): void
    {
        $this->getOvhConnection()
            ->delete(
                sprintf(
                    '/email/domain/%s/redirection/%d',
                    $emailRedirection->getDomain(),
                    $emailRedirection->getId()
                )
            );
    }

    private function getOvhConnection(): Api
    {
        if (!$this->session->has('consumerKey') || !$this->session->get('consumerKey')) {
            throw new ConsumerKeyNotFoundInSessionException();
        }

        return new Api(
            $this->ovhApplicationKey,
            $this->ovhApplicationSecret,
            self::API_ENDPOINT,
            $this->session->get('consumerKey')
        );
    }
}
