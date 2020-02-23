<?php
declare(strict_types=1);

namespace App\Provider;

use App\Model\EmailRedirection;

interface Provider
{
    public function me(): array;

    public function listDomains(): array;

    public function listEmailRedirection(string $domain): array;

    public function getEmailRedirection(string $domain, string $redirectionId): EmailRedirection;

    public function createEmailRedirection(EmailRedirection $emailRedirection): EmailRedirection;

    public function deleteEmailRedirection(EmailRedirection $emailRedirection): void;
}
