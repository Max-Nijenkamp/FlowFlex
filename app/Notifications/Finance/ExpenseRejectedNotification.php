<?php

namespace App\Notifications\Finance;

use App\Models\Finance\Expense;
use App\Notifications\FlowFlexNotification;
use Illuminate\Notifications\Messages\MailMessage;

class ExpenseRejectedNotification extends FlowFlexNotification
{
    public function __construct(public readonly Expense $expense) {}

    public function notificationType(): string
    {
        return 'finance.expense.rejected';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Expense Rejected')
            ->greeting('Expense Update')
            ->line("Your expense submission has been rejected.")
            ->line("Description: {$this->expense->description}.")
            ->line("Reason: {$this->expense->rejection_reason}.")
            ->action('View Expense', url('/finance/expenses/' . $this->expense->id . '/edit'))
            ->salutation('The FlowFlex Platform');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'             => $this->notificationType(),
            'expense_id'       => $this->expense->id,
            'description'      => $this->expense->description,
            'rejection_reason' => $this->expense->rejection_reason,
        ];
    }
}
