<?php
namespace App\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $email;
    protected $subject;
    protected $messageBody;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $subject, $messageBody)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->messageBody = $messageBody;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            Mail::raw($this->messageBody, function ($message) {
                $message->to($this->email)
                    ->from('contratacionlocal@niki.com.co', 'Contratación Local')
                    ->replyTo('contacto@contratacionlocal.com', 'Soporte')
                    ->subject($this->subject);
            });
        } else {
            \Log::error('Correo inválido: ' . $this->email);
        }
    }
}
