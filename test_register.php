<?php

use App\Models\User;
use App\Http\Controllers\User\AuthController;
use Illuminate\Http\Request;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$controller = new AuthController();
$request = Request::create('/api/register', 'POST', [
    'username' => 'tester_' . uniqid(),
    'email' => 'tester_' . uniqid() . '@gmail.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'gender' => 'Male',
    'job_class' => 'Al-Hafizh',
]);

$response = $controller->register($request);
$data = json_decode($response->getContent(), true);

if ($data['success']) {
    $userId = $data['data']['user']['id'];
    $user = User::with('rankTier')->find($userId);
    echo "USER CREATED:\n";
    echo "ID: " . $user->id . "\n";
    echo "Level: " . $user->level . "\n";
    echo "Rank: " . ($user->rankTier ? $user->rankTier->name : 'N/A') . "\n";
} else {
    echo "REGISTRATION FAILED:\n";
    print_r($data);
}
