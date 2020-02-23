<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Webauthn\PublicKeyCredentialSource as BasePublicKeyCredentialSource;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PublicKeyCredentialSourceRepository")
 */
class PublicKeyCredentialSource extends BasePublicKeyCredentialSource
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private UuidInterface $id;

    public function getId(): UuidInterface
    {
        return $this->id;
    }
}
