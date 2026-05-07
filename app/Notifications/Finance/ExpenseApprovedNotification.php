<?php

namespace App\Notifications\Finance;

use App\Models\Finance\Expense;
use App\Notifications\FlowFlexNotification;
use Illuminate\Notifications\Messages\MailMessage;

class ExpenseApprovedNotification extends FlowFlexNotification
{
    public function __construct(public readonly Expense $expense) {}

    public function notificationType(): string
    {
        return 'finance.expense.approved';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Expense Approved')
            ->greeting('Good news!')
            ->line("Your expense has been approved.")
            ->line("Description: {$this->expense->description}.")
            ->line("Amount: {$this->expense->currency} {$this->expense->amount}.")
            ->action('View Expense', url('/finance/expenses/' . $this->expense->id . '/edit'))
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
