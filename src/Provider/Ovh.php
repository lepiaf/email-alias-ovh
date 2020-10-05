<?php
declare(strict_types=1);

namespace App\Provider;

use App\Entity\User;
use App\Exception\ConsumerKeyNotFoundInSessionException;
use App\Model\EmailRedirection;
use Doctrine\Persistence\ManagerRegistry;
use Ovh\Api;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class Ovh
{
    /** @var string */
    private const API_ENDPOINT = 'ovh-eu';
    private string $ovhApplicationKey;
    private string $ovhApplicationSecret;
    private NormalizerInterface $normalizer;
    private DenormalizerInterface $denormalizer;
    private Security $security;

    private ManagerRegistry $registry;

    public function __construct(
        string $ovhApplicationKey,
        string $ovhApplicationSecret,
        Security $security,
        ManagerRegistry $registry,
        NormalizerInterface $normalizer,
        DenormalizerInterface $denormalizer
    ) {
        $this->ovhApplicationKey = $ovhApplicationKey;
        $this->ovhApplicationSecret = $ovhApplicationSecret;
        $this->security = $security;
        $this->registry = $registry;
        $this->normalizer = $normalizer;
        $this->denormalizer = $denormalizer;
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
        ];

        $conn = new Api($this->ovhApplicationKey, $this->ovhApplicationSecret, self::API_ENDPOINT);
        $credentials = $conn->requestCredentials($rights, $redirectUri);

        $this->security->getUser()->setConsumerKey($credentials['consumerKey']);
        $this->registry->getManager()->flush();

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
        $user = $this->security->getUser();
        if (!$user instanceof User || !$user->getConsumerKey()) {
            throw new ConsumerKeyNotFoundInSessionException();
        }

        return new Api(
            $this->ovhApplicationKey,
            $this->ovhApplicationSecret,
            self::API_ENDPOINT,
            $user->getConsumerKey()
        );
    }
}
