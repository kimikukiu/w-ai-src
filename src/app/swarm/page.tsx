'use client';

import { useState } from 'react';
import { aiAgent } from '@/lib/ai-agent';
import { swarmCoordinator } from '@/lib/swarm-intelligence';

export default function SWARMAgentPage() {
  const [input, setInput] = useState('');
  const [response, setResponse] = useState('');
  const [loading, setLoading] = useState(false);
  const [status, setStatus] = useState<any>(null);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!input.trim()) return;

    setLoading(true);
    try {
      const result = await aiAgent.process(input);
      setResponse(result.message);
    } catch (error) {
      setResponse('Error processing request');
    }
    setLoading(false);
  };

  const handleStatus = () => {
    const swarmStatus = swarmCoordinator.getSwarmStatus();
    setStatus(swarmStatus);
  };

  const capabilities = aiAgent.getCapabilities();

  return (
    <div className="min-h-screen bg-gray-900 text-white p-6">
      <div className="max-w-6xl mx-auto">
        <div className="text-center mb-8">
          <h1 className="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-600 mb-2">
            🚀 SWARM Intelligence Agent
          </h1>
          <p className="text-gray-400">Multi-Agent AI with Self-Repair & Code Generation</p>
        </div>

        <div className="grid lg:grid-cols-3 gap-6">
          <div className="lg:col-span-2">
            <div className="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700 p-6">
              <h2 className="text-xl font-bold mb-4">💬 Chat</h2>

              <form onSubmit={handleSubmit} className="space-y-4">
                <textarea
                  value={input}
                  onChange={(e) => setInput(e.target.value)}
                  placeholder="Ask me anything... (e.g., 'generate a React component' or 'fix this error')"
                  className="w-full h-32 px-4 py-3 bg-gray-900/50 border border-gray-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 resize-none"
                />

                <button
                  type="submit"
                  disabled={loading}
                  className="w-full py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl hover:opacity-90 transition-opacity disabled:opacity-50"
                >
                  {loading ? '⏳ Processing...' : '🚀 Execute'}
                </button>
              </form>

              {response && (
                <div className="mt-6 p-4 bg-gray-900/50 rounded-xl">
                  <h3 className="font-bold mb-2 text-green-400">Response:</h3>
                  <pre className="text-sm text-gray-300 whitespace-pre-wrap">{response}</pre>
                </div>
              )}
            </div>
          </div>

          <div className="space-y-6">
            <div className="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700 p-6">
              <h2 className="text-xl font-bold mb-4">⚙️ Capabilities</h2>
              <div className="space-y-3">
                {capabilities.map((cap) => (
                  <div key={cap.name} className="flex items-center gap-3">
                    <span className={cap.enabled ? 'text-green-400' : 'text-red-400'}>
                      {cap.enabled ? '✅' : '❌'}
                    </span>
                    <span className="text-gray-300">{cap.name}</span>
                  </div>
                ))}
              </div>
            </div>

            <div className="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700 p-6">
              <h2 className="text-xl font-bold mb-4">📊 SWARM Status</h2>
              <button
                onClick={handleStatus}
                className="w-full py-2 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl mb-4"
              >
                Refresh Status
              </button>
              {status && (
                <div className="space-y-2 text-sm">
                  <p>Total Nodes: <span className="text-purple-400">{status.totalNodes}</span></p>
                  <p>Active: <span className="text-green-400">{status.activeNodes}</span></p>
                  <p>Completed: <span className="text-blue-400">{status.completedTasks}</span></p>
                  <p>Pending: <span className="text-yellow-400">{status.pendingTasks}</span></p>
                </div>
              )}
            </div>

            <div className="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700 p-6">
              <h2 className="text-xl font-bold mb-4">🎯 Features</h2>
              <div className="space-y-2 text-sm text-gray-300">
                <p>🔧 Self-Repair - Automatic error fixing</p>
                <p>💻 Code Generation - Write any code</p>
                <p>🔍 Agent Search - Find solutions</p>
                <p>🎨 Media Generation - Images/Video/Audio</p>
                <p>⚡ Workflow Automation</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
