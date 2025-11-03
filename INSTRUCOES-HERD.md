# 🚀 Instruções para Usar com Laravel Herd

## ✅ Pré-requisitos

1. **Laravel Herd instalado e rodando**
   - Download: https://herd.laravel.com/
   - Instale e inicie o Herd

2. **Projeto configurado no Herd**
   - O Herd detecta automaticamente projetos Laravel na pasta configurada
   - Ou adicione o projeto manualmente

## 📝 Configuração

### 1. Verificar domínio do projeto

O Herd cria automaticamente um domínio `.test` para cada projeto.

Exemplo: Se seu projeto está em `C:\Users\Alexandre\Desktop\ALEXANDRE-S`
O domínio será: `alexandre-s.test` (baseado no nome da pasta)

### 2. Acessar no navegador

**No PC:**
```
https://alexandre-s.test
```

**No celular (mesma rede WiFi):**
1. Descubra o IP do seu PC:
   - Windows: `ipconfig` no CMD
   - Procure por "IPv4 Address" (ex: 192.168.1.100)

2. Edite o arquivo `hosts` no celular ou acesse via IP:
   ```
   https://192.168.1.100
   ```

   **OU** configure DNS no roteador para apontar `alexandre-s.test` para o IP do PC.

### 3. Verificar HTTPS

O Herd cria HTTPS automaticamente com certificado auto-assinado.

**No primeiro acesso:**
- O navegador pode mostrar aviso de certificado não confiável
- Clique em "Avançado" → "Continuar mesmo assim"
- Isso é normal em desenvolvimento

## 🔍 Verificar se está funcionando

### 1. Service Worker registrado

Abra o Console (F12) e procure por:
```
✅ Service Worker registrado: /
```

### 2. PWA funcionando

- Clique nos três pontos → "Instalar app"
- Ou aparecerá prompt automático para instalar

### 3. Testar offline

1. **Abra DevTools (F12)**
2. **Vá em Network (Rede)**
3. **Marque "Offline"**
4. **Tente usar o sistema**

Deve funcionar offline!

## 🐛 Troubleshooting

### Service Worker não registra

**Verificar:**
1. Acessar via **HTTPS** (não HTTP)
2. Verificar se `/sw.js` está acessível
3. Console do navegador para erros

**Solução:**
```bash
# Limpar cache do Service Worker
# Chrome: DevTools → Application → Service Workers → Unregister
```

### Certificado inválido no celular

**Solução:**
- Acesse primeiro no PC e aceite o certificado
- Depois tente no celular
- Se necessário, instale o certificado do Herd manualmente

### Não funciona no celular

**Verificar:**
1. ✅ Está usando **HTTPS** (não HTTP)
2. ✅ Mesma rede WiFi
3. ✅ IP correto ou DNS configurado
4. ✅ Service Worker registrado no console do celular

**Teste no celular:**
1. Abra Chrome/Edge
2. Vá em DevTools remoto ou use `chrome://inspect`
3. Veja o console para erros

## 📱 Comandos Úteis

```bash
# Verificar projetos no Herd
herd list

# Reiniciar Herd
herd restart

# Ver logs
herd logs
```

## ✅ Checklist Final

- [ ] Herd instalado e rodando
- [ ] Projeto acessível via HTTPS
- [ ] Service Worker registrado (ver console)
- [ ] Manifest.json carregando
- [ ] Funciona offline no PC
- [ ] Funciona offline no celular
- [ ] Formulários salvam offline

---

**Pronto!** Seu sistema offline está configurado para funcionar com Laravel Herd! 🎉

