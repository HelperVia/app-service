<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Domain\Agent\Constants\Agent;
return new class extends Migration {


    public function up(): void
    {

        Schema::create("company_user", function (Blueprint $table): void {

            $table->id();
            $table->uuid('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('role', [Agent::AGENT_ROLE_AGENT, Agent::AGENT_ROLE_OWNER, Agent::AGENT_ROLE_SUPERADMIN, Agent::AGENT_ROLE_UNKNOW])->default(Agent::AGENT_ROLE_AGENT);
            $table->timestamps();

            $table->unique(['company_id', 'user_id']);

        });

    }

    public function down(): void
    {

        Schema::dropIfExists('company_user');
    }

};