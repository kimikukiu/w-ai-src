import { describe, it, expect, vi, beforeEach } from 'vitest';
import { callAIFree, checkAIFreeHealth } from '../ai-engine-free';

// Mock fetch
global.fetch = vi.fn();

describe('AI Engine Free', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  describe('callAIFree', () => {
    it('should return fallback response when all APIs fail', async () => {
      (global.fetch as any).mockRejectedValue(new Error('Network error'));

      const messages = [
        { role: 'user', content: 'Hello, test message' }
      ];

      const result = await callAIFree(messages);
      
      expect(result).toContain('Free AI Response');
      expect(result).toContain('Hello, test message');
    });

    it('should handle GitHub Models API success', async () => {
      const mockResponse = {
        choices: [{
          message: { content: 'Test response from GitHub' }
        }]
      };

      (global.fetch as any).mockResolvedValueOnce({
        ok: true,
        json: async () => mockResponse,
      });

      const messages = [
        { role: 'user', content: 'Test message' }
      ];

      const result = await callAIFree(messages, 'gpt-4o');
      
      expect(result).toBe('Test response from GitHub');
      expect(global.fetch).toHaveBeenCalledWith(
        'https://models.inference.ai.azure.com/chat/completions',
        expect.objectContaining({
          method: 'POST',
          headers: expect.objectContaining({
            'Content-Type': 'application/json',
          }),
        })
      );
    });

    it('should handle HuggingFace API success', async () => {
      const mockResponse = {
        generated_text: 'Test response from HF'
      };

      // First call (GitHub) fails
      (global.fetch as any).mockRejectedValueOnce(new Error('GitHub failed'));
      
      // Second call (HF) succeeds
      (global.fetch as any).mockResolvedValueOnce({
        ok: true,
        json: async () => mockResponse,
      });

      const messages = [
        { role: 'user', content: 'Test message' }
      ];

      const result = await callAIFree(messages, 'hf-dialo');
      
      expect(result).toBe('Test response from HF');
    });

    it('should include system prompt in all requests', async () => {
      (global.fetch as any).mockResolvedValueOnce({
        ok: true,
        json: async () => ({ choices: [{ message: { content: 'Response' } }] }),
      });

      const messages = [
        { role: 'user', content: 'User message' }
      ];

      await callAIFree(messages);

      const fetchCall = (global.fetch as any).mock.calls[0];
      const body = JSON.parse(fetchCall[1].body);
      
      expect(body.messages[0].role).toBe('system');
      expect(body.messages[0].content).toContain('QuantumSwarm');
      expect(body.messages[1].role).toBe('user');
      expect(body.messages[1].content).toBe('User message');
    });

    it('should handle rate limiting with retry', async () => {
      let callCount = 0;
      (global.fetch as any).mockImplementation(() => {
        callCount++;
        if (callCount === 1) {
          return Promise.reject(new Error('rate limit'));
        }
        return Promise.resolve({
          ok: true,
          json: async () => ({ choices: [{ message: { content: 'Success after retry' } }] }),
        });
      });

      const messages = [{ role: 'user', content: 'Test' }];
      const result = await callAIFree(messages);
      
      expect(result).toContain('Success after retry');
      expect(callCount).toBe(2);
    });
  });

  describe('checkAIFreeHealth', () => {
    it('should return health status for all services', async () => {
      (global.fetch as any).mockImplementation((url: string) => {
        if (url.includes('models.inference.ai.azure.com')) {
          return Promise.resolve({ status: 200, ok: true });
        }
        if (url.includes('huggingface.co')) {
          return Promise.resolve({ status: 200, ok: true });
        }
        if (url.includes('localhost:11434')) {
          return Promise.reject(new Error('Connection refused'));
        }
        return Promise.reject(new Error('Unknown endpoint'));
      });

      const health = await checkAIFreeHealth();
      
      expect(health.github).toEqual({ status: 200, ok: true });
      expect(health.huggingface).toEqual({ status: 200, ok: true });
      expect(health.ollama.status).toBe('error');
    });

    it('should handle service failures gracefully', async () => {
      (global.fetch as any).mockRejectedValue(new Error('Network error'));

      const health = await checkAIFreeHealth();
      
      expect(health.github.status).toBe('error');
      expect(health.huggingface.status).toBe('error');
      expect(health.ollama.status).toBe('error');
    });
  });

  describe('Error Handling', () => {
    it('should return error message when all APIs fail', async () => {
      (global.fetch as any).mockRejectedValue(new Error('API Error'));

      const messages = [{ role: 'user', content: 'Test' }];
      const result = await callAIFree(messages);
      
      expect(result).toContain('❌ AI Engine Error');
      expect(result).toContain('API Error');
      expect(result).toContain('Using fallback response');
    });

    it('should handle malformed messages gracefully', async () => {
      const invalidMessages = [
        { role: 'invalid', content: 'Test' },
        null,
        undefined,
        { role: 'user' }, // missing content
      ];

      (global.fetch as any).mockResolvedValueOnce({
        ok: true,
        json: async () => ({ choices: [{ message: { content: 'Response' } }] }),
      });

      const result = await callAIFree(invalidMessages as any);
      
      expect(result).toBeDefined();
      expect(result.length).toBeGreaterThan(0);
    });
  });
});

// Integration tests
describe('AI Integration Tests', () => {
  it('should handle complete conversation flow', async () => {
    const conversation = [
      { role: 'system', content: 'You are a helpful assistant' },
      { role: 'user', content: 'Hello' },
      { role: 'assistant', content: 'Hi there!' },
      { role: 'user', content: 'How are you?' }
    ];

    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => ({ 
        choices: [{ 
          message: { 
            content: 'I am doing well, thank you for asking!' 
          } 
        }] 
      }),
    });

    const result = await callAIFree(conversation);
    
    expect(result).toContain('doing well');
    expect(result.length).toBeGreaterThan(20);
  });

  it('should maintain conversation context', async () => {
    const contextMessages = [
      { role: 'user', content: 'My name is John' },
      { role: 'assistant', content: 'Nice to meet you, John!' },
      { role: 'user', content: 'What is my name?' }
    ];

    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => ({ 
        choices: [{ 
          message: { 
            content: 'Your name is John, as you mentioned earlier.' 
          } 
        }] 
      }),
    });

    const result = await callAIFree(contextMessages);
    
    expect(result.toLowerCase()).toContain('john');
  });
});