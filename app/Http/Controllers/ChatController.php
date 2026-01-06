<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /* ======================= CUSTOMER: GET OR CREATE CONVERSATION ======================= */
    public function getOrCreateConversation(Request $request)
    {
        try {
            $customer = $request->user()->customer;

            if (!$customer) {
                return response()->json(['message' => 'Customer not found'], 404);
            }

            $conversation = ChatConversation::firstOrCreate(
                ['customer_id' => $customer->customer_id, 'status' => 'open'],
                ['customer_id' => $customer->customer_id]
            );

            $messages = $conversation->messages()
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json([
                'conversation_id' => $conversation->conversation_id,
                'messages' => $messages->map(fn($msg) => [
                    'message_id' => $msg->message_id,
                    'sender_type' => $msg->sender_type,
                    'message' => $msg->message,
                    'created_at' => $msg->created_at->toISOString(),
                    'is_read' => $msg->is_read
                ])
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error loading conversation', 'error' => $e->getMessage()], 500);
        }
    }

    /* ======================= CUSTOMER: SEND MESSAGE ======================= */
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'conversation_id' => 'required|exists:chat_conversations,conversation_id',
                'message' => 'required|string|max:1000'
            ]);

            $customer = $request->user()->customer;

            if (!$customer) {
                return response()->json(['message' => 'Customer not found'], 404);
            }

            // Kiểm tra conversation thuộc về customer này
            $conversation = ChatConversation::where('conversation_id', $request->conversation_id)
                ->where('customer_id', $customer->customer_id)
                ->first();

            if (!$conversation) {
                return response()->json(['message' => 'Conversation not found'], 404);
            }

            DB::beginTransaction();

            $message = ChatMessage::create([
                'conversation_id' => $request->conversation_id,
                'sender_type' => 'customer',
                'sender_id' => $customer->customer_id,
                'message' => $request->message
            ]);

            // Cập nhật updated_at của conversation
            $conversation->touch();

            DB::commit();

            return response()->json([
                'message_id' => $message->message_id,
                'sender_type' => $message->sender_type,
                'message' => $message->message,
                'created_at' => $message->created_at->toISOString()
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error sending message', 'error' => $e->getMessage()], 500);
        }
    }

    /* ======================= CUSTOMER: GET NEW MESSAGES (POLLING) ======================= */
    public function getNewMessages(Request $request, $conversationId)
    {
        try {
            $request->validate([
                'after_message_id' => 'nullable|integer'
            ]);

            $customer = $request->user()->customer;

            // Kiểm tra conversation thuộc về customer này
            $conversation = ChatConversation::where('conversation_id', $conversationId)
                ->where('customer_id', $customer->customer_id)
                ->first();

            if (!$conversation) {
                return response()->json(['message' => 'Conversation not found'], 404);
            }

            $afterMessageId = $request->query('after_message_id', 0);

            $messages = ChatMessage::where('conversation_id', $conversationId)
                ->where('message_id', '>', $afterMessageId)
                ->orderBy('created_at', 'asc')
                ->get();

            // Đánh dấu tin nhắn từ admin là đã đọc
            ChatMessage::where('conversation_id', $conversationId)
                ->where('sender_type', 'admin')
                ->where('is_read', false)
                ->update(['is_read' => true]);

            return response()->json([
                'messages' => $messages->map(fn($msg) => [
                    'message_id' => $msg->message_id,
                    'sender_type' => $msg->sender_type,
                    'message' => $msg->message,
                    'created_at' => $msg->created_at->toISOString()
                ])
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error loading messages', 'error' => $e->getMessage()], 500);
        }
    }

    /* ======================= ADMIN: GET ALL CONVERSATIONS ======================= */
    public function getConversations(Request $request)
    {
        try {
            // TODO: Uncomment khi có admin middleware
            // if (!$request->user()->is_admin) {
            //     return response()->json(['message' => 'Unauthorized'], 403);
            // }
            
            $conversations = ChatConversation::with(['customer'])
                ->withCount(['unreadCustomerMessages'])
                ->where('status', 'open')
                ->orderBy('updated_at', 'desc')
                ->get();

            return response()->json([
                'conversations' => $conversations->map(function($conv) {
                    $lastMessage = $conv->messages()->latest('created_at')->first();
                    
                    return [
                        'conversation_id' => $conv->conversation_id,
                        'customer_name' => $conv->customer->full_name ?? 'Unknown',
                        'last_message' => $lastMessage ? $lastMessage->message : '',
                        'updated_at' => $conv->updated_at->toISOString(),
                        'unread_count' => $conv->unread_customer_messages_count
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error loading conversations', 'error' => $e->getMessage()], 500);
        }
    }

    /* ======================= ADMIN: GET CONVERSATION MESSAGES ======================= */
    public function getConversationMessages($conversationId)
    {
        try {
            $conversation = ChatConversation::find($conversationId);

            if (!$conversation) {
                return response()->json(['message' => 'Conversation not found'], 404);
            }

            $messages = ChatMessage::where('conversation_id', $conversationId)
                ->orderBy('created_at', 'asc')
                ->get();

            // Đánh dấu tin nhắn từ customer là đã đọc
            ChatMessage::where('conversation_id', $conversationId)
                ->where('sender_type', 'customer')
                ->where('is_read', false)
                ->update(['is_read' => true]);

            return response()->json([
                'messages' => $messages->map(fn($msg) => [
                    'message_id' => $msg->message_id,
                    'sender_type' => $msg->sender_type,
                    'message' => $msg->message,
                    'created_at' => $msg->created_at->toISOString()
                ])
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error loading messages', 'error' => $e->getMessage()], 500);
        }
    }

    /* ======================= ADMIN: SEND MESSAGE ======================= */
    public function adminSendMessage(Request $request)
    {
        try {
            $request->validate([
                'conversation_id' => 'required|exists:chat_conversations,conversation_id',
                'message' => 'required|string|max:1000'
            ]);

            // TODO: Uncomment khi có admin middleware
            // if (!$request->user()->is_admin) {
            //     return response()->json(['message' => 'Unauthorized'], 403);
            // }

            $adminId = $request->user()->id;

            DB::beginTransaction();

            $message = ChatMessage::create([
                'conversation_id' => $request->conversation_id,
                'sender_type' => 'admin',
                'sender_id' => $adminId,
                'message' => $request->message
            ]);

            // Đánh dấu tin nhắn từ customer là đã đọc
            ChatMessage::where('conversation_id', $request->conversation_id)
                ->where('sender_type', 'customer')
                ->where('is_read', false)
                ->update(['is_read' => true]);

            // Cập nhật updated_at của conversation
            ChatConversation::where('conversation_id', $request->conversation_id)
                ->update(['updated_at' => now()]);

            DB::commit();

            return response()->json([
                'message_id' => $message->message_id,
                'sender_type' => $message->sender_type,
                'message' => $message->message,
                'created_at' => $message->created_at->toISOString()
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error sending message', 'error' => $e->getMessage()], 500);
        }
    }

    /* ======================= ADMIN: CLOSE CONVERSATION ======================= */
    public function closeConversation(Request $request, $conversationId)
    {
        try {
            $conversation = ChatConversation::find($conversationId);

            if (!$conversation) {
                return response()->json(['message' => 'Conversation not found'], 404);
            }

            $conversation->update(['status' => 'closed']);

            return response()->json(['message' => 'Conversation closed successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error closing conversation', 'error' => $e->getMessage()], 500);
        }
    }
}