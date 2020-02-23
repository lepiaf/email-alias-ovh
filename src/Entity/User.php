<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Webauthn\PublicKeyCredentialUserEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User extends PublicKeyCredentialUserEntity implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    protected $id;

    /**
     * @ORM\Column(type="json")
     */
    private $roles;

    /**
     * @var PublicKeyCredentialSource[]
     * @ORM\ManyToMany(targetEntity="App\Entity\PublicKeyCredentialSource")
     * @ORM\JoinTable(name="users_user_handles",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_handle", referencedColumnName="id", unique=true)}
     *      )
     */
    protected $publicKeyCredentialSources;

    public function __construct(string $id, string $name, string $displayName, array $roles)
    {
        parent::__construct($name, $id, $displayName);
        $this->roles = $roles;
        $this->publicKeyCredentialSources = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return (string) $this->name;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getPassword(): void
    {
    }

    public function getSalt(): void
    {
    }

    public function eraseCredentials(): void
    {
    }

    /**
     * @return PublicKeyCredentialSource[]
     */
    public function getPublicKeyCredentialSources(): array
    {
        return $this->publicKeyCredentialSources->getValues();
    }

    public function addPublicKeyCredentialSources(PublicKeyCredentialSource $publicKeyCredentialSource)
    {
        $this->publicKeyCredentialSources->add($publicKeyCredentialSource);
    }

    public function removePublicKeyCredentialSources(PublicKeyCredentialSource $publicKeyCredentialSource)
    {
        $this->publicKeyCredentialSources->remove($publicKeyCredentialSource);
    }
}
