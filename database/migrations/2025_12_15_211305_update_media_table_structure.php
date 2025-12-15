<?php
// database/migrations/xxxx_update_media_table_structure.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('media', function (Blueprint $table) {
            // Pastikan kolom berikut ada:
            // id, ref_table, ref_id, file_name, mime_type, created_at, updated_at
            // file_name harus varchar yang cukup panjang (255)
        });
    }
};