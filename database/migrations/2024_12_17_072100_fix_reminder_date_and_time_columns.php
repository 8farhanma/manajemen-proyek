<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create new table with correct column types
        Schema::create('reminders_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('date');
            $table->time('time');
            $table->timestamps();
        });

        // Copy data from old table to new table
        $reminders = DB::table('reminders')->get();
        foreach ($reminders as $reminder) {
            DB::table('reminders_new')->insert([
                'id' => $reminder->id,
                'user_id' => $reminder->user_id,
                'title' => $reminder->title,
                'description' => $reminder->description,
                'date' => $reminder->date,
                'time' => $reminder->time,
                'created_at' => $reminder->created_at,
                'updated_at' => $reminder->updated_at,
            ]);
        }

        // Drop old table and rename new table
        Schema::dropIfExists('reminders');
        Schema::rename('reminders_new', 'reminders');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Create new table with string columns
        Schema::create('reminders_old', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('date');
            $table->string('time');
            $table->timestamps();
        });

        // Copy data back
        $reminders = DB::table('reminders')->get();
        foreach ($reminders as $reminder) {
            DB::table('reminders_old')->insert([
                'id' => $reminder->id,
                'user_id' => $reminder->user_id,
                'title' => $reminder->title,
                'description' => $reminder->description,
                'date' => $reminder->date,
                'time' => $reminder->time,
                'created_at' => $reminder->created_at,
                'updated_at' => $reminder->updated_at,
            ]);
        }

        // Drop new table and rename old table
        Schema::dropIfExists('reminders');
        Schema::rename('reminders_old', 'reminders');
    }
};
