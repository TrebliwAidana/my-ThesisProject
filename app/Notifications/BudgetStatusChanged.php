<?php

namespace App\Notifications;

use App\Models\Budget;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BudgetStatusChanged extends Notification
{
    use Queueable;

    protected $budget;
    protected $newStatus;

    public function __construct(Budget $budget, $newStatus)
    {
        $this->budget = $budget;
        $this->newStatus = $newStatus;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Budget Request {$this->newStatus}")
            ->line("Your budget request '{$this->budget->title}' has been {$this->newStatus}.")
            ->action('View Budget', url('/budgets/' . $this->budget->id))
            ->line('Thank you for using our system.');
    }

    public function toArray($notifiable)
    {
        return [
            'budget_id' => $this->budget->id,
            'title' => $this->budget->title,
            'status' => $this->newStatus,
            'message' => "Budget request '{$this->budget->title}' has been {$this->newStatus}.",
        ];
    }
}