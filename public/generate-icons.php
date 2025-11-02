<?php
/**
 * Gerador de Ícones PWA
 * 
 * NOTA: Este script requer a extensão GD do PHP habilitada.
 * Se não tiver GD, use o gerador HTML: public/images/icon-generator.html
 * 
 * Para habilitar GD no PHP:
 * - Windows: Descomente extension=gd no php.ini
 * - Linux: sudo apt-get install php-gd
 */

// Verificar se GD está disponível
if (!extension_loaded('gd')) {
    die("❌ Erro: Extensão GD não está habilitada no PHP.\n\n" .
        "Por favor, use o gerador HTML alternativo:\n" .
        "1. Abra: public/images/icon-generator.html no navegador\n" .
        "2. Clique em 'Gerar Todos os Ícones'\n" .
        "3. Os ícones serão baixados automaticamente\n" .
        "4. Coloque os arquivos na pasta public/images/\n\n" .
        "Ou habilite a extensão GD no PHP.\n");
}

// Tamanhos dos ícones
$sizes = [72, 96, 128, 144, 152, 192, 384, 512];
$outputDir = __DIR__ . '/images/';

// Criar diretório se não existir
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

function generateIcon($size) {
    // Criar imagem
    $img = imagecreatetruecolor($size, $size);
    
    // Cores
    $bgDark1 = imagecolorallocate($img, 31, 41, 55);  // #1f2937
    $bgDark2 = imagecolorallocate($img, 55, 65, 81);  // #374151
    $primary = imagecolorallocate($img, 16, 185, 129); // #10b981
    $primaryDark = imagecolorallocate($img, 5, 150, 105); // #059669
    $primaryLight = imagecolorallocate($img, 52, 211, 153); // #34d399
    $primaryDarker = imagecolorallocate($img, 4, 120, 87); // #047857
    $white = imagecolorallocate($img, 255, 255, 255);
    $shadow = imagecolorallocatealpha($img, 0, 0, 0, 76); // rgba(0,0,0,0.3)
    
    // Fundo gradiente (simulado)
    for ($y = 0; $y < $size; $y++) {
        $ratio = $y / $size;
        $r = (int)(31 + ($ratio * (55 - 31)));
        $g = (int)(41 + ($ratio * (65 - 41)));
        $b = (int)(55 + ($ratio * (81 - 55)));
        $color = imagecolorallocate($img, $r, $g, $b);
        imageline($img, 0, $y, $size, $y, $color);
    }
    
    // Tamanhos para o ícone
    $centerX = $size / 2;
    $centerY = $size / 2;
    $iconSize = $size * 0.5;
    $boxSize = $iconSize * 0.6;
    $boxX = $centerX - $boxSize / 2;
    $boxY = $centerY - $boxSize / 2;
    $offset = $boxSize * 0.15;
    
    // Sombra
    imagefilledrectangle($img, 
        (int)($boxX + $offset), 
        (int)($boxY + $offset), 
        (int)($boxX + $offset + $boxSize), 
        (int)($boxY + $offset + $boxSize), 
        $shadow
    );
    
    // Face frontal (caixa)
    imagefilledrectangle($img, 
        (int)$boxX, 
        (int)$boxY, 
        (int)($boxX + $boxSize), 
        (int)($boxY + $boxSize), 
        $primary
    );
    
    // Borda da face frontal
    imagerectangle($img, 
        (int)$boxX, 
        (int)$boxY, 
        (int)($boxX + $boxSize), 
        (int)($boxY + $boxSize), 
        $primaryDark
    );
    
    // Face superior
    $points = [
        (int)$boxX, (int)$boxY,
        (int)($boxX + $offset), (int)($boxY - $offset),
        (int)($boxX + $offset + $boxSize), (int)($boxY - $offset),
        (int)($boxX + $boxSize), (int)$boxY
    ];
    imagefilledpolygon($img, $points, 4, $primaryLight);
    imagepolygon($img, $points, 4, $primaryDark);
    
    // Face lateral
    $points = [
        (int)($boxX + $boxSize), (int)$boxY,
        (int)($boxX + $boxSize + $offset), (int)($boxY - $offset),
        (int)($boxX + $boxSize + $offset), (int)($boxY - $offset + $boxSize),
        (int)($boxX + $boxSize), (int)($boxY + $boxSize)
    ];
    imagefilledpolygon($img, $points, 4, $primaryDark);
    imagepolygon($img, $points, 4, $primaryDarker);
    
    // Linhas divisórias
    imageline($img, 
        (int)($boxX + $boxSize * 0.5), 
        (int)$boxY, 
        (int)($boxX + $boxSize * 0.5 + $offset), 
        (int)($boxY - $offset), 
        $primaryDarker
    );
    
    imageline($img, 
        (int)($boxX + $boxSize * 0.5), 
        (int)($boxY + $boxSize), 
        (int)($boxX + $boxSize * 0.5 + $offset), 
        (int)($boxY - $offset + $boxSize), 
        $primaryDarker
    );
    
    // Linha vertical divisória
    imageline($img, 
        (int)($boxX + $boxSize * 0.5), 
        (int)$boxY, 
        (int)($boxX + $boxSize * 0.5), 
        (int)($boxY + $boxSize), 
        $primaryDarker
    );
    
    return $img;
}

// Gerar todos os ícones
echo "Gerando ícones PWA...\n";
$generated = [];

foreach ($sizes as $size) {
    echo "Gerando ícone {$size}x{$size}...\n";
    $img = generateIcon($size);
    $filename = $outputDir . "icon-{$size}x{$size}.png";
    imagepng($img, $filename);
    imagedestroy($img);
    $generated[] = "icon-{$size}x{$size}.png";
}

// Gerar favicon também (32x32)
echo "Gerando favicon...\n";
$favicon = generateIcon(32);
imagepng($favicon, $outputDir . 'favicon.png');
imagedestroy($favicon);
$generated[] = 'favicon.png';

echo "\n✅ Ícones gerados com sucesso!\n";
echo "Arquivos criados:\n";
foreach ($generated as $file) {
    echo "  - images/{$file}\n";
}

