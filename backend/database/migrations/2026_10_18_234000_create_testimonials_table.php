<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role_or_company')->nullable();
            $table->string('avatar')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('content');
            $table->boolean('is_featured')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed initial high-quality realistic testimonials
        $now = now();
        DB::table('testimonials')->insert([
            [
                'name'            => 'Budi Santoso',
                'role_or_company' => 'Owner Toko Sepatu Bandung',
                'avatar'          => null,
                'rating'          => 5,
                'content'         => 'Sistem notifikasi pengiriman pesanan toko online saya jadi 100% otomatis. Sejak pakai JalurGaib, pesan OTP dan resi terkirim instan tanpa khawatir nomor diblokir.',
                'is_featured'     => true,
                'sort_order'      => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'name'            => 'Farhan Maulana',
                'role_or_company' => 'Tech Lead PT Solusi Digital',
                'avatar'          => null,
                'rating'          => 5,
                'content'         => 'Integrasi API-nya sangat mudah dan dokumentasinya jelas. Queue delay-nya benar-benar mirip sentuhan manusia, pesan notifikasi ke ribuan user lancar jaya.',
                'is_featured'     => true,
                'sort_order'      => 2,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'name'            => 'Dewi Lestari',
                'role_or_company' => 'Founder Dapur Berkah Surabaya',
                'avatar'          => null,
                'rating'          => 5,
                'content'         => 'Sangat membantu bisnis catering saya untuk kirim invoice dan pengingat pesanan setiap hari. Nomor aman dan pelanggan senang karena respon cepat.',
                'is_featured'     => true,
                'sort_order'      => 3,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'name'            => 'Reza Pratama',
                'role_or_company' => 'Fullstack Developer Freelance',
                'avatar'          => null,
                'rating'          => 5,
                'content'         => 'Gateway WhatsApp paling fleksibel untuk developer. Endpoint REST API bersih, respon cepat, dan harga paketnya sangat masuk akal dibandingkan vendor lain.',
                'is_featured'     => true,
                'sort_order'      => 4,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
