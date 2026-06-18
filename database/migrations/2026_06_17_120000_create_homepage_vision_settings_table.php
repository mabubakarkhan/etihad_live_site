<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_vision_settings', function (Blueprint $table) {
            $table->id();
            $table->string('tagline')->nullable();
            $table->string('heading_line_1')->nullable();
            $table->string('heading_line_2')->nullable();
            $table->string('ceo_image')->nullable();
            $table->text('message_paragraph_1')->nullable();
            $table->text('message_paragraph_2_highlight')->nullable();
            $table->text('message_paragraph_2_body')->nullable();
            $table->string('ceo_name')->nullable();
            $table->string('ceo_title')->nullable();
            $table->timestamps();
        });

        $ceoImagePath = null;
        $sourceImage = public_path('homepage/assets/ceo-zeeshan-butt.png');
        if (is_file($sourceImage)) {
            $target = 'homepage-vision/ceo-zeeshan-butt.png';
            Storage::disk('public')->put($target, file_get_contents($sourceImage));
            $ceoImagePath = $target;
        }

        DB::table('homepage_vision_settings')->insert([
            'tagline' => 'MESSAGE FROM OUR CEO',
            'heading_line_1' => 'A VISION FOR',
            'heading_line_2' => 'EXCELLENCE',
            'ceo_image' => $ceoImagePath,
            'message_paragraph_1' => '"Over the years, Etihad Marketing has built a reputation for being a leading real estate firm and I take great pride in the long-term relationships we have forged, highlighting the strengths within our core values, and culture of the business."',
            'message_paragraph_2_highlight' => '"Our team of dedicated professionals',
            'message_paragraph_2_body' => 'brings passion and precision to every detail, ensuring that your project not only meets but exceeds expectations. Thank you for trusting us with your vision."',
            'ceo_name' => 'Zeeshan Ahsan Butt',
            'ceo_title' => 'CEO & Co-Founder',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_vision_settings');
    }
};
