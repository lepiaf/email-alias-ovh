<?php
declare(strict_types=1);

namespace App\Exception;

class ConsumerKeyNotFoundInSessionException extends \OutOfRangeException
{
    public function __construct()
    {
        parent::__construct('Consumer key not found in session.');
    }
}
