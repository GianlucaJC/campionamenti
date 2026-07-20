<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MicrobiologicalCheck;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();
    echo "Transaction started.\n";

    $check = MicrobiologicalCheck::create([
        "monitoring_section_id" => 2,
        "sampled_on" => "2026-07-15",
        "created_by_user_id" => 2,
    ]);

    echo "Insert successful. ID: " . $check->id . "\n";
    
    // Check if it exists within the transaction
    $insertedCheck = MicrobiologicalCheck::find($check->id);
    echo "Found inside transaction: " . ($insertedCheck ? "YES" : "NO") . "\n";

    DB::rollBack();
    echo "Transaction rolled back.\n";

    // Verify it is not in database after rollback
    $rolledBackCheck = MicrobiologicalCheck::find($check->id);
    echo "Found after rollback: " . ($rolledBackCheck ? "YES" : "NO") . "\n";

} catch (\Throwable $e) {
    DB::rollBack();
    echo "Error occurred: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}

