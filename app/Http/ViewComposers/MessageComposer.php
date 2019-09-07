<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App;
use Illuminate\Support\Facades\DB;
use App\Message;
use Carbon\Carbon;

class MessageComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $count = DB::table('messages')
            ->where(['messages.receiver_id'=>auth()->user()->id, 'messages.status'=>0, 'messages.deleted'=>0])
            ->count();
            
        $unread_messages = Message::Where(['messages.receiver_id'=>auth()->user()->id, 'messages.status'=>0, 'messages.deleted'=>0])
            ->limit(10)
            ->get();

        $current_time = Carbon::now();

        $view->with('unread_messages_count', $count);
        $view->with('unread_messages', $unread_messages);
        $view->with('unread_messages_current_time', $current_time);
    }
}