<?php
require __DIR__.'/autoload_app.php';

use App\Models\Payment;

$payments = Payment::latest()->take(10)->get(['id', 'payment_method', 'status', 'amount', 'external_id']);
file_put_contents(__DIR__.'/debug_payments_out.json', json_encode($payments->toArray(), JSON_PRETTY_PRINT));
echo "Written to debug_payments_out.json\n";
