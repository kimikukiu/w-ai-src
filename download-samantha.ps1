# Samantha Uncensored Model Downloader for Windows
# Run locally as backup if HF API goes down

Write-Host "📥 Downloading Samantha Uncensored Qwen3.6-27B..." -ForegroundColor Cyan
Write-Host "⚠️  This is ~50GB, ensure you have enough disk space!" -ForegroundColor Yellow

# Check if hf CLI is installed
$hfInstalled = Get-Command huggingface-cli -ErrorAction SilentlyContinue

if ($hfInstalled) {
    Write-Host "Using huggingface-cli..." -ForegroundColor Green
    huggingface-cli download cloudbjorn/Qwen3.6-27B_Samantha-Uncensored
} else {
    Write-Host "Installing huggingface-hub..." -ForegroundColor Yellow
    pip install huggingface-hub
    
    Write-Host "Downloading model..." -ForegroundColor Green
    python -c "from huggingface_hub import snapshot_download; snapshot_download('cloudbjorn/Qwen3.6-27B_Samantha-Uncensored')"
}

Write-Host "✅ Download complete!" -ForegroundColor Green
Write-Host ""
Write-Host "To serve locally with llama.cpp:" -ForegroundColor Cyan
Write-Host "  1. Install llama.cpp from https://github.com/ggerganov/llama.cpp"
Write-Host "  2. Quantize model if needed: ./quantize ./models/*.bin Q4_K_M"
Write-Host "  3. Run server: ./server -m ./models/*.gguf -c 32768 --host 0.0.0.0 --port 8080"
Write-Host "  4. Set OLLAMA_URL=http://localhost:8080 in your .env"
