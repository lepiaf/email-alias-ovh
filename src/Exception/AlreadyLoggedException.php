<?php
declare(strict_types=1);

namespace App\Exception;

class AlreadyLoggedException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Already logged.');
    }
}
