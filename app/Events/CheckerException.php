<?php

namespace App\Events;

use App\CheckerLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CheckerException
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public CheckerLog $checkerLog;

    public function __construct(CheckerLog $checkerLog)
    {
        $this->checkerLog = $checkerLog;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
