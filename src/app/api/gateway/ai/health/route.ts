import { NextRequest, NextResponse } from 'next/server';
import { checkAIFreeHealth } from '@/lib/ai-engine-free';

export async function GET(request: NextRequest) {
  try {
    const health = await checkAIFreeHealth();
    
    return NextResponse.json({
      status: 'ok',
      timestamp: new Date().toISOString(),
      services: health,
      available_models: {
        github: ['gpt-4o', 'gpt-4o-mini'],
        huggingface: ['microsoft/DialoGPT-large', 'facebook/blenderbot-400M-distill', 'google/flan-t5-large'],
        ollama: ['llama2', 'mistral', 'codellama'],
      },
      notes: [
        'GitHub Models: Completely free with GitHub account',
        'HuggingFace: Free tier with rate limits',
        'Ollama: Local models, requires local installation',
        'Fallback responses available if all APIs fail',
      ]
    });
  } catch (error) {
    return NextResponse.json({
      status: 'error',
      error: error instanceof Error ? error.message : 'Unknown error',
      timestamp: new Date().toISOString(),
    }, { status: 500 });
  }
}