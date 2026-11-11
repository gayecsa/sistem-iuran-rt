<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'letter_request_id',
        'user_id',
        'actor_name',
        'actor_role',
        'status_sebelumnya',
        'status_baru',
        'catatan',
        'ip_address',
    ];

    public function letterRequest()
    {
        return $this->belongsTo(LetterRequest::class, 'letter_request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
