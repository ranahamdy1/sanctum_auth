<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('otp');
            $table->timestamp('expires_at');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
