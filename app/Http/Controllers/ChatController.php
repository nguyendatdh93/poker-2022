<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Events\TurnToPlay;
use App\Http\Requests\SendChatMessage;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Get chat rooms
     */
    public function getRooms()
    {
        return ChatRoom::select('id', 'name')
            ->enabled()
            ->orderBy('id')
            ->get()
            ->map
            ->setAppends([]);
    }

    /**
     * Get recent chat messages
     *
     * @param ChatRoom $room
     * @return mixed
     */
    public function getMessages(ChatRoom $room)
    {
        return ChatMessage::fromRoom($room->id)
            ->orderBy('id', 'desc')
            ->with('user:id,name,email,avatar')
            ->with(['recipients' => function($query) {
                $query->select('user_id AS id', 'name', 'avatar'); // email is required for avatar_url to work
            }])
            ->limit(100)
            ->get()
            ->map(function ($room) {
                $room->user->setAppends(['avatar_url']);
                $room->user->makeHidden(['email']);
                return $room;
            })
            ->reverse()
            ->values(); // converting object to array
    }

    /**
     * Send a new chat message
     *
     * @param SendChatMessage $request
     * @param ChatRoom $room
     * @return array
     */
    public function sendMessage(SendChatMessage $request, ChatRoom $room)
    {
        $user = $request->user();

        // store message
        $message = new ChatMessage();
        $message->room()->associate($room);
        $message->user()->associate($user);
        $message->message = $request->message;
        $message->save();

        if (!empty($request->recipients)) {
            $message->recipients()->attach(array_unique($request->recipients));
        }

        if ($request->get('turn_to_play') ?? 0) {
            broadcast(new TurnToPlay($room,  $message->message, $request->get('turn_to_play') ?? 0));
        } else {
            broadcast(new ChatMessageSent($room, $message));
        }

        return [
            'success' => TRUE
        ];
    }
}
