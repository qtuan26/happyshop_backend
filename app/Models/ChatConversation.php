<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    use HasFactory;
    protected $table = 'chat_conversations';
    protected $primaryKey = 'conversation_id';
    
    protected $fillable = [
        'customer_id',
        'status'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id', 'conversation_id');
    }

    public function lastMessage()
    {
        return $this->hasOne(ChatMessage::class, 'conversation_id', 'conversation_id')
            ->latest('created_at');
    }

    public function unreadCustomerMessages()
    {
        return $this->messages()
            ->where('sender_type', 'customer')
            ->where('is_read', false);
    }
}
