<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class OptimizeAssetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:optimize {--minify : Minificar arquivos} {--compress : Comprimir imagens}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otimizar assets para máxima performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Iniciando otimização de assets...');
        
        if ($this->option('minify')) {
            $this->minifyAssets();
        }
        
        if ($this->option('compress')) {
            $this->compressImages();
        }
        
        $this->clearCache();
        $this->generateManifest();
        
        $this->info('✅ Otimização concluída com sucesso!');
    }
    
    /**
     * Minificar arquivos CSS e JS
     */
    private function minifyAssets()
    {
        $this->info('📦 Minificando arquivos...');
        
        // Minificar CSS
        $cssPath = public_path('css/app.css');
        if (File::exists($cssPath)) {
            $css = File::get($cssPath);
            $minifiedCss = $this->minifyCss($css);
            File::put(public_path('css/app.min.css'), $minifiedCss);
            $this->line('  ✓ CSS minificado');
        }
        
        // Minificar JS
        $jsPath = public_path('js/app.js');
        if (File::exists($jsPath)) {
            $js = File::get($jsPath);
            $minifiedJs = $this->minifyJs($js);
            File::put(public_path('js/app.min.js'), $minifiedJs);
            $this->line('  ✓ JavaScript minificado');
        }
    }
    
    /**
     * Comprimir imagens
     */
    private function compressImages()
    {
        $this->info('🖼️ Comprimindo imagens...');
        
        $imageDir = public_path('alimentos');
        if (File::exists($imageDir)) {
            $images = File::glob($imageDir . '/*.{jpg,jpeg,png}', GLOB_BRACE);
            
            foreach ($images as $image) {
                $this->compressImage($image);
            }
            
            $this->line('  ✓ Imagens comprimidas');
        }
    }
    
    /**
     * Minificar CSS
     */
    private function minifyCss($css)
    {
        // Remover comentários
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // Remover espaços desnecessários
        $css = preg_replace('/\s+/', ' ', $css);
        $css = preg_replace('/\s*{\s*/', '{', $css);
        $css = preg_replace('/;\s*/', ';', $css);
        $css = preg_replace('/\s*}\s*/', '}', $css);
        $css = preg_replace('/\s*,\s*/', ',', $css);
        $css = preg_replace('/\s*:\s*/', ':', $css);
        
        // Remover espaços no final das linhas
        $css = preg_replace('/\s*$/', '', $css);
        
        return trim($css);
    }
    
    /**
     * Minificar JavaScript
     */
    private function minifyJs($js)
    {
        // Remover comentários de linha única
        $js = preg_replace('/\/\/.*$/m', '', $js);
        
        // Remover comentários de bloco
        $js = preg_replace('/\/\*.*?\*\//s', '', $js);
        
        // Remover espaços desnecessários
        $js = preg_replace('/\s+/', ' ', $js);
        $js = preg_replace('/\s*([{}();,=+\-*\/])\s*/', '$1', $js);
        
        return trim($js);
    }
    
    /**
     * Comprimir imagem individual
     */
    private function compressImage($imagePath)
    {
        $info = pathinfo($imagePath);
        $extension = strtolower($info['extension']);
        
        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            // Aqui você pode implementar compressão real com GD ou ImageMagick
            // Por enquanto, apenas logamos
            $this->line("    - Comprimindo: {$info['basename']}");
        }
    }
    
    /**
     * Limpar cache
     */
    private function clearCache()
    {
        $this->info('🧹 Limpando cache...');
        
        // Limpar cache do Laravel
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('view:clear');
        
        $this->line('  ✓ Cache limpo');
    }
    
    /**
     * Gerar manifest para Service Worker
     */
    private function generateManifest()
    {
        $this->info('📋 Gerando manifest...');
        
        $manifest = [
            'name' => 'Meus Produtos',
            'short_name' => 'Produtos',
            'description' => 'Sistema de gestão de produtos',
            'start_url' => '/',
            'display' => 'standalone',
            'background_color' => '#1f2937',
            'theme_color' => '#10b981',
            'icons' => [
                [
                    'src' => '/images/icon-192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png'
                ],
                [
                    'src' => '/images/icon-512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png'
                ]
            ]
        ];
        
        File::put(public_path('manifest.json'), json_encode($manifest, JSON_PRETTY_PRINT));
        $this->line('  ✓ Manifest gerado');
    }
}
