<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Business + Data Access layer ek hi jagah: Eloquent Model khud simple
// business rule (total calculate karna) bhi rakhta hai aur DB access bhi.
class Order extends Model
{
    protected $fillable = ['customer_id', 'total'];

    public static function createFromValidated(array $data): self
    {
        $order = static::create([
            'customer_id' => $data['customer_id'],
            'total'       => 0,
        ]);

        foreach ($data['items'] as $item) {
            $order->items()->create($item);
        }

        $order->total = $order->items()->sum('subtotal');
        $order->save();

        return $order;
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
