<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('title')->nullable(); // Nama/label aset, misal: Final Makalah Bab 1-5
            $table->string('file_path')->nullable(); // Path di storage jika diupload
            $table->string('file_type')->nullable(); // png, jpg, pdf, doc, docx, ppt, pptx
            $table->unsignedBigInteger('file_size')->nullable(); // bytes
            $table->text('drive_link')->nullable(); // Teks link Google Drive / cloud storage
            $table->text('notes')->nullable(); // Catatan tambahan / instruksi dari admin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_assets');
    }
};
