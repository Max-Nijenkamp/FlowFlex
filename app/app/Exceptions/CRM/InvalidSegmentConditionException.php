<?php

declare(strict_types=1);

namespace App\Exceptions\CRM;

use Exception;

class InvalidSegmentConditionException extends Exception
{
    protected $message = 'Segment condition uses an unknown field or operator.';
}
