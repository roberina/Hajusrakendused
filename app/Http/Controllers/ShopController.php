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
            ['id' => 1, 'name' => 'Skandinaavia Vaas', 'price' => 34.99, 'description' => 'Käsitsi valmistatud keraamiline vaas minimalistliku skandinaavia disainiga. Sobib nii kuivate kui värsketele lilledele.', 'image' => 'https://images.unsplash.com/photo-1578500351865-d6c3706f46bc?w=400&h=300&fit=crop'],
            ['id' => 2, 'name' => 'Puidust Fotoraam', 'price' => 24.99, 'description' => 'Käsitsi viimistletud tammepuidust fotoraam. Elegantne ja vastupidav, sobib igasse interjööri.', 'image' => 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?w=400&h=300&fit=crop'],
            ['id' => 3, 'name' => 'Bambusest Riiulid', 'price' => 89.99, 'description' => 'Kolmeastmeline bambusest seinariiul. Keskkonnasõbralik ja vastupidav, ideaalne taimede ja raamatute jaoks.', 'image' => 'https://images.unsplash.com/photo-1594026112284-02bb6f3352fe?w=400&h=300&fit=crop'],
            ['id' => 4, 'name' => 'Marmor Küünlaalus', 'price' => 19.99, 'description' => 'Elegantne looduslikust marmorist küünlaalus. Lisab igale ruumile luksuslikku atmosfääri.', 'image' => 'https://images.unsplash.com/photo-1567225557594-88d73e55f2cb?w=400&h=300&fit=crop'],
            ['id' => 5, 'name' => 'Villane Pleed', 'price' => 69.99, 'description' => 'Pehme meriinovillast pleed. Soe ja hubane, ideaalne diivanil lösutamiseks külmadel õhtutel.', 'image' => 'https://images.unsplash.com/photo-1600369671236-e74521d4b6ad?w=400&h=300&fit=crop'],
            ['id' => 6, 'name' => 'Ronitaim Ripppotis', 'price' => 29.99, 'description' => 'Käsitsi kootud makramee ripppott koos ronitaimega. Toob looduse tunde tuppa.', 'image' => 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?w=400&h=300&fit=crop'],
            ['id' => 7, 'name' => 'Puidust Serveerimislaud', 'price' => 49.99, 'description' => 'Käsitsi viimistletud tammepuidust serveerimislaud koos käepidemetega. Sobib nii kööki kui elutuppa.', 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&h=300&fit=crop'],
            ['id' => 8, 'name' => 'Lauavalgusti', 'price' => 79.99, 'description' => 'Elegantne metallist lauavalgusti sooja valgusega. Sobib nii magamistuppa kui töölaua kõrvale.', 'image' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=400&h=300&fit=crop'],
            ['id' => 9, 'name' => 'Looduslik Aroomidifuuser', 'price' => 44.99, 'description' => 'Bambus ja klaasist aroomidifuuser koos 5 eeterlike õlidega. Loob koduse ja rahustava atmosfääri.', 'image' => 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?w=400&h=300&fit=crop'],
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