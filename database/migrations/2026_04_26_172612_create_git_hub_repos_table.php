<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('git_hub_repos', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('full_name')->unique();
            $table->text('description')->nullable();
            $table->string('html_url');
            $table->string('language')->nullable();
            $table->unsignedInteger('stargazers_count')->default(0);
            $table->unsignedInteger('forks_count')->default(0);
            $table->unsignedInteger('open_issues_count')->default(0);
            $table->string('visibility')->default('public');
            $table->dateTime('last_push_at')->nullable();
            $table->dateTime('synced_at')->nullable();
            $table->timestamps();

            $table->index('language');
            $table->index('stargazers_count');
            $table->index('last_push_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('git_hub_repos');
    }
};
