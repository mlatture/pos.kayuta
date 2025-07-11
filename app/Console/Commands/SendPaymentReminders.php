<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\ScheduledPayment;
use App\Models\SystemLog;

use App\Notifications\PaymentReminder;
use Carbon\Carbon;

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
        $now = now();
        $rangeStart = $now->copy()->startOfDay();
        $rangeEnd = $now->copy()->addDays(30)->endOfDay();

        
        // Get grouped pending & not-yet-reminded payments
        $this->info("🔁 Scanning for payments from {$rangeStart} to {$rangeEnd}");
        $paymentsGrouped = ScheduledPayment::where('reminder_sent', false)->where('status', 'Pending')->with('customer')->get()->groupBy('customer_email');
        
        if ($paymentsGrouped->isEmpty()) {
            SystemLog::create([
                'transaction_type' => 'Scheduled Job',
                'status' => 'Skipped',
                'description' => 'No pending payments due in the next 7 days.',
                'created_at' => now(),
            ]);

            $this->warn('⛔ No pending payments found in upcoming date range.');
            return Command::SUCCESS;
        }

        $sent = 0;
        $skipped = 0;

        foreach ($paymentsGrouped as $email => $payments) {
            $customer = $payments->first()->customer;


            if (!$customer) {
                $this->warn("❌ Skipped {$email} — no associated customer found.");
                $skipped++;
                continue;
            }

            $upcomingPayments = $payments->filter(function ($payment) use ($rangeStart, $rangeEnd) {
                return Carbon::parse($payment->payment_date)->between($rangeStart, $rangeEnd);
            });

            if ($upcomingPayments->isEmpty()) {
                $this->line("ℹ️ No due payments for {$email} in next 7 days.");
                continue;
            }

            try {
                // Send a grouped reminder notification
                $customer->notify(new PaymentReminder($upcomingPayments));

                // Mark only the matched ones as reminded
                foreach ($upcomingPayments as $payment) {
                    $payment->update(['reminder_sent' => true]);
                }

                SystemLog::create([
                    'transaction_type' => 'Scheduled Job',
                    'status' => 'Success',
                    'customer_name' => $customer->f_name . ' ' . $customer->l_name,
                    'customer_email' => $email,
                    'user_id' => $customer->id,
                    'description' => "Reminder sent for {$upcomingPayments->count()} scheduled payment(s)",
                    'sale_amount' => $upcomingPayments->first()->amount,
                    'payment_type' => $upcomingPayments->first()->payment_type ?? 'N/A',
                    'created_at' => now(),
                ]);

                $this->info("✅ Reminder sent to {$email} ({$upcomingPayments->count()} payment(s))");
                $sent++;
            } catch (\Exception $e) {
                SystemLog::create([
                    'transaction_type' => 'Scheduled Job',
                    'status' => 'Failed',
                    'customer_name' => $customer->f_name . ' ' . $customer->l_name,
                    'customer_email' => $email,
                    'user_id' => $customer->id,
                    'description' => '❌ Reminder failed: ' . $e->getMessage(),
                    'created_at' => now(),
                ]);

                $this->error("⚠️ Failed to send reminder to {$email}: {$e->getMessage()}");
                $skipped++;
            }
        }

        $this->line('---');
        $this->info("📬 Total reminders sent: {$sent}");
        $this->warn("⚠️ Total skipped or failed: {$skipped}");

        return Command::SUCCESS;
    }
}
