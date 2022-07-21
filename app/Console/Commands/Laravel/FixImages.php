<?php

namespace App\Console\Commands\Laravel;

use App\Models\Image;
use Illuminate\Console\Command;

class FixImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel:fixImages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $images = Image::get();

        $this->output->progressStart($images->count());

        foreach ($images as $image) {
            if (!file_exists(storage_path("app/public{$image->path}"))) {
                $image->delete();
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        return 0;
    }
}
