<?php

namespace App\Yena;
use Illuminate\Support\Facades\Mail;

class YenaMail{
    public function send($mail = [], $template = false, $variables = []){
        $body = ao($mail, 'body');
        if($template){
            $body = $this->template($template, $variables);
        }

        if(!ao($mail, 'to')) return;

        try {
            $send = Mail::send([], [], function ($message) use ($mail, $body) {
                !empty(ao($mail, 'from')) ? $message->from(ao($mail, 'from')) : '';
                
                $message->to(ao($mail, 'to'));
                $message->subject(ao($mail, 'subject'));
                $message->html($body);
            });

            return $send;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function template($template, $variables = []){
        return view("email.templates.$template", $variables)->render();
    }

    public static function notify_admin($subject, $template, $extra = []){
        // Emails
        $emails = settings('notification.emails');
        $emails = explode(',', $emails);
        $emails = str_replace(' ', '', $emails);

        // Email class
        $email = new \App\Yena\YenaMail;
        // Get email template
        $template = $email->template($template, $extra);
        // Email array
        $mail = [
            'to' => $emails,
            'subject' => $subject,
            'body' => $template
        ];

        // Send Email
        return $email->send($mail);
    }
}
