<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'bizkit:generate-policies')]
#[Description('Generate policies for all models in the application mapping them to Spatie permissions.')]
#[Signature('bizkit:generate-policies {--force : Overwrite existing policies}')]
final class GeneratePoliciesCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $modelsPath = app_path('Models');
        if (! File::isDirectory($modelsPath)) {
            $this->error('Models directory not found at ' . $modelsPath);

            return self::FAILURE;
        }

        $files = File::files($modelsPath);
        $stubPath = base_path('stubs/policy.stub');
        if (! File::exists($stubPath)) {
            $this->error('Policy stub not found at ' . $stubPath);

            return self::FAILURE;
        }

        $stub = File::get($stubPath);
        $policiesPath = app_path('Policies');
        if (! File::isDirectory($policiesPath)) {
            File::makeDirectory($policiesPath, 0755, true);
        }

        $generatedCount = 0;
        $skippedCount = 0;

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $model = $file->getBasename('.php');
            $policyName = $model . 'Policy';
            $policyFilePath = $policiesPath . '/' . $policyName . '.php';

            if (File::exists($policyFilePath) && ! $this->option('force')) {
                $this->info(sprintf('Policy for %s already exists. Skipped.', $model));
                $skippedCount++;

                continue;
            }

            $modelClass = 'App\\Models\\' . $model;
            $modelVariable = Str::snake($model);

            $content = str_replace(
                ['{{ modelClass }}', '{{ model }}', '{{ modelVariable }}'],
                [$modelClass, $model, $modelVariable],
                $stub
            );

            File::put($policyFilePath, $content);
            $this->info(sprintf('Generated policy for %s at app/Policies/%s.php', $model, $policyName));
            $generatedCount++;
        }

        $this->info(sprintf('Done! Generated: %d, Skipped: %d', $generatedCount, $skippedCount));

        return self::SUCCESS;
    }
}
