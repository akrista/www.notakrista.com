<?php

declare(strict_types=1);

namespace App\Console\Commands\Fillakit;

use Illuminate\Console\Command;
use Laravel\Prompts\Concerns\Colors;
use Override;

final class About extends Command
{
    use Colors;

    #[Override]
    protected $signature = 'fillakit:about';

    #[Override]
    protected $description = 'Display information about Fillakit.';

    public function handle(): void
    {
        $banner = <<<'EOT'
███████╗██╗██╗     ██╗      █████╗ ██╗  ██╗██╗████████╗
██╔════╝██║██║     ██║     ██╔══██╗██║ ██╔╝██║╚══██╔══╝
█████╗  ██║██║     ██║     ███████║█████╔╝ ██║   ██║
██╔══╝  ██║██║     ██║     ██╔══██║██╔═██╗ ██║   ██║
██║     ██║███████╗███████╗██║  ██║██║  ██╗██║   ██║
╚═╝     ╚═╝╚══════╝╚══════╝╚═╝  ╚═╝╚═╝  ╚═╝╚═╝   ╚═╝
EOT;

        $banner = preg_replace_callback('/█/u', fn(array $matches): string => $this->red($matches[0]), $banner);
        $banner = preg_replace_callback('/[╔╗╚╝║═]/u', fn(array $matches): string => $this->dim($this->red($matches[0])), (string) $banner);

        echo $banner . PHP_EOL;

        $message = <<<'EOT'
Fillakit is a Laravel starter kit that includes Filament as an admin panel.

After installing, you can run all the commands needed for your application with a single command:

> composer run dev

Fillakit was developed by Jorge Thomas (akrista). If you like it, please let me know!

• Twitter: https://twitter.com/notakrista
• Website: https://notakrista.com
• GitHub: https://github.com/akrista/fillakit
EOT;

        echo wordwrap($message);
    }
}
