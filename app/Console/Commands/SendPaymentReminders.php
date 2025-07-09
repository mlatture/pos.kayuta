<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\ScheduledPayment;
use App\Models\SystemLog;

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
            ->whereBetween('payment_date', [now(), now()->addDays(3)])
            ->with('customer')
            ->get();

        if ($upcoming->isEmpty()) {
            $description = 'No scheduled payments due in the next 3 days';

            SystemLog::create([
                'transaction_type' => 'Scheduled Job',
                'status' => 'Failed',
                'description' => $description,
                'created_at' => now(),
            ]);

            $this->info($description);
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

            try {
                $customer->notify(new PaymentReminder($payment));

                $payment->reminder_sent = true;
                $payment->save();

                $logStatus = 'Success';
                $description = "Reminder email sent for payment ID {$payment->id}";
            } catch (\Exception $e) {
                $logStatus = 'Failed';
                $description = "Failed to send reminder for payment ID {$payment->id}: " . $e->getMessage();
            }

            SystemLog::create([
                'transaction_type' => 'Scheduled Job',
                'status' => $logStatus,
                'customer_name' => $customer->f_name . ' ' . $customer->l_name,
                'customer_email' => $customer->email,
                'user_id' => $customer->id,
                'description' => $description,
                'sale_amount' => $payment->amount,
                'payment_type' => $payment->payment_type,
                'created_at' => now(),
            ]);

            $this->info("Reminder {$logStatus} for {$customer->email} (Payment ID {$payment->id})");

            $logStatus === 'Success' ? $sent++ : $skipped++;
        }

        $this->line('---------');
        $this->info("Total reminders sent: {$sent}");
        $this->warn("Total skipped or failed: {$skipped}");

        return Command::SUCCESS;
    }
}
