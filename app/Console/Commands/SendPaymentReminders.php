<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\ScheduledPayment;

use App\Notifications\PaymentReminder;

class SendPaymentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for upcoming scheduled payments';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $upcoming = ScheduledPayment::where('reminder_sent', false)
            ->whereBetween('created_at', [now(), now()->addDays(3)])
            ->with('customer')
            ->get();

        if ($upcoming->isEmpty()) {
            $this->info('No scheduled payments due in the next 3 days.');
            return Command::SUCCESS;
        }

        $sent = 0;
        $skipped = 0;

        foreach ($upcoming as $payment) {
            $customer = $payment->customer;

            if (!$customer) {
                $this->warn("Skipped payment ID {$payment->id} (no customer)");
                $skipped++;
                continue;
            }

            $customer->notify(new PaymentReminder($payment));

            $payment->reminder_sent = true;
            $payment->save();

            $this->info("Reminder sent to {$customer->email} for {$payment->amount}");
            $sent++;
        }

        $this->line('---------');
        $this->info("Total reminders sent: {$sent}");
        $this->warn("Total skipped (no customer): {$skipped}");
    

        return Command::SUCCESS;
    }
}
