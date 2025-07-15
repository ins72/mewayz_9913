<?php

namespace App\Http\Controllers\Run;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Yena\YenaMail;
use Carbon\Carbon;

class CronController extends Controller{

    public function run(){
        $this->invoice();
    }

    public function invoice(){
        $invoices = Invoice::where('paid', false)->get();

        $sendReminder = function($invoice, $timeFrame){
            $mail = new YenaMail;
            $mail->send([
                'to' => ao($invoice->payer, 'email'),
                'subject' => __("Invoice Reminder - Due in :time", [
                    'time' => $timeFrame
                ]),
            ], 'invoice.reminder', [
                'invoice' => $invoice,
                'timeframe' => $timeFrame,
            ]);
        };

        foreach ($invoices as $invoice) {
            $dueDate = Carbon::parse($invoice->due);
            $now = Carbon::now();

            if(!$invoice->enable_reminder || $invoice->paid) continue;

            if ($now->diffInDays($dueDate) == 14) {
                if (!$invoice->timelines()->where('type', 'reminder_14_days')->exists()) {
                    $sendReminder($invoice, __('14 days'));
                    $invoice->addTimeline('reminder_14_days');
                }
            }

            if ($now->diffInDays($dueDate) == 7) {
                if (!$invoice->timelines()->where('type', 'reminder_7_days')->exists()) {
                    $sendReminder($invoice, __('7 days'));
                    $invoice->addTimeline('reminder_7_days');
                }
            }

            if ($now->diffInDays($dueDate) == 1) {
                if (!$invoice->timelines()->where('type', 'reminder_1_day')->exists()) {
                    $sendReminder($invoice, __('1 day'));
                    $invoice->addTimeline('reminder_1_day');
                }
            }
        }

        return 0;
    }
}
