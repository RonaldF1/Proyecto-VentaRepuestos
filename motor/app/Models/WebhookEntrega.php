<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEntrega extends Model
{
    protected $table = 'webhook_entregas';

    protected $fillable = [
        'delivery_id',
    ];
}
