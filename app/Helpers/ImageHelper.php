<?php

namespace App\Helpers;

class ImageHelper
{
    /**
     * Obter URL da imagem do produto
     */
    public static function getProductImageUrl(?string $imagePath, ?string $imageUrl): string
    {
        // Priorizar imagem enviada por upload
        if ($imagePath) {
            $storagePath = public_path('storage/' . $imagePath);
            if (file_exists($storagePath)) {
                return asset('storage/' . $imagePath);
            }
        }
        
        // Se não há upload, usar URL se existir
        if ($imageUrl) {
            // Se a imagem começa com /, é um caminho relativo
            if (str_starts_with($imageUrl, '/')) {
                $decodedImage = urldecode($imageUrl);
                $imagePath = public_path($decodedImage);
                
                // Verificar se o arquivo existe
                if (file_exists($imagePath)) {
                    return asset($imageUrl);
                }
                
                // Tentar outras extensões comuns
                $extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                $imageDir = dirname($imagePath);
                $baseFileName = pathinfo($imagePath, PATHINFO_FILENAME);
                $originalExt = pathinfo($decodedImage, PATHINFO_EXTENSION);
                
                foreach ($extensions as $ext) {
                    $testPath = $imageDir . DIRECTORY_SEPARATOR . $baseFileName . '.' . $ext;
                    if (file_exists($testPath)) {
                        $relativePath = str_replace(public_path(), '', $testPath);
                        $relativePath = str_replace('\\', '/', $relativePath);
                        return asset($relativePath);
                    }
                }
            }
            
            // Se é uma URL completa, retornar como está
            if (str_starts_with($imageUrl, 'http://') || str_starts_with($imageUrl, 'https://')) {
                return $imageUrl;
            }
        }
        
        return asset('images/no-image.png');
    }
}

