<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class ShopController extends Controller
{
    private function getProducts(): array
    {
        return [
            ['id' => 1, 'name' => 'Bluetooth Kõrvaklapid', 'price' => 49.99, 'description' => 'Kvaliteetsed juhtmevabad kõrvaklapid 30h akuga.', 'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=300&fit=crop'],
            ['id' => 2, 'name' => 'Mehhaaniline Klaviatuur', 'price' => 89.99, 'description' => 'RGB taustvalgustusega mängurite klaviatuur.', 'image' => 'https://images.unsplash.com/photo-1541140532154-b024d705b90a?w=400&h=300&fit=crop'],
            ['id' => 3, 'name' => 'Juhtmevaba Hiir', 'price' => 34.99, 'description' => 'Ergonoomiline juhtmevaba hiir 6 nupuga.', 'image' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&h=300&fit=crop'],
            ['id' => 4, 'name' => 'USB-C Hub', 'price' => 29.99, 'description' => '7-in-1 USB-C hub HDMI, USB 3.0 ja SD kaardilugejaga.', 'image' => 'https://images.unsplash.com/photo-1625895197185-efcec01cffe0?w=400&h=300&fit=crop'],
            ['id' => 5, 'name' => 'Veebikaamra 4K', 'price' => 79.99, 'description' => '4K webkaamra automaatse fookusega.', 'image' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=400&h=300&fit=crop'],
            ['id' => 6, 'name' => 'Monitor 27"', 'price' => 299.99, 'description' => '27" IPS monitor 144Hz ja 1ms reageerimisajaga.', 'image' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400&h=300&fit=crop'],
            ['id' => 7, 'name' => 'SSD 1TB', 'price' => 69.99, 'description' => 'NVMe SSD 1TB kuni 3500MB/s lugemiskiirusega.', 'image' => 'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=400&h=300&fit=crop'],
            ['id' => 8, 'name' => 'Laualamp LED', 'price' => 24.99, 'description' => 'Silmi säästev LED laualamp USB laadimisportiga.', 'image' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=400&h=300&fit=crop'],
            ['id' => 9, 'name' => 'Nutikell', 'price' => 149.99, 'description' => 'Nutikell terviseanduri ja GPS-iga, 7 päeva aku.', 'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=300&fit=crop'],
        ];
    }

    public function index()
    {
        return Inertia::render('Shop/Index', [
            'products'   => $this->getProducts(),
            'stripeKey'  => config('services.stripe.key'),
        ]);
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email',
            'phone'      => 'required|string|max:30',
            'cart'       => 'required|array|min:1',
            'cart.*.id'       => 'required|integer',
            'cart.*.name'     => 'required|string',
            'cart.*.price'    => 'required|numeric',
            'cart.*.quantity' => 'required|integer|min:1',
        ]);

        $total = collect($data['cart'])->sum(fn($i) => $i['price'] * $i['quantity']);

        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::create([
            'amount'   => (int) round($total * 100),
            'currency' => 'eur',
            'metadata' => ['email' => $data['email']],
        ]);

        $order = Order::create([
            'user_id'                => auth()->id(),
            'first_name'             => $data['first_name'],
            'last_name'              => $data['last_name'],
            'email'                  => $data['email'],
            'phone'                  => $data['phone'],
            'total'                  => $total,
            'status'                 => 'pending',
            'stripe_payment_intent'  => $intent->id,
        ]);

        foreach ($data['cart'] as $item) {
            $order->items()->create([
                'product_name' => $item['name'],
                'price'        => $item['price'],
                'quantity'     => $item['quantity'],
            ]);
        }

        return response()->json([
            'clientSecret' => $intent->client_secret,
            'orderId'      => $order->id,
        ]);
    }

    public function confirm(Request $request)
    {
        $data = $request->validate([
            'order_id'       => 'required|integer',
            'payment_intent' => 'required|string',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));
        $intent = PaymentIntent::retrieve($data['payment_intent']);

        $order = Order::findOrFail($data['order_id']);

        if ($intent->status === 'succeeded') {
            $order->update(['status' => 'paid']);
            return response()->json(['status' => 'paid']);
        }

        $order->update(['status' => 'failed']);
        return response()->json(['status' => 'failed'], 400);
    }
}