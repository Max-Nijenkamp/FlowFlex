<?php

declare(strict_types=1);

namespace App\Exceptions\Finance;

use RuntimeException;

class CannotVoidPaidInvoiceException extends RuntimeException {}
