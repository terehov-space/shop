<?php

namespace App\Console\Commands\Laravel;

use App\Models\Digital;
use App\Models\File;
use App\Models\Image;
use App\Models\Page;
use App\Models\Vendor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransferContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel:content';

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
        $this->info('vendors');
        $this->transferVendors();

        $this->info('pages');
        $this->transferPages();

        $this->info('digitals');
        $this->transferDigitals();

        return 0;
    }

    private function transferVendors()
    {
        $vendors = DB::table('old_base.vendors')
            ->get();

        $this->output->progressStart($vendors->count());

        foreach ($vendors as $vendor) {
            if ($vendor->image) {
                $dbImage = Image::updateOrCreate([
                    'path' => '/upload' . $vendor->image,
                ], [
                    'title' => '',
                ]);
            }

            Vendor::updateOrCreate([
                'extId' => $vendor->id,
                'title' => $vendor->title,
                'code' => Str::slug($vendor->title),
            ], [
                'imageId' => $dbImage->id,
            ]);


            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    private function transferDigitals()
    {
        $digitals = DB::table('old_base.digitals')
            ->get();

        $this->output->progressStart($digitals->count());

        foreach ($digitals as $key => $digital) {
            $dbImage = Image::updateOrCreate([
               'path' => '/uploads' . $digital->image,
            ], [
                'title' => '',
            ]);

            $dbFile = File::updateOrCreate([
               'path' => '/uploads' . $digital->file,
            ], [
                'title' => '',
            ]);

            $dbVendor = null;

            if ($digital->vendor_id) {
                $dbVendor = Vendor::where('extId', '=', $digital->vendor_id)->first();
            }

            Digital::updateOrCreate([
                'title' => 'Каталог ' . $key + 1,
            ], [
                'imageId' => $dbImage->id,
                'fileId' => $dbFile->id,
                'vendorId' => $dbVendor ? $dbVendor->id : null,
            ]);

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    private function transferPages()
    {
        $pages = DB::table('old_base.pages')
            ->get();

        $this->output->progressStart($pages->count());

        foreach ($pages as $page) {
            Page::updateOrCreate([
                'title' => $page->title,
                'code' => $page->code,
            ], [
                'body' => $page->content,
                'seoTitlePostfix' => $page->seo_title,
                'seoDescription' => $page->seo_description,
            ]);


            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
