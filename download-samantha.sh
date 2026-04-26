#!/bin/bash
# Samantha Uncensored Model Downloader
# Run locally as backup if HF API goes down

set -e

echo "📥 Downloading Samantha Uncensored Qwen3.6-27B..."
echo "⚠️  This is ~50GB, ensure you have enough disk space!"

# Method 1: Using huggingface-cli (recommended)
if command -v huggingface-cli &> /dev/null; then
    echo "Using huggingface-cli..."
    huggingface-cli download cloudbjorn/Qwen3.6-27B_Samantha-Uncensored
else
    echo "Installing huggingface-cli..."
    pip install huggingface-hub
    python -c "from huggingface_hub import snapshot_download; snapshot_download('cloudbjorn/Qwen3.6-27B_Samantha-Uncensored')"
fi

echo "✅ Download complete!"
echo ""
echo "To serve locally with llama.cpp:"
echo "  1. Install llama.cpp: brew install llama.cpp  (mac) or build from source"
echo "  2. Run: ./server -m ./models/cloudbjorn--Qwen3.6-27B_Samantha-Uncensored/*.gguf -c 32768 --host 0.0.0.0 --port 8080"
echo "  3. Set OLLAMA_URL=http://localhost:8080 in your .env"
