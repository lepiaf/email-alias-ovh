<?php
declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

class EmailRedirection
{
    /**
     * @Groups({"create", "detail"})
     */
    private ?string $from;

    /**
     * @Groups({"create", "detail"})
     */
    private ?string $to;

    private ?string $domain;

    /**
     * @Groups({"detail"})
     */
    private ?int $id;

    /**
     * @Groups({"create"})
     */
    private bool $localCopy = false;

    public function __construct(?string $from = null, ?string $to = null, ?string $domain = null, ?int $id = null)
    {
        $this->from = $from;
        $this->to = $to;
        $this->domain = $domain;
        $this->id = $id;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function getTo(): ?string
    {
        return $this->to;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setFrom(string $from): void
    {
        $this->from = $from;
    }

    public function setTo(string $to): void
    {
        $this->to = $to;
    }

    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function isLocalCopy(): bool
    {
        return $this->localCopy;
    }

    public function setLocalCopy(bool $localCopy): void
    {
        $this->localCopy = $localCopy;
    }
}
