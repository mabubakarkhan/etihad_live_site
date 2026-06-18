<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * @return array<string, string|null>
     */
    private function seedImages(): array
    {
        $map = [
            'image_left' => ['contemporary-left-BqpaZZO6.avif', 'left.avif'],
            'image_center' => ['contemporary-center-Cy1UF1UF.avif', 'center.avif'],
            'image_right' => ['contemporary-right-BGFk98DL.avif', 'right.avif'],
            'image_center_back' => ['contemporary-center-back-MRHJVZZb.avif', 'center-back.avif'],
        ];

        $paths = [];
        foreach ($map as $column => [$sourceName, $targetName]) {
            $source = public_path('homepage/assets/' . $sourceName);
            if (! is_file($source)) {
                $paths[$column] = null;

                continue;
            }

            $target = 'homepage-why/' . $targetName;
            Storage::disk('public')->put($target, file_get_contents($source));
            $paths[$column] = $target;
        }

        return $paths;
    }

    public function up(): void
    {
        Schema::create('homepage_why_settings', function (Blueprint $table) {
            $table->id();
            $table->string('heading_line_1')->nullable();
            $table->string('heading_line_2')->nullable();
            $table->text('description')->nullable();
            $table->string('scroll_label')->nullable();
            $table->string('image_left')->nullable();
            $table->string('image_center')->nullable();
            $table->string('image_right')->nullable();
            $table->string('image_center_back')->nullable();
            $table->timestamps();
        });

        $images = $this->seedImages();

        DB::table('homepage_why_settings')->insert([
            'heading_line_1' => 'WHY CHOOSE',
            'heading_line_2' => 'ETIHAD?',
            'description' => 'To achieve flawless interior design from planning to execution, you need a skilled real estate and property development consultant in Pakistan. Our experienced team delivers customized solutions, prioritizing client satisfaction and handling projects of all sizes with precision.',
            'scroll_label' => 'SCROLL',
            'image_left' => $images['image_left'],
            'image_center' => $images['image_center'],
            'image_right' => $images['image_right'],
            'image_center_back' => $images['image_center_back'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_why_settings');
    }
};
