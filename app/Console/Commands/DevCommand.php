<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Command;
use Illuminate\Console\Prohibitable;
use Illuminate\Foundation\DevCommands;
use Illuminate\Support\NodePackageManager;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Terminal as ConsoleTerminal;

#[AsCommand(name: 'dev')]
#[Description('Run the dev processes')]
final class DevCommand extends Command
{
    use Prohibitable;

    #[Override]
    protected $name = 'dev';

    public function handle(NodePackageManager $packageManager): int
    {
        if ($this->isProhibited()) {
            return self::FAILURE;
        }

        $devCommands = DevCommands::commands();

        $commands = array_column($devCommands, 'command');
        $colors = array_column($devCommands, 'color');
        $names = array_column($devCommands, 'name');

        $longestName = $names === [] ? 0 : max(array_map(strlen(...), $names));

        $columns = getenv('COLUMNS');

        $terminalWidth = new ConsoleTerminal()->getWidth();
        putenv('COLUMNS=' . max($terminalWidth - $longestName - 4, 1));

        $this->line('');

        foreach ($devCommands as $devCommand) {
            $this->line(
                sprintf(
                    '<fg=%s>[%s]</>%s%s',
                    $devCommand['color'],
                    $devCommand['name'],
                    str_repeat(' ', ($longestName - mb_strlen($devCommand['name'])) + 1),
                    $devCommand['command'],
                ),
            );
        }

        $this->line('');

        $command = $packageManager->getExecCommand(sprintf(
            'concurrently -c "%s" "%s" --names=%s --kill-others',
            implode(',', $colors),
            implode('" "', $commands),
            implode(',', $names)
        ));

        if (extension_loaded('pcntl')) {
            pcntl_exec('/usr/bin/env', ['sh', '-c', $command]);
        }

        passthru($command, $exitCode);

        $columns === false ? putenv('COLUMNS') : putenv('COLUMNS=' . $columns);

        return $exitCode;
    }
}
