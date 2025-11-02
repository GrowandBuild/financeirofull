/**
 * Gerador de Ícones PWA usando Node.js
 * 
 * Requisitos:
 *   npm install canvas
 * 
 * Execute:
 *   node public/generate-icons-node.js
 */

const fs = require('fs');
const path = require('path');

// Verificar se canvas está disponível
let Canvas;
try {
    Canvas = require('canvas');
} catch (e) {
    console.error('❌ Erro: Biblioteca "canvas" não instalada.');
    console.error('\nPara instalar:');
    console.error('  npm install canvas');
    console.error('\nOu use o gerador HTML: public/images/icon-generator.html');
    process.exit(1);
}

const sizes = [32, 72, 96, 128, 144, 152, 192, 384, 512];
const outputDir = path.join(__dirname, 'images');

// Criar diretório se não existir
if (!fs.existsSync(outputDir)) {
    fs.mkdirSync(outputDir, { recursive: true });
}

function generateIcon(size) {
    // Criar canvas
    const canvas = Canvas.createCanvas(size, size);
    const ctx = canvas.getContext('2d');
    
    // Cores
    const bgDark1 = { r: 31, g: 41, b: 55 };  // #1f2937
    const bgDark2 = { r: 55, g: 65, b: 81 };  // #374151
    const primary = '#10b981';
    const primaryDark = '#059669';
    const primaryLight = '#34d399';
    const primaryDarker = '#047857';
    
    // Fundo gradiente
    for (let y = 0; y < size; y++) {
        const ratio = y / size;
        const r = Math.round(bgDark1.r + ratio * (bgDark2.r - bgDark1.r));
        const g = Math.round(bgDark1.g + ratio * (bgDark2.g - bgDark1.g));
        const b = Math.round(bgDark1.b + ratio * (bgDark2.b - bgDark1.b));
        ctx.fillStyle = `rgb(${r}, ${g}, ${b})`;
        ctx.fillRect(0, y, size, 1);
    }
    
    // Tamanhos para o ícone
    const centerX = size / 2;
    const centerY = size / 2;
    const iconSize = size * 0.5;
    const boxSize = iconSize * 0.6;
    const boxX = centerX - boxSize / 2;
    const boxY = centerY - boxSize / 2;
    const offset = boxSize * 0.15;
    
    // Sombra
    ctx.fillStyle = 'rgba(0, 0, 0, 0.3)';
    ctx.fillRect(boxX + offset, boxY + offset, boxSize, boxSize);
    
    // Face frontal
    ctx.fillStyle = primary;
    ctx.strokeStyle = primaryDark;
    ctx.lineWidth = Math.max(1, size * 0.015);
    ctx.fillRect(boxX, boxY, boxSize, boxSize);
    ctx.strokeRect(boxX, boxY, boxSize, boxSize);
    
    // Face superior
    ctx.fillStyle = primaryLight;
    ctx.beginPath();
    ctx.moveTo(boxX, boxY);
    ctx.lineTo(boxX + offset, boxY - offset);
    ctx.lineTo(boxX + offset + boxSize, boxY - offset);
    ctx.lineTo(boxX + boxSize, boxY);
    ctx.closePath();
    ctx.fill();
    ctx.stroke();
    
    // Face lateral
    ctx.fillStyle = primaryDark;
    ctx.beginPath();
    ctx.moveTo(boxX + boxSize, boxY);
    ctx.lineTo(boxX + boxSize + offset, boxY - offset);
    ctx.lineTo(boxX + boxSize + offset, boxY - offset + boxSize);
    ctx.lineTo(boxX + boxSize, boxY + boxSize);
    ctx.closePath();
    ctx.fill();
    ctx.strokeStyle = primaryDarker;
    ctx.stroke();
    
    // Linhas divisórias
    ctx.strokeStyle = primaryDarker;
    ctx.lineWidth = Math.max(1, size * 0.01);
    
    // Linha horizontal superior
    ctx.beginPath();
    ctx.moveTo(boxX + boxSize * 0.5, boxY);
    ctx.lineTo(boxX + boxSize * 0.5 + offset, boxY - offset);
    ctx.stroke();
    
    // Linha horizontal inferior
    ctx.beginPath();
    ctx.moveTo(boxX + boxSize * 0.5, boxY + boxSize);
    ctx.lineTo(boxX + boxSize * 0.5 + offset, boxY - offset + boxSize);
    ctx.stroke();
    
    // Linha vertical divisória
    ctx.beginPath();
    ctx.moveTo(boxX + boxSize * 0.5, boxY);
    ctx.lineTo(boxX + boxSize * 0.5, boxY + boxSize);
    ctx.stroke();
    
    return canvas;
}

// Gerar todos os ícones
console.log('🎨 Gerando ícones PWA...\n');

const generated = [];

sizes.forEach(size => {
    process.stdout.write(`Gerando ícone ${size}x${size}... `);
    const canvas = generateIcon(size);
    const filename = size === 32 
        ? path.join(outputDir, 'favicon.png')
        : path.join(outputDir, `icon-${size}x${size}.png`);
    
    const buffer = canvas.toBuffer('image/png');
    fs.writeFileSync(filename, buffer);
    generated.push(path.basename(filename));
    console.log('✅');
});

console.log('\n✅ Ícones gerados com sucesso!');
console.log('\nArquivos criados em public/images/:');
generated.forEach(file => {
    console.log(`  ✓ ${file}`);
});

