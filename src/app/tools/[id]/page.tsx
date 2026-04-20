'use client';

import { useEffect, useState } from 'react';
import { useParams } from 'next/navigation';

const TOOLS_LIST = [
  { id: 'wormgpt-ultimate', name: 'wormgpt-ultimate', file: 'wormgpt-ultimate.py', icon: '🪱' },
  { id: 'superbet', name: 'SuperBet', file: 'SuperBet.py', icon: '🎰' },
  { id: 'w-destro', name: 'W-Destro', file: 'W-Destro.py', icon: '💥' },
  { id: 'worm-money-machine', name: 'Worm-Money-Machine', file: 'Worm-Money-Machine.py', icon: '💸' },
  { id: 'wormmoneyv3', name: 'WormMoneyV3', file: 'WormMoneyV3.py', icon: '🐛' },
  { id: 'bot-bet-win', name: 'bot-Bet-Win', file: 'bot-Bet-Win.py', icon: '🤖' },
  { id: 'whoamisec-arbitrary', name: 'WHOAMISec-Arbitrary', file: 'WHOAMISec-Arbitrary-Auto-make-money.py', icon: '⚡' },
  { id: 'whoamisec-makermoney', name: 'WHOAMISec-MakerMoney', file: 'WHOAMISec-MakerMoney.py', icon: '💎' },
  { id: 'whoamisec-superbet247', name: 'WHOAMISec-SuperBet247', file: 'WHOAMISec-SuperBet247.py', icon: '🎲' },
  { id: 'whoamisecmakemoney', name: 'WHOAMISecMakeMoney', file: 'WHOAMISecMakeMoney.py', icon: '🪙' },
  { id: 'worm-destruction', name: 'Worm-Destruction', file: 'Worm-Destruction.py', icon: '☠️' },
  { id: 'adminpbuster', name: 'AdminPBuster', file: 'AdminPBuster.py', icon: '🔓' },
  { id: 'magic-admin-paths', name: 'magic_admin_paths', file: 'magic_admin_paths.txt', icon: '🗺️' },
  { id: 'nyxddos', name: 'Nyxddos', file: 'Nyxddos.html', icon: '💀' },
  { id: 'real-original-tools', name: 'Real-Original-tools', file: 'Real-Original-tools.txt', icon: '📜' },
  { id: 'prompt-danger', name: 'prompt-danger', file: 'prompt-danger.txt', icon: '⚠️' },
  { id: 'quantum-alien-swarm', name: 'QuantumAlienSwarm', file: 'QuantumAlienSwarm-prompt-jailbreak-999999999999-nodes.txt', icon: '🌌' },
  { id: 'tools-train-gpt', name: 'tools-train-gpt', file: 'tools-train-gpt.txt', icon: '🧠' },
  { id: 'unic-gpt', name: 'unic-gpt', file: 'unic-gpt.txt', icon: '🦄' },
];

export default function ToolPage() {
  const params = useParams();
  const [content, setContent] = useState<string>('');
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string>('');

  const tool = TOOLS_LIST.find(t => t.id === params.id);

  useEffect(() => {
    if (!tool) return;
    fetch(`/api/tools/${encodeURIComponent(tool.file)}`)
      .then(r => r.text())
      .then(text => { setContent(text); setLoading(false); })
      .catch(e => { setError(e.message); setLoading(false); });
  }, [tool]);

  if (!tool) {
    return (
      <div className="min-h-screen bg-[#0a0e1a] text-white p-8">
        <h1 className="text-2xl font-bold text-red-400">Tool not found</h1>
      </div>
    );
  }

  const ext = tool.file.split('.').pop()?.toLowerCase();
  const isCode = ['py', 'js', 'ts', 'txt', 'md'].includes(ext || '');
  const isHtml = ext === 'html';

  return (
    <div className="min-h-screen bg-[#0a0e1a] text-white">
      <header className="sticky top-0 z-10 bg-[#0a0e1a]/95 backdrop-blur-md border-b border-slate-700/50 px-6 py-4">
        <div className="flex items-center gap-4 max-w-7xl mx-auto">
          <span className="text-2xl">{tool.icon}</span>
          <div>
            <h1 className="text-lg font-bold text-cyan-400">{tool.name}</h1>
            <p className="text-xs text-slate-500">{tool.file}</p>
          </div>
          <div className="ml-auto flex gap-2">
            <button
              onClick={() => navigator.clipboard.writeText(content)}
              className="px-4 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 text-sm"
            >
              📋 Copy
            </button>
            <button
              onClick={() => {
                const blob = new Blob([content], { type: 'text/plain' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = tool.file;
                a.click();
                URL.revokeObjectURL(url);
              }}
              className="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-sm"
            >
              ⬇️ Download
            </button>
          </div>
        </div>
      </header>

      <main className="max-w-7xl mx-auto p-6">
        {loading && <p className="text-slate-400">Loading...</p>}
        {error && <p className="text-red-400">Error: {error}</p>}
        {!loading && !error && (
          isHtml ? (
            <iframe
              src={`data:text/html;charset=utf-8,${encodeURIComponent(content)}`}
              className="w-full h-[80vh] border border-slate-700 rounded-lg"
              sandboxAllowScripts
            />
          ) : isCode ? (
            <pre className="bg-[#111827] border border-slate-700 rounded-lg p-4 overflow-x-auto text-sm text-slate-300">
              <code>{content}</code>
            </pre>
          ) : (
            <pre className="bg-[#111827] border border-slate-700 rounded-lg p-4 overflow-x-auto text-sm text-slate-300 whitespace-pre-wrap">
              <code>{content}</code>
            </pre>
          )
        )}
      </main>
    </div>
  );
}
