<?php

use App\Models\Response;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('response_channels')) {
            return;
        }

        Schema::create('response_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Response::class)->constrained()->cascadeOnDelete();
            $table->string('provider');
            $table->string('provider_message_id')->nullable();
            $table->json('payload_json')->nullable();
            $table->string('processed_status')->default('received');
            $table->timestamp('received_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index('provider');
            $table->index('processed_status');
            $table->index('response_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('response_channels');
    }
};
