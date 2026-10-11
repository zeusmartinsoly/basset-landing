<?php

namespace App\Console\Commands;

use App\Models\SeoSetting;
use Illuminate\Console\Command;

class GenerateRobotsTxt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:robots-txt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Write public/robots.txt from the robots.txt settings';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        SeoSetting::writeRobotsTxtFile();

        $this->components->info('public/robots.txt written.');

        return self::SUCCESS;
    }
}
