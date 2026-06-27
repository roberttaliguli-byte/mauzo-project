<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Models\Bidhaa;

return new class extends Migration
{
    public function up()
    {
        // Add new columns for file storage
        Schema::table('bidhaas', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('image');
            $table->string('image_mime_type')->nullable()->after('image_path');
            $table->unsignedBigInteger('image_size')->nullable()->after('image_mime_type');
        });

        // Migrate existing images from BLOB to filesystem
        $this->migrateExistingImages();
    }

    public function down()
    {
        Schema::table('bidhaas', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'image_mime_type', 'image_size']);
        });
    }

    private function migrateExistingImages()
    {
        $products = Bidhaa::whereNotNull('image')->get();
        
        foreach ($products as $product) {
            try {
                // Detect mime type
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_buffer($finfo, $product->image);
                finfo_close($finfo);
                
                // Get file extension
                $extension = $this->getExtensionFromMimeType($mimeType);
                
                // Generate unique filename
                $filename = 'products/' . $product->id . '_' . time() . '_' . uniqid() . '.' . $extension;
                
                // Store file
                Storage::disk('public')->put($filename, $product->image);
                
                // Update product
                $product->update([
                    'image_path' => $filename,
                    'image_mime_type' => $mimeType,
                    'image_size' => strlen($product->image)
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Failed to migrate image for product ' . $product->id . ': ' . $e->getMessage());
            }
        }
    }

    private function getExtensionFromMimeType($mimeType)
    {
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/bmp' => 'bmp',
            'image/svg+xml' => 'svg',
        ];
        
        return $extensions[$mimeType] ?? 'jpg';
    }
};