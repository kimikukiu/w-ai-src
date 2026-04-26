import { loadConfig } from '@/lib/config';

const PROVIDERS = {
  'glm': {
    endpoint: 'https://api.z.ai/api/coding/paas/v4/chat/completions',
    apiKeyEnv: 'GLM_API_KEY',
    defaultModel: 'glm-5.1',
    models: ['glm-5.1', 'glm-5', 'glm-5-turbo', 'glm-4.7', 'glm-4.6', 'glm-4.5', 'glm-4', 'glm-4-plus', 'glm-4-flash']
  },
  'openai': {
    endpoint: 'https://api.openai.com/v1/chat/completions',
    apiKeyEnv: 'OPENAI_API_KEY',
    defaultModel: 'gpt-4o',
    models: ['gpt-5.4-pro', 'gpt-5.4', 'gpt-5.2', 'gpt-4o', 'gpt-4-turbo', 'gpt-4', 'gpt-3.5-turbo']
  },
  'anthropic': {
    endpoint: 'https://api.anthropic.com/v1/messages',
    apiKeyEnv: 'ANTHROPIC_API_KEY',
    defaultModel: 'claude-3-5-sonnet-20241022',
    models: ['claude-opus-4-6', 'claude-sonnet-4-6', 'claude-3.5-sonnet', 'claude-3-opus', 'claude-3-haiku']
  },
  'deepseek': {
    endpoint: 'https://api.deepseek.com/v1/chat/completions',
    apiKeyEnv: 'DEEPSEEK_API_KEY',
    defaultModel: 'deepseek-chat',
    models: ['deepseek-3.2', 'deepseek-3', 'deepseek-coder-33B', 'deepseek-chat']
  },
  'google': {
    endpoint: 'https://generativelanguage.googleapis.com/v1beta/models',
    apiKeyEnv: 'GOOGLE_API_KEY',
    defaultModel: 'gemini-2.0-flash',
    models: ['gemini-3.0-pro-preview', 'gemini-3-flash', 'gemini-2.0-pro', 'gemini-2.0-flash', 'gemini-1.5-pro', 'gemini-1.5-flash']
  },
  'kimi': {
    endpoint: 'https://api.moonshot.cn/v1/chat/completions',
    apiKeyEnv: 'KIMI_API_KEY',
    defaultModel: 'moonshot-v1-8k',
    models: ['kimi-k2.5', 'kimi-k2', 'kimi-k1.5', 'moonshot-v1-8k', 'moonshot-v1-32k', 'moonshot-v1-128k']
  },
  'github': {
    endpoint: 'https://models.github.ai/inference/chat/completions',
    apiKeyEnv: 'GITHUB_TOKEN',
    defaultModel: 'openai/gpt-4o',
    models: ['openai/gpt-4o', 'openai/gpt-4o-mini', 'meta-llama/Llama-3.3-70B-Instruct', 'deepseek-ai/DeepSeek-R1', 'anthropic/claude-3.5-sonnet', 'mistralai/Mixtral-8x7B-Instruct-v0.1']
  },
  'ollama': {
    endpoint: process.env.OLLAMA_URL || 'http://localhost:11434/api/chat',
    apiKeyEnv: '',
    defaultModel: 'hermes3:8b',
    models: ['hermes3:8b', 'hermes3:70b', 'llama3:8b', 'llama3.2:8b', 'mistral', 'codellama', 'phi3', 'qwen2.5:14b', 'deepseek-coder:33b', 'huihui-ai/qwen3-abliterated:8b-v2', 'mannix/llama3.1-8b-abliterated', 'slimaki-24b']
  },
  'groq': {
    endpoint: 'https://api.groq.com/openai/v1/chat/completions',
    apiKeyEnv: 'GROQ_API_KEY',
    defaultModel: 'llama-3.3-70b-versatile',
    models: ['llama-3.3-70b-versatile', 'llama-3.3-70b-instruct', 'mixtral-8x7b-32768', 'llama3-70b-8192', 'llama3-8b-8192', 'gemma2-9b-it']
  },
  'together': {
    endpoint: 'https://api.together.ai/v1/chat/completions',
    apiKeyEnv: 'TOGETHER_API_KEY',
    defaultModel: 'meta-llama/Llama-3.3-70B-Instruct-Turbo',
    models: ['meta-llama/Llama-3.3-70B-Instruct-Turbo', 'meta-llama/Llama-3.2-11B-Vision-Instruct-Turbo', 'mistralai/Mixtral-8x7B-Instruct-v0.1', 'Qwen/Qwen2.5-72B-Instruct-Turbo', 'deepseek-ai/DeepSeek-V3', 'nvidia/Llama-3.1-Nemotron-70B-Instruct-Hero']
  },
  'lepton': {
    endpoint: 'https://api.lepton.ai/api/v1/chat/completions',
    apiKeyEnv: 'LEPTON_API_KEY',
    defaultModel: 'llama-3.3-70b',
    models: ['llama-3.3-70b', 'llama-3.1-8b', 'mixtral-8x7b']
  },
  'huggingface': {
    endpoint: 'https://api-inference.huggingface.co/v1/chat/completions',
    apiKeyEnv: 'HF_API_KEY',
    defaultModel: 'cloudbjorn/Qwen3.6-27B_Samantha-Uncensored',
    models: [
      'cloudbjorn/Qwen3.6-27B_Samantha-Uncensored',
      'huihui-ai/Huihui-Qwen3.5-35B-A3B-abliterated',
      'paperscarecrow/Gemma-4-31B-it-abliterated',
      'meta-llama/Llama-3.3-70B-Instruct',
      'mistralai/Mixtral-8x7B-Instruct-v0.1',
      'Qwen/Qwen2.5-72B-Instruct-Turbo'
    ]
  },
  'swarm': {
    endpoint: 'https://models.github.ai/inference/chat/completions',
    apiKeyEnv: 'GITHUB_TOKEN',
    defaultModel: 'openai/gpt-4o',
    models: [] // All SWARM models route here
  }
};

function detectProvider(modelId: string): { provider: string; mappedModel: string } {
  const modelLower = modelId.toLowerCase();

  // SWARM models - route to GitHub Models (free)
  if (modelLower.includes('swarm') || modelLower.includes('wormgpt') || modelLower.includes('redgpt') || modelLower.includes('darkgpt') || modelLower.includes('queen-swarm')) {
    return { provider: 'swarm', mappedModel: 'openai/gpt-4o' };
  }

  // GLM models - route to GitHub Models (free) since z.ai has no balance
  if (modelLower.startsWith('glm')) {
    return { provider: 'github', mappedModel: 'meta-llama/Llama-3.3-70B-Instruct' };
  }

  // OpenAI models
  if (modelLower.startsWith('gpt')) {
    return { provider: 'openai', mappedModel: modelId };
  }

  // Claude models
  if (modelLower.startsWith('claude')) {
    return { provider: 'anthropic', mappedModel: modelId };
  }

  // DeepSeek models
  if (modelLower.startsWith('deepseek') || modelLower.includes('deepseek')) {
    return { provider: 'deepseek', mappedModel: modelId };
  }

  // Google models
  if (modelLower.startsWith('gemini')) {
    return { provider: 'google', mappedModel: modelId };
  }

  // Kimi models
  if (modelLower.startsWith('kimi') || modelLower.startsWith('moonshot')) {
    return { provider: 'kimi', mappedModel: modelId };
  }

  // Ollama uncensored models
  if (modelLower.includes('hermes') || modelLower.includes('slimaki') || modelLower.includes('huihui') || modelLower.includes('abliterated') || modelLower.includes('mistral') || modelLower.includes('codellama') || modelLower.includes('phi3')) {
    return { provider: 'ollama', mappedModel: modelId };
  }

  // Groq models (fast, free tier)
  if (modelLower.includes('groq') || modelLower.includes('llama-3.3') || modelLower.includes('mixtral') || modelLower.includes('gemma')) {
    return { provider: 'groq', mappedModel: modelId };
  }

  // Together.ai models
  if (modelLower.includes('together') || modelLower.includes('qwen') || modelLower.includes('nemotron')) {
    return { provider: 'together', mappedModel: modelId };
  }

  // Lepton models
  if (modelLower.includes('lepton')) {
    return { provider: 'lepton', mappedModel: modelId };
  }

  // Hugging Face models (Pro account) - uncensored & abliterated
  if (modelLower.includes('hf') || modelLower.includes('hugging') || modelLower.includes('samantha') || modelLower.includes('huihui') || modelLower.includes('abliterated') || modelLower.includes('gemma') || modelLower.startsWith('meta-') || modelLower.startsWith('mistral') || modelLower.startsWith('qwen') || modelLower.startsWith('deepseek')) {
    return { provider: 'huggingface', mappedModel: modelId };
  }

  // Default to GitHub Models (free)
  return { provider: 'github', mappedModel: 'openai/gpt-4o' };
}

function getApiKey(provider: string): string | null {
  const config = loadConfig();

  switch (provider) {
    case 'glm':
      return config.glm_api_key || process.env.GLM_API_KEY || null;
    case 'github':
      return config.github_token || process.env.GITHUB_TOKEN || null;
    case 'ollama':
      return ''; // No API key needed for local Ollama
    case 'swarm':
      return config.github_token || process.env.GITHUB_TOKEN || config.glm_api_key || process.env.GLM_API_KEY || null;
    case 'openai':
      return process.env.OPENAI_API_KEY || null;
    case 'anthropic':
      return process.env.ANTHROPIC_API_KEY || null;
    case 'deepseek':
      return process.env.DEEPSEEK_API_KEY || null;
    case 'google':
      return process.env.GEMINI_API_KEY || null;
    case 'kimi':
      return process.env.KIMI_API_KEY || null;
    case 'groq':
      return process.env.GROQ_API_KEY || null;
    case 'together':
      return process.env.TOGETHER_API_KEY || null;
    case 'lepton':
      return process.env.LEPTON_API_KEY || null;
    case 'huggingface':
      return process.env.HF_API_KEY || null;
    default:
      return null;
  }
}

export async function multiProviderChat(messages: any[], modelId: string): Promise<string> {
  const { provider, mappedModel } = detectProvider(modelId);
  const config = PROVIDERS[provider as keyof typeof PROVIDERS];

  if (!config) {
    throw new Error(`Unknown provider for model: ${modelId}`);
  }

  const apiKey = getApiKey(provider);
  if (!apiKey && provider !== 'ollama') {
    throw new Error(`No API key configured for provider: ${provider}`);
  }

  // Build request based on provider
  const headers: Record<string, string> = {
    'Content-Type': 'application/json',
  };

  let body: any = {
    model: mappedModel,
    messages,
    temperature: 0.7,
    max_tokens: 8192,
  };

  let endpoint = config.endpoint;

  switch (provider) {
    case 'glm':
    case 'swarm':
      headers['Authorization'] = `Bearer ${apiKey}`;
      headers['Accept-Language'] = 'en-US,en';
      break;

    case 'openai':
    case 'deepseek':
      headers['Authorization'] = `Bearer ${apiKey}`;
      break;

    case 'anthropic':
      headers['x-api-key'] = apiKey;
      headers['anthropic-version'] = '2023-06-01';
      delete headers['Content-Type'];
      body = {
        model: mappedModel,
        messages,
        max_tokens: 8192,
      };
      break;

    case 'google':
      headers['x-goog-api-key'] = apiKey;
      endpoint = `${config.endpoint}/${mappedModel}:generateContent?key=${apiKey}`;
      body = {
        contents: messages.map((m: any) => ({
          role: m.role === 'assistant' ? 'model' : 'user',
          parts: [{ text: m.content }]
        })),
        generationConfig: {
          temperature: 0.7,
          maxOutputTokens: 8192,
        },
      };
      break;

    case 'kimi':
      headers['Authorization'] = `Bearer ${apiKey}`;
      break;

    case 'groq':
    case 'together':
    case 'lepton':
    case 'huggingface':
      headers['Authorization'] = `Bearer ${apiKey}`;
      break;

    case 'ollama':
      // No auth needed for Ollama local
      body = {
        model: mappedModel,
        messages,
        stream: false,
        options: {
          temperature: 0.7,
          num_predict: 8192,
        },
      };
      break;
  }

  const controller = new AbortController();
  const timeout = setTimeout(() => controller.abort(), 15000);

  try {
    const response = await fetch(endpoint, {
      method: 'POST',
      headers,
      body: JSON.stringify(body),
      signal: controller.signal,
    });

    clearTimeout(timeout);

    if (!response.ok) {
      const errorText = await response.text();
      throw new Error(`${provider.toUpperCase()} API error ${response.status}: ${errorText}`);
    }

    const data = await response.json();

    // Parse response based on provider
    switch (provider) {
      case 'glm':
      case 'swarm':
      case 'openai':
      case 'deepseek':
      case 'kimi':
        return data.choices?.[0]?.message?.content || '';

      case 'anthropic':
        return data.content?.[0]?.text || '';

      case 'google':
        return data.candidates?.[0]?.content?.parts?.[0]?.text || '';

      case 'ollama':
        return data.message?.content || data.response || '';

      default:
        return JSON.stringify(data);
    }

  } catch (error: any) {
    clearTimeout(timeout);
    if (error.name === 'AbortError') {
      throw new Error(`${provider.toUpperCase()} request timeout`);
    }
    throw error;
  }
}

export { detectProvider, getApiKey };
