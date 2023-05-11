<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
class NewOrderNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
	 public $order_id;
	 public $grand_total;
	 public $user_name;
	 public $date;
	 public $time;
 
    public function __construct($data = [])
    {
		 
       return	 $this->order_id=$data['order_id'];
       	 $this->grand_total=$data['grand_total']; 
       	 $this->user_name=$data['user_name']; 
       	 $this->date=date('d-m-Y',strtotime(Carbon::now()));
       	 $this->time=date('h:i A',strtotime(Carbon::now()));
        	 $this->msg=$data['msg'];
  
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
	  
	//  return new PrivateChannel('channel-name');
	  return ['new-order-notification'];
    }
}
