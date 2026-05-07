<?php

namespace App\Notifications\Finance;

use App\Models\Finance\Expense;
use App\Notifications\FlowFlexNotification;
use Illuminate\Notifications\Messages\MailMessage;

class ExpenseSubmittedNotification extends FlowFlexNotification
{
    public function __construct(public readonly Expense $expense) {}

    public function notificationType(): string
    {
        return 'finance.expense.submitted';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Expense Submitted for Approval')
            ->greeting('Expense Submitted')
            ->line("An expense has been submitted for approval.")
            ->line("Description: {$this->expense->description}.")
            ->line("Amount: {$this->expense->currency} {$this->expense->amount}.")
            ->action('Review Expense', url('/finance/expenses/' . $this->expense->id . '/edit'))
            ->salutation('The FlowFlex Platform');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => $this->notificationType(),
            'expense_id'  => $this->expense->id,
            'description' => $this->expense->description,
            'amount'      => $this->expense->amount,
            'currency'    => $this->expense->currency,
        ];
    }
}
