import { describe, it, expect, vi, beforeEach } from 'vitest';
import { NextRequest } from 'next/server';
import { POST } from '../route';

// Mock dependencies
vi.mock('@/lib/config', () => ({
  loadConfig: vi.fn(() => ({
    glm_model: 'gpt-4o-mini',
  })),
}));

vi.mock('@/lib/sync-bus', () => ({
  emitConfigUpdate: vi.fn(),
  emitModelChange: vi.fn(),
  emitEndpointChange: vi.fn(),
  validateApiKey: vi.fn(() => true),
}));

vi.mock('@/lib/subscription-manager', () => ({
  isValidSubscriber: vi.fn(() => ({ valid: true })),
  isOwnerToken: vi.fn(() => false),
  incrementRequests: vi.fn(),
}));

vi.mock('@/lib/ai-engine-free', () => ({
  callAIFree: vi.fn(async (messages, model) => `AI response for: ${messages[messages.length - 1]?.content || 'test'}`),
}));

describe('AI Gateway API', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  describe('POST /api/gateway/ai', () => {
    it('should process chat messages successfully', async () => {
      const request = new NextRequest('http://localhost:3000/api/gateway/ai', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'x-api-key': 'test-api-key',
        },
        body: JSON.stringify({
          messages: [
            { role: 'user', content: 'Hello AI' }
          ],
          model: 'gpt-4o-mini',
        }),
      });

      const response = await POST(request);
      const data = await response.json();

      expect(response.status).toBe(200);
      expect(data.ok).toBe(true);
      expect(data.response).toContain('AI response for');
      expect(data.model).toBe('gpt-4o-mini');
    });

    it('should process simple prompt successfully', async () => {
      const request = new NextRequest('http://localhost:3000/api/gateway/ai', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'x-api-key': 'test-api-key',
        },
        body: JSON.stringify({
          prompt: 'Test prompt',
          model: 'gpt-4o-mini',
        }),
      });

      const response = await POST(request);
      const data = await response.json();

      expect(response.status).toBe(200);
      expect(data.ok).toBe(true);
      expect(data.response).toContain('AI response for');
      expect(data.prompt).toContain('Test prompt');
    });

    it('should require API key or subscription token', async () => {
      const request = new NextRequest('http://localhost:3000/api/gateway/ai', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          prompt: 'Test prompt',
        }),
      });

      const response = await POST(request);
      const data = await response.json();

      expect(response.status).toBe(401);
      expect(data.error).toContain('API key or subscription token required');
    });

    it('should handle subscription tokens', async () => {
      const { isOwnerToken } = await import('@/lib/subscription-manager');
      (isOwnerToken as any).mockReturnValue(true);

      const request = new NextRequest('http://localhost:3000/api/gateway/ai', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'x-subscription-token': 'ADMIN-HERMES-V4',
        },
        body: JSON.stringify({
          prompt: 'Admin test',
        }),
      });

      const response = await POST(request);
      const data = await response.json();

      expect(response.status).toBe(200);
      expect(data.ok).toBe(true);
    });

    it('should handle invalid API keys', async () => {
      const { validateApiKey } = await import('@/lib/sync-bus');
      (validateApiKey as any).mockReturnValue(false);

      const request = new NextRequest('http://localhost:3000/api/gateway/ai', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'x-api-key': 'invalid-key',
        },
        body: JSON.stringify({
          prompt: 'Test prompt',
        }),
      });

      const response = await POST(request);
      const data = await response.json();

      expect(response.status).toBe(403);
      expect(data.error).toContain('Invalid API key');
    });

    it('should handle invalid subscription tokens', async () => {
      const { isValidSubscriber } = await import('@/lib/subscription-manager');
      (isValidSubscriber as any).mockReturnValue({ valid: false, message: 'Invalid subscription' });

      const request = new NextRequest('http://localhost:3000/api/gateway/ai', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'x-subscription-token': 'invalid-token',
        },
        body: JSON.stringify({
          prompt: 'Test prompt',
        }),
      });

      const response = await POST(request);
      const data = await response.json();

      expect(response.status).toBe(403);
      expect(data.error).toContain('Invalid subscription');
    });

    it('should handle missing request body', async () => {
      const request = new NextRequest('http://localhost:3000/api/gateway/ai', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'x-api-key': 'test-api-key',
        },
        body: '',
      });

      const response = await POST(request);
      
      expect(response.status).toBe(400);
    });

    it('should handle rate limiting with retry', async () => {
      const { callAIFree } = await import('@/lib/ai-engine-free');
      let callCount = 0;
      (callAIFree as any).mockImplementation(async () => {
        callCount++;
        if (callCount === 1) {
          throw new Error('rate limit');
        }
        return 'Success after retry';
      });

      const request = new NextRequest('http://localhost:3000/api/gateway/ai', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'x-api-key': 'test-api-key',
        },
        body: JSON.stringify({
          prompt: 'Test prompt',
        }),
      });

      const response = await POST(request);
      const data = await response.json();

      expect(response.status).toBe(200);
      expect(data.response).toBe('Success after retry');
      expect(callCount).toBe(2);
    });

    it('should emit config updates', async () => {
      const { emitConfigUpdate } = await import('@/lib/sync-bus');

      const request = new NextRequest('http://localhost:3000/api/gateway/ai', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'x-api-key': 'test-api-key',
        },
        body: JSON.stringify({
          prompt: 'Test prompt',
          model: 'gpt-4o-mini',
        }),
      });

      await POST(request);

      expect(emitConfigUpdate).toHaveBeenCalledWith('web', { model: 'gpt-4o-mini' });
    });
  });

  describe('Error Handling', () => {
    it('should handle AI engine failures gracefully', async () => {
      const { callAIFree } = await import('@/lib/ai-engine-free');
      (callAIFree as any).mockRejectedValue(new Error('AI Engine Error'));

      const request = new NextRequest('http://localhost:3000/api/gateway/ai', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'x-api-key': 'test-api-key',
        },
        body: JSON.stringify({
          prompt: 'Test prompt',
        }),
      });

      const response = await POST(request);
      const data = await response.json();

      expect(response.status).toBe(200); // Should fallback to error response
      expect(data.response).toContain('AI Engine Error');
    });

    it('should handle malformed JSON', async () => {
      const request = new NextRequest('http://localhost:3000/api/gateway/ai', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'x-api-key': 'test-api-key',
        },
        body: 'invalid json {',
      });

      const response = await POST(request);
      
      expect(response.status).toBe(400);
    });

    it('should handle missing model parameter', async () => {
      const request = new NextRequest('http://localhost:3000/api/gateway/ai', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'x-api-key': 'test-api-key',
        },
        body: JSON.stringify({
          prompt: 'Test prompt',
          // model is missing
        }),
      });

      const response = await POST(request);
      const data = await response.json();

      expect(response.status).toBe(200);
      expect(data.model).toBe('glm-4-plus'); // Default model
    });
  });
});