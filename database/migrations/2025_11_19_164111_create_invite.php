<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\Invite;
use App\Constants\Agent;
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $statusLabels = array_map(
            fn($key, $label) => "$key=$label",
            array_keys(Invite::STATUS_LABELS),
            Invite::STATUS_LABELS
        );

        $statusComment = 'Invite status: ' . implode(', ', $statusLabels);

        Schema::create('invite', function (Blueprint $table) use ($statusComment) {

            $table->id();
            $table->uuid('inviting_company_id');
            $table->foreign('inviting_company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->string('invited_email');
            $table->uuid('invited_id');
            $table->uuid('inviting_user');
            $table->foreign('inviting_user')->references('id')->on('users')->onDelete('cascade');
            $table->enum('status', [
                Invite::INVITE_PENDING,
                Invite::INVITE_COMPLETE,
                Invite::INVITE_DECLINE,
                Invite::INVITE_EXPIRED
            ])->default(Invite::INVITE_PENDING)
                ->comment($statusComment);
            $table->unsignedBigInteger('invite_expire');
            $table->enum('invited_role', [
                Agent::AGENT_ROLE_AGENT,
                Agent::AGENT_ROLE_SUPERADMIN,
            ])->default(Agent::AGENT_ROLE_AGENT);
            $table->string('temporary_name');
            $table->string('invite_code');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invite');
    }
};
