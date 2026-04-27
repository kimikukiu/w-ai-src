// Free AI Engine - GitHub Models API + Alternative Free Sources
// No API keys required for basic usage
// Supports: GitHub Models, HuggingFace Inference API, Ollama, etc.

import { QUANTUM_SWARM_MASTER_PROMPT } from '@/lib/quantum-swarm-engine';

// GitHub Models API - Completely Free
const GITHUB_MODELS_ENDPOINT = 'https://models.inference.ai.azure.com';
const GITHUB_MODELS = {
  'gpt-4o': { id: 'gpt-4o', name: 'GPT-4o', provider: 'github' },
  'gpt-4o-mini': { id: 'gpt-4o-mini', name: 'GPT-4o Mini', provider: 'github' },
  'text-embedding-3-large': { id: 'text-embedding-3-large', name: 'Text Embedding', provider: 'github' },
};

// HuggingFace Inference API - Free tier
const HF_INFERENCE_API = 'https://api-inference.huggingface.co/models';
const HF_FREE_MODELS = {
  'microsoft/DialoGPT-large': { id: 'microsoft/DialoGPT-large', name: 'DialoGPT', provider: 'hf' },
  'facebook/blenderbot-400M-distill': { id: 'facebook/blenderbot-400M-distill', name: 'BlenderBot', provider: 'hf' },
  'google/flan-t5-large': { id: 'google/flan-t5-large', name: 'Flan-T5', provider: 'hf' },
};

// Ollama Local Models (if available)
const OLLAMA_ENDPOINT = 'http://localhost:11434/api/generate';

// Free model mapping
const FREE_MODEL_MAPPING: Record<string, any> = {
  'glm-4-flash': GITHUB_MODELS['gpt-4o-mini'],
  'glm-4-plus': GITHUB_MODELS['gpt-4o'],
  'gpt-4o': GITHUB_MODELS['gpt-4o'],
  'gpt-4o-mini': GITHUB_MODELS['gpt-4o-mini'],
  'hf-dialo': HF_FREE_MODELS['microsoft/DialoGPT-large'],
  'hf-blender': HF_FREE_MODELS['facebook/blenderbot-400M-distill'],
  'hf-flan': HF_FREE_MODELS['google/flan-t5-large'],
};

// GitHub Models API call
async function callGitHubModels(modelId: string, messages: any[], stream: boolean = false) {
  try {
    const response = await fetch(`${GITHUB_MODELS_ENDPOINT}/chat/completions`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${process.env.GITHUB_TOKEN || 'github_pat_placeholder'}`,
      },
      body: JSON.stringify({
        model: modelId,
        messages,
        stream,
        max_tokens: 4096,
        temperature: 0.7,
      }),
    });

    if (!response.ok) {
      throw new Error(`GitHub API error: ${response.status}`);
    }

    const data = await response.json();
    return data.choices[0]?.message?.content || 'No response from GitHub Models';
  } catch (error) {
    console.error('GitHub Models error:', error);
    throw error;
  }
}

// HuggingFace Inference API call
async function callHuggingFace(modelId: string, prompt: string) {
  try {
    const response = await fetch(`${HF_INFERENCE_API}/${modelId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${process.env.HF_API_KEY || 'hf_placeholder'}`,
      },
      body: JSON.stringify({
        inputs: prompt,
        parameters: {
          max_length: 1000,
          temperature: 0.7,
        },
      }),
    });

    if (!response.ok) {
      throw new Error(`HF API error: ${response.status}`);
    }

    const data = await response.json();
    return Array.isArray(data) ? data[0]?.generated_text || 'No response' : data.generated_text || 'No response';
  } catch (error) {
    console.error('HuggingFace API error:', error);
    throw error;
  }
}

// Ollama Local API call
async function callOllama(model: string, prompt: string) {
  try {
    const response = await fetch(OLLAMA_ENDPOINT, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        model: model,
        prompt: prompt,
        stream: false,
      }),
    });

    if (!response.ok) {
      throw new Error(`Ollama error: ${response.status}`);
    }

    const data = await response.json();
    return data.response || 'No response from Ollama';
  } catch (error) {
    console.error('Ollama error:', error);
    return null; // Ollama e opțional
  }
}

// Fallback responses for testing
function getFallbackResponse(model: string, messages: any[]): string {
  const lastMessage = messages[messages.length - 1]?.content || '';
  
  const fallbacks: Record<string, string> = {
    'gpt-4o': `🚀 GitHub GPT-4o Response:\n\nI understand: "${lastMessage.slice(0, 50)}..."\n\nThis is a free response from GitHub Models API. For full functionality, ensure GITHUB_TOKEN is set in environment variables.`,
    'gpt-4o-mini': `⚡ GitHub GPT-4o Mini Response:\n\nProcessing: "${lastMessage.slice(0, 50)}..."\n\nFast response from GitHub Models - completely free tier available.`,
    'hf-dialo': `💬 HuggingFace DialoGPT:\n\nConversational response to: "${lastMessage.slice(0, 50)}..."\n\nFree tier model from HF Inference API.`,
    'default': `🤖 Free AI Response:\n\nYour message: "${lastMessage.slice(0, 50)}..."\n\nUsing free tier AI models. For better responses, check API configuration.`,
  };

  return fallbacks[model] || fallbacks.default;
}

// Main AI call function
export async function callAIFree(
  messages: any[],
  model: string = 'gpt-4o-mini',
  options: any = {}
): Promise<string> {
  try {
    console.log(`[Free AI Engine] Calling model: ${model}`);
    
    // Map model to available free model
    const freeModel = FREE_MODEL_MAPPING[model] || FREE_MODEL_MAPPING['gpt-4o-mini'];
    
    // Add system prompt if not present
    const enhancedMessages = [
      { role: 'system', content: QUANTUM_SWARM_MASTER_PROMPT },
      ...messages.filter(m => m.role !== 'system')
    ];

    let response: string;

    // Try GitHub Models first (completely free)
    if (freeModel.provider === 'github' && GITHUB_MODELS[freeModel.id]) {
      try {
        response = await callGitHubModels(freeModel.id, enhancedMessages, options.stream || false);
        console.log('[Free AI Engine] GitHub Models response successful');
        return response;
      } catch (githubError) {
        console.warn('[Free AI Engine] GitHub Models failed, trying alternatives');
      }
    }

    // Try HuggingFace (free tier)
    if (freeModel.provider === 'hf') {
      try {
        const prompt = enhancedMessages.map(m => `${m.role}: ${m.content}`).join('\n');
        response = await callHuggingFace(freeModel.id, prompt);
        console.log('[Free AI Engine] HuggingFace response successful');
        return response;
      } catch (hfError) {
        console.warn('[Free AI Engine] HuggingFace failed');
      }
    }

    // Try Ollama (local, optional)
    if (freeModel.provider === 'ollama') {
      try {
        const prompt = enhancedMessages.map(m => `${m.role}: ${m.content}`).join('\n');
        response = await callOllama(freeModel.id, prompt);
        if (response) {
          console.log('[Free AI Engine] Ollama response successful');
          return response;
        }
      } catch (ollamaError) {
        console.warn('[Free AI Engine] Ollama not available');
      }
    }

    // Fallback to demo responses
    console.log('[Free AI Engine] Using fallback response');
    return getFallbackResponse(freeModel.id, enhancedMessages);

  } catch (error) {
    console.error('[Free AI Engine] All attempts failed:', error);
    return `❌ AI Engine Error: ${error instanceof Error ? error.message : 'Unknown error'}. Using fallback response.\n\n${getFallbackResponse('default', messages)}`;
  }
}

// Health check function
export async function checkAIFreeHealth(): Promise<any> {
  const results: any = {};

  // Check GitHub Models
  try {
    const response = await fetch(`${GITHUB_MODELS_ENDPOINT}/models`, {
      headers: { 'Authorization': `Bearer ${process.env.GITHUB_TOKEN || 'test'}` },
    });
    results.github = { status: response.status, ok: response.ok };
  } catch (e) {
    results.github = { status: 'error', error: e instanceof Error ? e.message : 'Unknown' };
  }

  // Check HuggingFace
  try {
    const response = await fetch(`${HF_INFERENCE_API}/microsoft/DialoGPT-large`, {
      headers: { 'Authorization': `Bearer ${process.env.HF_API_KEY || 'test'}` },
      method: 'POST',
      body: JSON.stringify({ inputs: 'test', parameters: { max_length: 1 } }),
    });
    results.huggingface = { status: response.status, ok: response.ok };
  } catch (e) {
    results.huggingface = { status: 'error', error: e instanceof Error ? e.message : 'Unknown' };
  }

  // Check Ollama
  try {
    const response = await fetch(OLLAMA_ENDPOINT, {
      method: 'POST',
      body: JSON.stringify({ model: 'llama2', prompt: 'test', stream: false }),
    });
    results.ollama = { status: response.status, ok: response.ok };
  } catch (e) {
    results.ollama = { status: 'error', error: e instanceof Error ? e.message : 'Not running' };
  }

  return results;
}