<?php

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
        Schema::table('complaints', function (Blueprint $table) {
            $table->text('appeal_reason')->nullable()->after('resolution_note');
            $table->string('appeal_evidence_url')->nullable()->after('appeal_reason');
            $table->timestamp('appealed_at')->nullable()->after('appeal_evidence_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn(['appeal_reason', 'appeal_evidence_url', 'appealed_at']);
        });
    }
};
