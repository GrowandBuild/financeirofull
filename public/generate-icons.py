#!/usr/bin/env python3
"""
Gerador de Ícones PWA
Gera todos os ícones necessários para o PWA
"""

try:
    from PIL import Image, ImageDraw, ImageFont
    import os
except ImportError:
    print("❌ Erro: Biblioteca PIL/Pillow não instalada.")
    print("\nPara instalar:")
    print("  pip install Pillow")
    print("\nOu use o gerador HTML: public/images/icon-generator.html")
    exit(1)

# Tamanhos dos ícones
sizes = [32, 72, 96, 128, 144, 152, 192, 384, 512]
output_dir = os.path.join(os.path.dirname(__file__), 'images')

# Criar diretório se não existir
os.makedirs(output_dir, exist_ok=True)

def generate_icon(size):
    """Gera um ícone do tamanho especificado"""
    # Criar imagem com fundo transparente
    img = Image.new('RGBA', (size, size), (0, 0, 0, 0))
    draw = ImageDraw.Draw(img)
    
    # Cores
    bg_dark_1 = (31, 41, 55)  # #1f2937
    bg_dark_2 = (55, 65, 81)  # #374151
    primary = (16, 185, 129)  # #10b981
    primary_dark = (5, 150, 105)  # #059669
    primary_light = (52, 211, 153)  # #34d399
    primary_darker = (4, 120, 87)  # #047857
    
    # Fundo gradiente (simulado com linhas)
    for y in range(size):
        ratio = y / size
        r = int(bg_dark_1[0] + ratio * (bg_dark_2[0] - bg_dark_1[0]))
        g = int(bg_dark_1[1] + ratio * (bg_dark_2[1] - bg_dark_1[1]))
        b = int(bg_dark_1[2] + ratio * (bg_dark_2[2] - bg_dark_1[2]))
        draw.line([(0, y), (size, y)], fill=(r, g, b))
    
    # Tamanhos para o ícone de caixa
    center_x = size / 2
    center_y = size / 2
    icon_size = size * 0.5
    box_size = icon_size * 0.6
    box_x = center_x - box_size / 2
    box_y = center_y - box_size / 2
    offset = box_size * 0.15
    
    # Sombra
    shadow_box = (
        int(box_x + offset),
        int(box_y + offset),
        int(box_x + offset + box_size),
        int(box_y + offset + box_size)
    )
    draw.rectangle(shadow_box, fill=(0, 0, 0, 76))  # rgba(0,0,0,0.3)
    
    # Face frontal (caixa)
    front_box = (
        int(box_x),
        int(box_y),
        int(box_x + box_size),
        int(box_y + box_size)
    )
    draw.rectangle(front_box, fill=primary, outline=primary_dark, width=max(1, int(size * 0.015)))
    
    # Face superior
    top_points = [
        (int(box_x), int(box_y)),
        (int(box_x + offset), int(box_y - offset)),
        (int(box_x + offset + box_size), int(box_y - offset)),
        (int(box_x + box_size), int(box_y))
    ]
    draw.polygon(top_points, fill=primary_light, outline=primary_dark)
    
    # Face lateral
    side_points = [
        (int(box_x + box_size), int(box_y)),
        (int(box_x + box_size + offset), int(box_y - offset)),
        (int(box_x + box_size + offset), int(box_y - offset + box_size)),
        (int(box_x + box_size), int(box_y + box_size))
    ]
    draw.polygon(side_points, fill=primary_dark, outline=primary_darker)
    
    # Linhas divisórias
    line_width = max(1, int(size * 0.01))
    
    # Linha horizontal superior
    draw.line([
        (int(box_x + box_size * 0.5), int(box_y)),
        (int(box_x + box_size * 0.5 + offset), int(box_y - offset))
    ], fill=primary_darker, width=line_width)
    
    # Linha horizontal inferior
    draw.line([
        (int(box_x + box_size * 0.5), int(box_y + box_size)),
        (int(box_x + box_size * 0.5 + offset), int(box_y - offset + box_size))
    ], fill=primary_darker, width=line_width)
    
    # Linha vertical divisória
    draw.line([
        (int(box_x + box_size * 0.5), int(box_y)),
        (int(box_x + box_size * 0.5), int(box_y + box_size))
    ], fill=primary_darker, width=line_width)
    
    return img

# Gerar todos os ícones
print("🎨 Gerando ícones PWA...")
print()

generated = []

for size in sizes:
    print(f"Gerando ícone {size}x{size}...", end=" ")
    img = generate_icon(size)
    
    if size == 32:
        filename = os.path.join(output_dir, 'favicon.png')
    else:
        filename = os.path.join(output_dir, f'icon-{size}x{size}.png')
    
    img.save(filename, 'PNG', optimize=True)
    generated.append(os.path.basename(filename))
    print("✅")

print()
print("✅ Ícones gerados com sucesso!")
print()
print("Arquivos criados em public/images/:")
for filename in generated:
    print(f"  ✓ {filename}")

