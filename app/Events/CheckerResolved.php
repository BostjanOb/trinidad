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

class CheckerResolved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private CheckerLog $checkerLog;

    public function __construct(CheckerLog $checkerLog)
    {
        //
        $this->checkerLog = $checkerLog;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
