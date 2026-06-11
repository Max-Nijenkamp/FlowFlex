<?php

declare(strict_types=1);

namespace App\Contracts\CRM;

use App\Models\CRM\SequenceEnrolment;

interface SequenceServiceInterface
{
    public function enrol(string $sequenceId, string $contactId, ?string $dealId = null): SequenceEnrolment;

    /** @return array{advanced: int, completed: int, failed: int} */
    public function advanceDue(): array;

    public function pause(string $enrolmentId): void;

    public function resume(string $enrolmentId): void;

    public function unenrol(string $enrolmentId): void;

    public function pauseOnReply(string $contactId): void;

    public function enrolByTrigger(string $triggerType, string $contactId, ?string $dealId = null): int;
}
