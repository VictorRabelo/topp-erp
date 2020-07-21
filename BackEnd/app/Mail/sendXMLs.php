<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class sendXMLs extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($dados)
    {
        $this->dados = (array) $dados;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $file = $this->dados['file'];
        return $this->view('mails.template-sendXML', ['dados' => $this->dados])
            ->subject('XMLs Mensal')
            ->attach($file);
    }
}
