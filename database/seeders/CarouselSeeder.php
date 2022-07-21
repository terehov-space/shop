<?php

namespace Database\Seeders;

use App\Models\Carousel;
use App\Models\Image;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CarouselSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $image1 = Image::updateOrCreate([
            'path' => '/uploads/images/carousel/s1.webp',
        ], [
            'title' => '',
        ]);

        $image2 = Image::updateOrCreate([
            'path' => '/uploads/images/carousel/s2.webp',
        ], [
            'title' => '',
        ]);

        $image3 = Image::updateOrCreate([
            'path' => '/uploads/images/carousel/s3.webp',
        ], [
            'title' => '',
        ]);

        $image4 = Image::updateOrCreate([
            'path' => '/uploads/images/carousel/s4.webp',
        ], [
            'title' => '',
        ]);

        $image5 = Image::updateOrCreate([
            'path' => '/uploads/images/carousel/s5.webp',
        ], [
            'title' => '',
        ]);

        $image6 = Image::updateOrCreate([
            'path' => '/uploads/images/carousel/s6.webp',
        ], [
            'title' => '',
        ]);

        $image7 = Image::updateOrCreate([
            'path' => '/uploads/images/carousel/s7.webp',
        ], [
            'title' => '',
        ]);

        $image8 = Image::updateOrCreate([
            'path' => '/uploads/images/carousel/s8.webp',
        ], [
            'title' => '',
        ]);

        $image9 = Image::updateOrCreate([
            'path' => '/uploads/images/carousel/s9.webp',
        ], [
            'title' => '',
        ]);

        $mobileImage1 = Image::updateOrCreate([
            'path' => '/uploads/images/carousel/s1_mobile.webp',
        ], [
            'title' => '',
        ]);

        $mobileImage2 = Image::updateOrCreate([
            'path' => '/uploads/images/carousel/s2_mobile.webp',
        ], [
            'title' => '',
        ]);

        $mobileImage3 = Image::updateOrCreate([
            'path' => '/uploads/images/carousel/s3_mobile.webp',
        ], [
            'title' => '',
        ]);

        $mobileImage4 = Image::updateOrCreate([
            'path' => '/uploads/images/carousel/s4_mobile.webp',
        ], [
            'title' => '',
        ]);

        $mobileImage5 = Image::updateOrCreate([
            'path' => '/uploads/images/carousel/s5_mobile.webp',
        ], [
            'title' => '',
        ]);

        $mobileImage6 = Image::updateOrCreate([
            'path' => '/uploads/images/carousel/s6_mobile.webp',
        ], [
            'title' => '',
        ]);

        $mobileImage7 = Image::updateOrCreate([
            'path' => '/uploads/images/carousel/s7_mobile.webp',
        ], [
            'title' => '',
        ]);

        $mobileImage8 = Image::updateOrCreate([
            'path' => '/uploads/images/carousel/s8_mobile.webp',
        ], [
            'title' => '',
        ]);

        $mobileImage9 = Image::updateOrCreate([
            'path' => '/uploads/images/carousel/s9_mobile.webp',
        ], [
            'title' => '',
        ]);

        Carousel::updateOrCreate([
            'title' => 'Барабаны(катушки) для шланга',
        ], [
            'imageId' => $image1->id,
            'mobileImage' => $mobileImage1->id,
        ]);

        Carousel::updateOrCreate([
            'title' => 'Вакуумные насосы',
        ], [
            'imageId' => $image2->id,
            'mobileImage' => $mobileImage2->id,
        ]);

        Carousel::updateOrCreate([
            'title' => 'Помпы и моторы',
        ], [
            'imageId' => $image3->id,
            'mobileImage' => $mobileImage3->id,
        ]);

        Carousel::updateOrCreate([
            'title' => 'Мембранно-поршневые насосы',
        ], [
            'imageId' => $image4->id,
            'mobileImage' => $mobileImage4->id,
        ]);

        Carousel::updateOrCreate([
            'title' => 'Инвентарь для пищевых производств',
        ], [
            'imageId' => $image5->id,
            'mobileImage' => $mobileImage5->id,
        ]);

        Carousel::updateOrCreate([
            'title' => 'Аксессуары высокого давления',
        ], [
            'imageId' => $image6->id,
            'mobileImage' => $mobileImage6->id,
        ]);

        Carousel::updateOrCreate([
            'title' => 'Аппараты высокого давления',
        ], [
            'imageId' => $image7->id,
            'mobileImage' => $mobileImage7->id,
        ]);

        Carousel::updateOrCreate([
            'title' => 'Насадки для мойки емкостей',
        ], [
            'imageId' => $image8->id,
            'mobileImage' => $mobileImage8->id,
        ]);

        Carousel::updateOrCreate([
            'title' => 'Каналопромывочные дюзы',
        ], [
            'imageId' => $image9->id,
            'mobileImage' => $mobileImage9->id,
        ]);
    }
}
