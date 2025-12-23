<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\Companies;
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('company_name');
            $table->string('license_number')->unique();
            $table->tinyInteger('create_step')->default(0);
            $table->enum('status', [
                Companies::COMPANY_STATUS_ACTIVE,
                Companies::COMPANY_STATUS_DELETED,
                Companies::COMPANY_STATUS_SUSPENDED
            ])->default(Companies::COMPANY_STATUS_ACTIVE);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
