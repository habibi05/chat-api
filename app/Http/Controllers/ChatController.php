<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    protected $user;
    
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function detail($sender, $receiver)
    {
        // Get data chat
        $chat = Chat::where([
                        ['id_user_sender', $sender],
                        ['id_user_receiver', $receiver],
                        ])
                    ->orWhere([
                        ['id_user_sender', $receiver],
                        ['id_user_receiver', $sender],
                        ])
                    ->get();

        $response = [
            'sender' => $sender,
            'receiver' => $receiver,
            'data' => $chat
        ];
    
        return response()->json($response, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        // Validate Data
        $data = $request->only('id_user_sender', 'id_user_receiver', 'text');
        $validator = Validator::make($data, [
            'id_user_sender' => 'required',
            'id_user_receiver' => 'required',
            'text' => 'required',
        ]);

        // Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        // Request is valid, create new product
        $chat = Chat::create([
            'id_user_sender' => $request->id_user_sender,
            'id_user_receiver' => $request->id_user_receiver,
            'text' => $request->text,
        ]);

        return response()->json([
            'message' => 'Chat created successfully',
            'data' => $chat
        ], Response::HTTP_OK);
    }
}
