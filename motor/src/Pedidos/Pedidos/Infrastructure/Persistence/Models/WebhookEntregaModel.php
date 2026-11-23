<?php

declare(strict_types=1);

namespace Src\Pedidos\Pedidos\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEntregaModel extends Model
{
    protected $table = 'webhook_entregas';

    protected $fillable = [
        'delivery_id',
    ];
}