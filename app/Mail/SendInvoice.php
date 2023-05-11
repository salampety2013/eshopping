<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendInvoice extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
	 public $order_details;
    public function __construct($order_details)
    {
       $this->order_details=$order_details;
	   
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
     //   return $this->view('view.name');realpath('assets/images/invoices_pdf/'.invoice-'.$order_details->id'.'.pdf'
        //return $this->subject(__('general.send_invoice'))->view('emails.SendInvoiceEmail')->attach(realpath('assets/images/invoices_pdf/invoice-'.$this->order_details->id.'.pdf'))->with(['order_details'=>$this->order_details]);
        return $this->subject(__('general.send_invoice'))->view('emails.SendInvoiceEmail')->attach(realpath('assets/images/invoices_pdf/invoice-'.$this->order_details->id.'.pdf'))->with(['order_details'=>$this->order_details]);
    }
}
