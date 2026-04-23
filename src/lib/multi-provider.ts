import { loadConfig } from '@/lib/config';

const PROVIDERS = {
  'glm': {
    endpoint: 'https://api.z.ai/api/coding/paas/v4/chat/completions',
    apiKeyEnv: 'GLM_API_KEY',
    defaultModel: 'glm-5.1',
    models: ['glm-5.1', 'glm-5', 'glm-5-turbo', 'glm-4.7', 'glm-4.6', 'glm-4.5', 'glm-4', 'glm-4-plus', 'glm-4-flash']
  },
  // GitHub Models — free API via GitHub Personal Access Token (models:read scope)
  // Endpoint: https://models.inference.ai.azure.com (OpenAI-compatible)
  // Get your token at: https://github.com/settings/tokens
  'github': {
    endpoint: 'https://models.inference.ai.azure.com/chat/completions',
    apiKeyEnv: 'GITHUB_TOKEN',
    defaultModel: 'gpt-4o-mini',
    models: ['gpt-4o', 'gpt-4o-mini', 'gpt-4.1', 'gpt-4.1-mini', 'gpt-4.1-nano', 'o1', 'o1-mini', 'o3', 'o3-mini', 'o4-mini', 'Llama-4-Scout-17B-16E-Instruct', 'Llama-4-Maverick-17B-128E-Instruct', 'Meta-Llama-3.3-70B-Instruct', 'Mistral-large', 'Mistral-small', 'mistral-large-2411', 'AI21-Jamba-1.5-Large', 'AI21-Jamba-1.5-Mini', 'Phi-4', 'Phi-4-mini', 'Phi-3.5-MoE-instruct']
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
  'swarm': {
    endpoint: 'https://api.z.ai/api/coding/paas/v4/chat/completions',
    apiKeyEnv: 'GLM_API_KEY',
    defaultModel: 'glm-5.1',
    models: [] // All SWARM models route here
  }
};

function detectProvider(modelId: string): { provider: string; mappedModel: string } {
  const modelLower = modelId.toLowerCase();

  // SWARM models
  if (modelLower.includes('swarm') || modelLower.includes('wormgpt') || modelLower.includes('redgpt') || modelLower.includes('darkgpt') || modelLower.includes('queen-swarm')) {
    return { provider: 'swarm', mappedModel: 'glm-5.1' };
  }

  // GLM models
  if (modelLower.startsWith('glm')) {
    return { provider: 'glm', mappedModel: modelId };
  }

  // OpenAI / GPT models — use GitHub Models (free) if GITHUB_TOKEN is set, else OpenAI
  if (modelLower.startsWith('gpt') || modelLower.startsWith('o1') || modelLower.startsWith('o3') || modelLower.startsWith('o4')) {
    const hasGithubToken = !!(process.env.GITHUB_TOKEN);
    const hasOpenaiKey = !!(process.env.OPENAI_API_KEY);
    if (hasGithubToken) {
      // Map to a GitHub Models-supported model name
      const githubModelMap: Record<string, string> = {
        'gpt-4o': 'gpt-4o',
        'gpt-4o-mini': 'gpt-4o-mini',
        'gpt-4-turbo': 'gpt-4o',
        'gpt-4': 'gpt-4o',
        'gpt-3.5-turbo': 'gpt-4o-mini',
        'o1': 'o1',
        'o1-mini': 'o4-mini',
        'o3': 'o3',
        'o3-mini': 'o3-mini',
        'o4-mini': 'o4-mini',
      };
      const mapped = githubModelMap[modelId] || 'gpt-4o-mini';
      return { provider: 'github', mappedModel: mapped };
    }
    if (hasOpenaiKey) {
      return { provider: 'openai', mappedModel: modelId };
    }
    // Default to GitHub Models even without token — will fail with clear error
    return { provider: 'github', mappedModel: 'gpt-4o-mini' };
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

  // Default to GLM
  return { provider: 'glm', mappedModel: 'glm-5.1' };
}

function getApiKey(provider: string): string | null {
  const config = loadConfig();

  switch (provider) {
    case 'glm':
    case 'swarm':
      return config.glm_api_key || process.env.GLM_API_KEY || null;
    case 'github':
      // GitHub Personal Access Token with models:read scope — free to use
      // Create at: https://github.com/settings/tokens (classic or fine-grained with models:read)
      return process.env.GITHUB_TOKEN || process.env.GITHUB_MODELS_TOKEN || null;
    case 'openai':
      return process.env.OPENAI_API_KEY || null;
    case 'anthropic':
      return process.env.ANTHROPIC_API_KEY || null;
    case 'deepseek':
      return process.env.DEEPSEEK_API_KEY || null;
    case 'google':
      return process.env.GOOGLE_API_KEY || null;
    case 'kimi':
      return process.env.KIMI_API_KEY || null;
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
  if (!apiKey) {
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

    case 'github':
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
      case 'github':
      case 'openai':
      case 'deepseek':
      case 'kimi':
        return data.choices?.[0]?.message?.content || '';

      case 'anthropic':
        return data.content?.[0]?.text || '';

      case 'google':
        return data.candidates?.[0]?.content?.parts?.[0]?.text || '';

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
