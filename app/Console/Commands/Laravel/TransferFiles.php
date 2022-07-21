<?php

namespace App\Console\Commands\Laravel;

use App\Models\File;
use App\Models\FileToProduct;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TransferFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel:files';

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
        $ftps = DB::table('old_base.file_to_products')
            ->get();

        $this->output->progressStart($ftps->count());

        foreach ($ftps as $ftp) {
            $file = DB::table('old_base.files')
                ->where('id', '=', $ftp->file_id)
                ->first();

            $product = DB::table('old_base.products')
                ->where('id', '=', $ftp->product_id)
                ->first();

            if ($product->vendor_code) {
                $vendorCode = trim($product->vendor_code);
                $dbProduct = Product::where('vendorCode', '=', $vendorCode)->first();

                if ($dbProduct) {
                    $dbFile = File::updateOrCreate([
                        'path' => str_replace('/uploads/', '/uploads/files/', $file->path),
                    ], [
                        'title' => $file->title,
                    ]);

                    if ($dbFile) {
                        FileToProduct::updateOrCreate([
                            'fileId' => $dbFile->id,
                            'productId' => $dbProduct->id,
                        ], []);
                    }
                }
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        return 0;
    }
}
