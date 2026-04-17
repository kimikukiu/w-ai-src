'use client';

import React, { useState, useEffect, useCallback, useRef } from 'react';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Separator } from '@/components/ui/separator';
import { toast } from 'sonner';
import {
  Bot, Brain, FolderOpen, Rocket, Settings, FileText, LogOut, Send,
  RefreshCw, Key, Shield, ChevronRight, Terminal, Cpu, Zap,
  Download, Trash2, Copy, ExternalLink, Loader2, CheckCircle2,
  XCircle, AlertCircle, Eye, EyeOff, Monitor, Smartphone,
  Database, GitBranch, Globe, LayoutDashboard, Code, Activity,
  Lock, Unlock, Command, Crown, Star, Users, Sparkles, Play, ChevronDown, X
} from 'lucide-react';
import Image from 'next/image';

// ═══════════════════════════════════════════════
// TYPES
// ═══════════════════════════════════════════════

interface AppConfig {
  glm_api_key: string;
  telegram_token: string;
  glm_model: string;
  glm_endpoint: string;
  github_repo: string;
  auto_repair: string;
  max_repair_iterations: number;
  expert_mode: string;
  glm_api_key_masked?: string;
  telegram_token_masked?: string;
}

interface BotMessage {
  id: number;
  from: string;
  text: string;
  date: string;
}

interface FileInfo {
  name: string;
  size: number;
  modified: string;
}

interface LoopProblem {
  id: number;
  title: string;
  description: string;
  difficulty: string;
  category: string;
  template: string;
  hint?: string;
}

interface LogEntry {
  time: string;
  type: 'info' | 'ok' | 'err' | 'warn';
  message: string;
}

// ═══════════════════════════════════════════════
// SUBSCRIPTION PLAN DATA
// ═══════════════════════════════════════════════

const SUBSCRIPTION_PLANS = [
  {
    id: 'free',
    name: 'Free Demo',
    price: '0',
    currency: 'EUR',
    period: '1 oră',
    requests: 10,
    models: ['glm-4-flash', 'glm-4.6'],
    features: ['10 requesturi demo', 'Acces 1 oră', '2 modele AI', 'Chat de bază', 'Fără fișiere'],
    color: 'from-slate-600 to-slate-500',
    badge: null,
    popular: false,
  },
  {
    id: 'pro',
    name: 'Pro',
    price: '25',
    currency: 'EUR',
    period: 'lună',
    requests: 500,
    models: ['glm-4.6', 'glm-5-turbo', 'hermes-4-70B', 'DeepSeek-3.2', 'kimi-k2.5'],
    features: ['500 requesturi/lună', 'Toate modelele Pro', 'GLM Chat avansat', 'Upload fișiere', 'Bot Telegram complet', 'Loop Coder (13 limbi)', 'Red Team testing', 'Priority support'],
    color: 'from-blue-600 to-cyan-500',
    badge: 'POPULAR',
    popular: true,
  },
  {
    id: 'enterprise',
    name: 'Enterprise',
    price: '75',
    currency: 'EUR',
    period: 'lună',
    requests: -1,
    models: ['ALL MODELS', 'queen-ultra', 'queen-max', 'hermes-4-405B', 'gpt-5.4-pro', 'claude-opus-4-6', 'gemini-3.0-pro-preview'],
    features: ['Requesturi nelimitate', 'TOATE modelele (19+)', 'Queen Ultra + Max', 'Expert Mode ON', 'Codespace Builder IDE', 'API access direct', 'Custom training', 'Codespace Tools', '24/7 Priority support', 'Admin management'],
    color: 'from-purple-600 to-pink-500',
    badge: 'MAX',
    popular: false,
  },
];

const CODE_DEMO_SLIDES = [
  { lang: 'Python', icon: '🐍', code: 'from hermes import QuantumSwarm\n\nagent = QuantumSwarm(model="queen-ultra")\nresult = agent.analyze(\n    target="web_app",\n    depth="deep_scan",\n    reasoning=True\n)\nprint(result.summary)\n# >> Vulnerability Score: 0.03\n# >> Security: A+ Grade', output: '✅ QuantumSwarm init...\n🔍 Deep scanning...\n📊 Analyzing 847 endpoints\n🛡️ Security Grade: A+\n⚡ Done in 2.3s' },
  { lang: 'TypeScript', icon: '⚡', code: 'interface ExploitChain {\n  vector: string;\n  severity: Critical | High | Medium;\n  payload: () => Promise<Shell>;\n}\n\nconst scanner = new HermesScanner({\n  models: ["hermes-4-405B", "DeepSeek-3.2"],\n  parallel: true,\n  quantumSwarm: true\n});\nawait scanner.runFullAudit();', output: '🚀 HermesScanner init...\n🔍 Loading 2 models...\n📡 Parallel scan active...\n✅ 0 critical, 0 high, 2 medium\n📋 Report generated' },
  { lang: 'Rust', icon: '🦀', code: 'use hermes::{Agent, Model, Tool};\n\n#[hermes::main]\nasync fn main() {\n    let agent = Agent::builder()\n        .model(Model::QueenUltra)\n        .tool(Tool::Browser)\n        .tool(Tool::Terminal)\n        .tool(Tool::CodeEditor)\n        .build()?;\n    \n    let result = agent.execute(\n        "Build secure API gateway"\n    ).await?;\n    println!("{}", result.code);\n}', output: '🏗️ Building agent...\n📎 Tools: Browser, Terminal, CodeEditor\n🧠 Model: Queen Ultra\n💻 Generating secure API...\n✅ Build complete (1.2s)' },
];

// ═══════════════════════════════════════════════
// LANDING PAGE COMPONENT (PUBLIC VIEW)
// ═══════════════════════════════════════════════

function LandingPage({ onAdminClick }: { onAdminClick: () => void }) {
  const [currentSlide, setCurrentSlide] = useState(0);
  const [subscriberToken, setSubscriberToken] = useState('');
  const [subLoginError, setSubLoginError] = useState('');
  const [subLoading, setSubLoading] = useState(false);
  const [demoLoading, setDemoLoading] = useState(false);
  const [showTos, setShowTos] = useState(false);
  const [showPlans, setShowPlans] = useState(false);

  useEffect(() => {
    const interval = setInterval(() => {
      setCurrentSlide(prev => (prev + 1) % CODE_DEMO_SLIDES.length);
    }, 4000);
    return () => clearInterval(interval);
  }, []);

  // Auto-login from URL token
  useEffect(() => {
    const params = new URLSearchParams(window.location.search);
    const token = params.get('token');
    if (token) {
      setSubscriberToken(token);
      handleSubLogin(token);
    }
  }, []);

  const handleSubLogin = async (tkn?: string) => {
    const token = tkn || subscriberToken;
    if (!token) return;
    setSubLoading(true);
    setSubLoginError('');
    try {
      const res = await fetch('/api/subscribe/status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ token }),
      });
      const data = await res.json();
      if (data.valid) {
        localStorage.setItem('wsec_token', token);
        localStorage.setItem('wsec_role', data.role || 'subscriber');
        window.location.href = '/?authenticated=true';
      } else {
        setSubLoginError(data.error || 'Token invalid sau expirat');
      }
    } catch {
      setSubLoginError('Eroare de conexiune');
    }
    setSubLoading(false);
  };

  const handleDemoRegister = async () => {
    setDemoLoading(true);
    try {
      const res = await fetch('/api/subscribe/register', { method: 'POST' });
      const data = await res.json();
      if (data.token) {
        toast.success('Demo activat! Redirecting...');
        window.location.href = '/?token=' + data.token;
      } else {
        toast.error(data.error || 'Eroare la înregistrare demo');
      }
    } catch {
      toast.error('Eroare de rețea');
    }
    setDemoLoading(false);
  };

  const slide = CODE_DEMO_SLIDES[currentSlide];

  return (
    <div className="min-h-screen bg-[#0a0e1a] text-slate-100 relative overflow-hidden">
      {/* Animated Background */}
      <div className="absolute inset-0 overflow-hidden pointer-events-none">
        {Array.from({ length: 50 }).map((_, i) => (
          <div
            key={i}
            className="absolute rounded-full"
            style={{
              left: `${Math.random() * 100}%`,
              top: `${Math.random() * 100}%`,
              width: `${1 + Math.random() * 2}px`,
              height: `${1 + Math.random() * 2}px`,
              background: i % 3 === 0 ? 'rgba(99,102,241,0.3)' : i % 3 === 1 ? 'rgba(6,182,212,0.2)' : 'rgba(168,85,247,0.2)',
              animation: `floatParticle ${4 + Math.random() * 8}s ease-in-out infinite`,
              animationDelay: `${Math.random() * 10}s`,
            }}
          />
        ))}
      </div>
      <style jsx>{`
        @keyframes floatParticle {
          0%, 100% { transform: translateY(0) translateX(0); opacity: 0.2; }
          50% { transform: translateY(-80px) translateX(40px); opacity: 0.6; }
        }
        @keyframes pulse-glow {
          0%, 100% { box-shadow: 0 0 20px rgba(99,102,241,0.3); }
          50% { box-shadow: 0 0 40px rgba(99,102,241,0.6); }
        }
        @keyframes slideIn {
          from { opacity: 0; transform: translateY(20px); }
          to { opacity: 1; transform: translateY(0); }
        }
        @keyframes typeCursor {
          0%, 100% { opacity: 1; }
          50% { opacity: 0; }
        }
      `}</style>

      {/* Navigation Bar */}
      <nav className="relative z-10 border-b border-slate-800/50 bg-[#0a0e1a]/80 backdrop-blur-md">
        <div className="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <Image src="/whoamisec-logo.jpg" alt="WHOAMISec AI" width={42} height={42} className="rounded-lg" />
            <div>
              <h1 className="text-lg font-bold bg-gradient-to-r from-indigo-400 via-cyan-400 to-purple-400 bg-clip-text text-transparent">WHOAMISec AI</h1>
              <p className="text-[10px] text-slate-500">WHOAMISec AI · QuantumSwarm Intelligence</p>
            </div>
          </div>
          <div className="flex items-center gap-3">
            <a href="https://t.me/whoamisecai" target="_blank" className="text-xs text-slate-400 hover:text-cyan-400 transition-colors hidden sm:block">📡 Canal</a>
            <a href="https://t.me/idkebowbot" target="_blank" className="text-xs text-slate-400 hover:text-cyan-400 transition-colors hidden sm:block">🤖 Bot</a>
            <button onClick={() => setShowTos(true)} className="text-xs text-slate-500 hover:text-slate-300 transition-colors">TOS</button>
            <button onClick={() => setShowPlans(true)} className="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-cyan-500 text-white text-xs font-semibold hover:from-blue-500 hover:to-cyan-400 transition-all">
              Abonamente
            </button>
          </div>
        </div>
      </nav>

      {/* Hero Section */}
      <section className="relative z-10 max-w-7xl mx-auto px-6 pt-16 pb-12">
        <div className="grid lg:grid-cols-2 gap-12 items-center">
          <div style={{ animation: 'slideIn 0.6s ease-out' }}>
            <Badge className="mb-4 bg-indigo-500/20 text-indigo-300 border-indigo-500/30 text-xs px-3 py-1">
              <Sparkles className="h-3 w-3 mr-1" /> QuantumSwarm Intelligence Engine
            </Badge>
            <h2 className="text-4xl md:text-5xl font-extrabold leading-tight mb-6">
              <span className="bg-gradient-to-r from-indigo-400 via-cyan-300 to-purple-400 bg-clip-text text-transparent">AI Agentic Coder</span>
              <br />
              <span className="text-slate-200">cu 19+ Modele</span>
            </h2>
            <p className="text-slate-400 text-base mb-8 leading-relaxed max-w-lg">
              WHOAMISec AI — platformă AI completă cu coding agentic, analiză securitate, Loop Coder în 13 limbaje,
              Red Team testing, QuantumSwarm training și acces la cele mai avansate modele AI din lume.
            </p>
            <div className="flex flex-wrap gap-3 mb-8">
              <button onClick={handleDemoRegister} disabled={demoLoading} className="px-6 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-cyan-500 text-white font-semibold hover:from-indigo-500 hover:to-cyan-400 transition-all flex items-center gap-2">
                <Play className="h-4 w-4" /> {demoLoading ? 'Se încarcă...' : 'Încearcă Demo Gratis'}
              </button>
              <a href="https://t.me/idkebowbot" target="_blank" className="px-6 py-3 rounded-xl border border-slate-700 text-slate-300 hover:bg-slate-800 transition-all flex items-center gap-2">
                <Bot className="h-4 w-4" /> Deschide Bot Telegram
              </a>
            </div>
            <div className="flex flex-wrap gap-4 text-xs text-slate-500">
              <span className="flex items-center gap-1"><CheckCircle2 className="h-3.5 w-3.5 text-emerald-400" /> 10 Requesturi Demo</span>
              <span className="flex items-center gap-1"><CheckCircle2 className="h-3.5 w-3.5 text-emerald-400" /> Fără card</span>
              <span className="flex items-center gap-1"><CheckCircle2 className="h-3.5 w-3.5 text-emerald-400" /> Activare instantă</span>
            </div>
          </div>

          {/* Code Demo Slideshow */}
          <div className="relative" style={{ animation: 'slideIn 0.8s ease-out' }}>
            <div className="rounded-2xl bg-[#111827] border border-slate-700/50 overflow-hidden shadow-2xl shadow-indigo-500/10" style={{ animation: 'pulse-glow 4s ease-in-out infinite' }}>
              <div className="flex items-center gap-2 px-4 py-3 bg-[#0a0e1a] border-b border-slate-800">
                <div className="flex gap-1.5">
                  <div className="w-3 h-3 rounded-full bg-red-500/70" />
                  <div className="w-3 h-3 rounded-full bg-yellow-500/70" />
                  <div className="w-3 h-3 rounded-full bg-green-500/70" />
                </div>
                <span className="text-xs text-slate-500 ml-2">{slide.icon} {slide.lang} — WHOAMISec Agent</span>
                <div className="ml-auto flex gap-1">
                  {CODE_DEMO_SLIDES.map((_, i) => (
                    <button key={i} onClick={() => setCurrentSlide(i)} className={`w-2 h-2 rounded-full transition-all ${i === currentSlide ? 'bg-indigo-400 w-6' : 'bg-slate-600 hover:bg-slate-500'}`} />
                  ))}
                </div>
              </div>
              <div className="p-4 font-mono text-xs leading-relaxed">
                <pre className="text-emerald-300 whitespace-pre-wrap mb-3">{slide.code}</pre>
                <div className="border-t border-slate-800 pt-3 mt-3">
                  <pre className="text-cyan-300/70 whitespace-pre-wrap">{slide.output}</pre>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Features Grid */}
      <section className="relative z-10 max-w-7xl mx-auto px-6 py-12">
        <h3 className="text-2xl font-bold text-center mb-8">
          <span className="bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">Capabilități Complete</span>
        </h3>
        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
          {[
            { icon: <Brain className="h-6 w-6" />, title: '19+ Modele AI', desc: 'Queen Ultra, GPT-5.4, Claude Opus 4, DeepSeek 3.2, GLM-5 și multe altele', color: 'from-blue-500/20 to-indigo-500/20', border: 'border-blue-500/30' },
            { icon: <Code className="h-6 w-6" />, title: 'Agentic Coder', desc: 'Coding complet cu QuantumSwarm, Loop Coder în 13 limbaje, auto-repair', color: 'from-emerald-500/20 to-cyan-500/20', border: 'border-emerald-500/30' },
            { icon: <Shield className="h-6 w-6" />, title: 'Red Team Testing', desc: 'Jailbreak testing, adversarial reasoning, SQL injection, vulnerability scanning', color: 'from-red-500/20 to-orange-500/20', border: 'border-red-500/30' },
            { icon: <Terminal className="h-6 w-6" />, title: 'Bot Telegram', desc: 'Comenzi complete, fișiere, deploy, training, loop coding, totul din Telegram', color: 'from-purple-500/20 to-pink-500/20', border: 'border-purple-500/30' },
          ].map((f, i) => (
            <div key={i} className={`rounded-xl bg-gradient-to-br ${f.color} border ${f.border} p-5 hover:scale-[1.02] transition-transform`}>
              <div className="text-indigo-300 mb-3">{f.icon}</div>
              <h4 className="font-bold text-sm mb-1">{f.title}</h4>
              <p className="text-slate-400 text-xs leading-relaxed">{f.desc}</p>
            </div>
          ))}
        </div>
      </section>

      {/* Subscription Plans */}
      <section className="relative z-10 max-w-7xl mx-auto px-6 py-12">
        <h3 className="text-2xl font-bold text-center mb-3">
          <span className="bg-gradient-to-r from-cyan-400 to-indigo-400 bg-clip-text text-transparent">Planuri de Abonament</span>
        </h3>
        <p className="text-slate-500 text-sm text-center mb-8">Alege planul potrivit pentru tine. Plată în XMR sau USDT(TON).</p>
        <div className="grid md:grid-cols-3 gap-6">
          {SUBSCRIPTION_PLANS.map(plan => (
            <div key={plan.id} className={`relative rounded-2xl bg-gradient-to-b ${plan.color} p-[1px] ${plan.popular ? 'md:-mt-4 md:mb-[-16px] scale-105 z-10' : ''}`}>
              {plan.badge && (
                <div className="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-0.5 rounded-full bg-gradient-to-r from-blue-500 to-cyan-500 text-white text-[10px] font-bold">
                  {plan.badge}
                </div>
              )}
              <div className="rounded-2xl bg-[#111827] p-6 h-full">
                <h4 className="text-xl font-bold mb-1">{plan.name}</h4>
                <div className="flex items-baseline gap-1 mb-4">
                  <span className="text-3xl font-extrabold">{plan.price === '0' ? 'GRATIS' : `${plan.price}`}</span>
                  {plan.price !== '0' && <span className="text-slate-500 text-sm">{plan.currency}/{plan.period}</span>}
                </div>
                <div className="text-xs text-slate-400 mb-4">
                  {plan.requests === -1 ? '∞ Requesturi' : `${plan.requests} requesturi`} · {plan.models.length} modele
                </div>
                <ul className="space-y-2 mb-6">
                  {plan.features.map((f, i) => (
                    <li key={i} className="flex items-start gap-2 text-xs text-slate-300">
                      <CheckCircle2 className="h-3.5 w-3.5 text-emerald-400 mt-0.5 shrink-0" /> {f}
                    </li>
                  ))}
                </ul>
                <button className={`w-full py-2.5 rounded-lg font-semibold text-sm transition-all ${plan.popular ? 'bg-gradient-to-r from-blue-600 to-cyan-500 text-white hover:from-blue-500 hover:to-cyan-400' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'}`}>
                  {plan.id === 'free' ? 'Demo Gratis' : 'Contactează-ne'}
                </button>
                {plan.id !== 'free' && (
                  <p className="text-center text-[10px] text-slate-600 mt-2">XMR / USDT(TON) · t.me/loghandelbot</p>
                )}
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* Subscriber Login Section */}
      <section className="relative z-10 max-w-md mx-auto px-6 py-12">
        <div className="rounded-2xl bg-[#111827] border border-slate-700/50 p-6">
          <div className="text-center mb-4">
            <Key className="h-6 w-6 text-cyan-400 mx-auto mb-2" />
            <h4 className="font-bold">Ai deja token de acces?</h4>
            <p className="text-xs text-slate-500 mt-1">Introdu token-ul WSEC pentru a accesa platforma</p>
          </div>
          {subLoginError && (
            <div className="bg-red-950/50 border border-red-900/50 text-red-300 p-2 rounded-lg mb-3 text-xs">{subLoginError}</div>
          )}
          <div className="flex gap-2">
            <Input value={subscriberToken} onChange={e => setSubscriberToken(e.target.value)} placeholder="WSEC-XXXX-XXXX-XXXX" className="bg-[#0a0e1a] border-slate-600 text-slate-100 flex-1 text-sm" onKeyDown={e => { if (e.key === 'Enter') handleSubLogin(); }} />
            <Button onClick={() => handleSubLogin()} disabled={subLoading} className="bg-cyan-600 hover:bg-cyan-500 px-4">
              {subLoading ? <Loader2 className="h-4 w-4 animate-spin" /> : <Unlock className="h-4 w-4" />}
            </Button>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="relative z-10 border-t border-slate-800/50 py-6">
        <div className="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
          <div className="flex items-center gap-2 text-xs text-slate-600">
            <Image src="/whoamisec-logo.jpg" alt="" width={20} height={20} className="rounded" />
            WHOAMISec AI · v4.0
          </div>
          <div className="flex items-center gap-4 text-xs text-slate-600">
            <a href="https://t.me/whoamisecai" target="_blank" className="hover:text-slate-400">Canal</a>
            <a href="https://t.me/idkebowbot" target="_blank" className="hover:text-slate-400">Bot</a>
            <a href="https://t.me/loghandelbot" target="_blank" className="hover:text-slate-400">Contact</a>
          </div>
        </div>
      </footer>

      {/* Admin Floating Button (40% opacity, bottom-right corner) */}
      <button
        onClick={onAdminClick}
        className="fixed bottom-6 right-6 z-50 w-12 h-12 rounded-full bg-slate-800/40 hover:bg-slate-700/60 border border-slate-600/30 flex items-center justify-center transition-all hover:scale-110 group"
        title="Admin"
      >
        <Lock className="h-4 w-4 text-slate-500 group-hover:text-slate-300" />
      </button>

      {/* TOS Modal */}
      {showTos && (
        <div className="fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4" onClick={() => setShowTos(false)}>
          <div className="bg-[#111827] border border-slate-700/50 rounded-2xl max-w-lg w-full max-h-[80vh] overflow-y-auto p-6" onClick={e => e.stopPropagation()}>
            <div className="flex items-center justify-between mb-4">
              <h4 className="font-bold text-lg">Termeni și Condiții</h4>
              <button onClick={() => setShowTos(false)} className="text-slate-500 hover:text-white"><X className="h-5 w-5" /></button>
            </div>
            <div className="text-sm text-slate-400 space-y-3 leading-relaxed">
              <p><strong className="text-slate-200">1. Utilizare:</strong> Platforma WHOAMISec AI este destinată utilizării profesionale în scopuri de dezvoltare software, analiză de securitate și cercetare. Orice utilizare abuzivă este strict interzisă.</p>
              <p><strong className="text-slate-200">2. Token-uri:</strong> Token-urile WSEC sunt personale și netransferabile. Un token expirat nu poate fi reactivat. Demo-ul oferă 10 requesturi valabile 1 oră.</p>
              <p><strong className="text-slate-200">3. Plată:</strong> Abonamentele se plătesc în XMR sau USDT(TON). Contact: t.me/loghandelbot. Nu există rambursări după activare.</p>
              <p><strong className="text-slate-200">4. Responsabilitate:</strong> Utilizatorul este singurul responsabil pentru conținutul generat și acțiunile efectuate prin platformă. WHOAMISec AI nu garantează acuratețea rezultatelor.</p>
              <p><strong className="text-slate-200">5. Confidențialitate:</strong> Nu stocăm date personale dincolo de token și istoricul de utilizare. Nu vindem date terților.</p>
              <p><strong className="text-slate-200">6. Modificări:</strong> Ne rezervăm dreptul de a modifica prețurile și funcționalitățile fără notificare prealabilă.</p>
            </div>
          </div>
        </div>
      )}

      {/* Plans Modal */}
      {showPlans && (
        <div className="fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4" onClick={() => setShowPlans(false)}>
          <div className="bg-[#111827] border border-slate-700/50 rounded-2xl max-w-2xl w-full max-h-[80vh] overflow-y-auto p-6" onClick={e => e.stopPropagation()}>
            <div className="flex items-center justify-between mb-4">
              <h4 className="font-bold text-lg">Plată și Contact</h4>
              <button onClick={() => setShowPlans(false)} className="text-slate-500 hover:text-white"><X className="h-5 w-5" /></button>
            </div>
            <div className="space-y-4 text-sm text-slate-300">
              <div className="rounded-xl bg-[#0a0e1a] p-4">
                <h5 className="font-bold text-amber-400 mb-1">Monero (XMR)</h5>
                <code className="text-xs text-slate-400 break-all">8BbApiMBHsPVKkLEP4rVbST6CnSb3LW2gXygngCi5MGiBuwAFh6bFEzT3UTuFCkLHtyHnrYNnHycdaGb2Kgkkmw8jViCdB6</code>
              </div>
              <div className="rounded-xl bg-[#0a0e1a] p-4">
                <h5 className="font-bold text-blue-400 mb-1">USDT (TON)</h5>
                <code className="text-xs text-slate-400 break-all">UQB652W7D6OQwI7mmkiBNzguViY7or3fVORRdjNOigeeafjk</code>
              </div>
              <p className="text-center text-slate-500 text-xs">După plată, contactează <a href="https://t.me/loghandelbot" target="_blank" className="text-cyan-400 hover:underline">t.me/loghandelbot</a> pentru a primi token-ul de acces.</p>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

// ═══════════════════════════════════════════════
// ADMIN LOGIN MODAL (POPUP)
// ═══════════════════════════════════════════════

function AdminLoginModal({ onSuccess, onClose }: { onSuccess: () => void; onClose: () => void }) {
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [loginError, setLoginError] = useState('');
  const [loading, setLoading] = useState(false);

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoginError('');
    setLoading(true);
    const res = await fetch('/api/auth/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ password }),
    });
    const data = await res.json();
    setLoading(false);
    if (data.success) {
      onSuccess();
    } else {
      setLoginError(data.error || 'Parolă invalidă');
    }
  };

  return (
    <div className="fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4" onClick={onClose}>
      <div className="absolute inset-0 overflow-hidden pointer-events-none">
        {Array.from({ length: 20 }).map((_, i) => (
          <div key={i} className="absolute rounded-full bg-blue-500/20" style={{ left: `${Math.random()*100}%`, top: `${Math.random()*100}%`, width: '2px', height: '2px', animation: `floatParticle ${5+Math.random()*6}s ease-in-out infinite`, animationDelay: `${Math.random()*8}s` }} />
        ))}
      </div>
      <Card className="w-full max-w-md bg-gradient-to-br from-[#111827] to-[#1a1f35] border-slate-700/50" onClick={e => e.stopPropagation()}>
        <CardContent className="pt-8 pb-8 px-8">
          <div className="text-center mb-8">
            <Image src="/whoamisec-logo.jpg" alt="WHOAMISec" width={60} height={60} className="rounded-xl mx-auto mb-3" />
            <h1 className="text-2xl font-bold bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">WHOAMISec Admin</h1>
            <p className="text-slate-500 text-sm mt-1">Admin Control Panel v4.0</p>
          </div>
          {loginError && (
            <div className="bg-red-950/50 border border-red-900/50 text-red-300 p-3 rounded-lg mb-5 text-sm">{loginError}</div>
          )}
          <form onSubmit={handleLogin} className="space-y-5">
            <div>
              <label className="text-slate-400 text-sm font-medium mb-2 block">
                <Lock className="h-3.5 w-3.5 inline mr-1.5" /> Admin Password
              </label>
              <div className="relative">
                <Input type={showPassword ? 'text' : 'password'} value={password} onChange={e => setPassword(e.target.value)} placeholder="Enter admin password" autoFocus className="bg-[#0f172a] border-slate-600 text-slate-100 h-12 pr-10" />
                <button type="button" onClick={() => setShowPassword(!showPassword)} className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300">
                  {showPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                </button>
              </div>
            </div>
            <div className="flex gap-2">
              <Button type="button" onClick={onClose} variant="outline" className="flex-1 h-12 border-slate-600 text-slate-400 hover:bg-slate-800">Înapoi</Button>
              <Button type="submit" disabled={loading} className="flex-1 h-12 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white font-semibold">
                {loading ? <Loader2 className="h-4 w-4 animate-spin" /> : 'Access Dashboard'}
              </Button>
            </div>
          </form>
          <p className="text-center text-slate-600 text-xs mt-6">WHOAMISec AI · Expert Edition · 24/7 Online</p>
        </CardContent>
      </Card>
    </div>
  );
}

// ═══════════════════════════════════════════════
// MAIN PAGE COMPONENT
// ═══════════════════════════════════════════════

export default function HermesPage() {
  const [authenticated, setAuthenticated] = useState(false);
  const [loading, setLoading] = useState(true);
  const [showAdminLogin, setShowAdminLogin] = useState(false);

  useEffect(() => {
    fetch('/api/auth/check').then(r => r.json()).then(data => {
      setAuthenticated(data.authenticated === true);
      setLoading(false);
    }).catch(() => setLoading(false));

    // Check subscriber token in localStorage
    const subToken = localStorage.getItem('wsec_token');
    if (subToken) {
      fetch('/api/subscribe/status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ token: subToken }),
      }).then(r => r.json()).then(data => {
        if (data.valid) {
          setAuthenticated(true);
        }
        setLoading(false);
      }).catch(() => setLoading(false));
    }
  }, []);

  const handleAdminLogin = () => {
    setShowAdminLogin(true);
  };

  const handleAdminSuccess = () => {
    setAuthenticated(true);
    setShowAdminLogin(false);
    toast.success('Autentificat ca Admin!');
  };

  const handleLogout = async () => {
    await fetch('/api/auth/logout', { method: 'POST' });
    localStorage.removeItem('wsec_token');
    localStorage.removeItem('wsec_role');
    setAuthenticated(false);
    toast.info('Deconectat');
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-[#0a0e1a] flex items-center justify-center">
        <div className="text-center">
          <Loader2 className="h-10 w-10 text-blue-500 animate-spin mx-auto mb-4" />
          <p className="text-slate-400 text-sm">Se încarcă WHOAMISec AI...</p>
        </div>
      </div>
    );
  }

  if (!authenticated) {
    return <LandingPage onAdminClick={handleAdminLogin} />;
  }

  if (showAdminLogin) {
    return <AdminLoginModal onSuccess={handleAdminSuccess} onClose={() => setShowAdminLogin(false)} />;
  }

  return <Dashboard onLogout={handleLogout} />;
}

// ═══════════════════════════════════════════════
// DASHBOARD COMPONENT
// ═══════════════════════════════════════════════

function Dashboard({ onLogout }: { onLogout: () => void }) {
  const [activeSection, setActiveSection] = useState('overview');
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const [config, setConfig] = useState<AppConfig | null>(null);
  const [deployStatus, setDeployStatus] = useState<any>(null);
  const [logs, setLogs] = useState<LogEntry[]>([]);
  const [botMessages, setBotMessages] = useState<BotMessage[]>([]);
  const [glmMessages, setGlmMessages] = useState<{ role: string; content: string }[]>([
    { role: 'system', content: 'GLM Ready. Type any prompt - code, analysis, security, etc.' }
  ]);
  const [files, setFiles] = useState<{ downloads: FileInfo[]; generated: FileInfo[] }>({
    downloads: [],
    generated: [],
  });
  const [loopProblems, setLoopProblems] = useState<LoopProblem[]>([]);
  const [selectedProblem, setSelectedProblem] = useState<LoopProblem | null>(null);

  // Settings form state
  const [telegramToken, setTelegramToken] = useState('');
  const [glmKey, setGlmKey] = useState('');
  const [glmModel, setGlmModel] = useState('glm-4.6');
  const [glmEndpoint, setGlmEndpoint] = useState('https://api.z.ai/api/coding/paas/v4/chat/completions');
  const [githubRepo, setGithubRepo] = useState('');
  const [autoRepair, setAutoRepair] = useState('true');
  const [maxRepair, setMaxRepair] = useState(3);
  const [expertMode, setExpertMode] = useState('false');

  // Loading states
  const [botLoading, setBotLoading] = useState(false);
  const [glmLoading, setGlmLoading] = useState(false);
  const [botActive, setBotActive] = useState(false);
  const [botSetupLoading, setBotSetupLoading] = useState(false);
  const [pollCount, setPollCount] = useState(0);

  // Agent capabilities
  const [agentReasoning, setAgentReasoning] = useState(true);
  const [agentMemory, setAgentMemory] = useState(false);
  const [agentCots, setAgentCots] = useState(true);

  const glmEndRef = useRef<HTMLDivElement>(null);
  const logEndRef = useRef<HTMLDivElement>(null);
  const pollIntervalRef = useRef<NodeJS.Timeout | null>(null);

  const addLog = useCallback((type: LogEntry['type'], message: string) => {
    setLogs(prev => {
      const newLogs = [...prev, { time: new Date().toLocaleTimeString(), type, message }];
      return newLogs.slice(-100);
    });
  }, []);

  const refreshConfig = useCallback(async () => {
    try {
      const res = await fetch('/api/config');
      const data = await res.json();
      setConfig(data);
      if (data.glm_model) setGlmModel(data.glm_model);
      if (data.glm_endpoint) setGlmEndpoint(data.glm_endpoint);
      if (data.auto_repair !== undefined) setAutoRepair(String(data.auto_repair));
      if (data.max_repair_iterations) setMaxRepair(data.max_repair_iterations);
      if (data.expert_mode !== undefined) setExpertMode(String(data.expert_mode));
      if (data.github_repo) setGithubRepo(data.github_repo);
    } catch {}
  }, []);

  const refreshMessages = useCallback(async () => {
    try {
      const res = await fetch('/api/bot/messages');
      const data = await res.json();
      if (data.messages) setBotMessages(data.messages);
    } catch {}
  }, []);

  const refreshFiles = useCallback(async () => {
    try {
      const res = await fetch('/api/files');
      const data = await res.json();
      setFiles({
        downloads: Array.isArray(data.downloads) ? data.downloads : data.downloads?.files || [],
        generated: Array.isArray(data.generated) ? data.generated : data.generated_code?.files || [],
      });
    } catch {}
  }, []);

  const refreshDeployStatus = useCallback(async () => {
    try {
      const res = await fetch('/api/deploy/status');
      const data = await res.json();
      setDeployStatus(data);
    } catch {}
  }, []);

  const refreshLoopProblems = useCallback(async () => {
    try {
      const res = await fetch('/api/loop-problems');
      const data = await res.json();
      if (data.problems) setLoopProblems(data.problems);
    } catch {}
  }, []);

  // Initial load
  useEffect(() => {
    refreshConfig();
    refreshMessages();
    refreshFiles();
    refreshDeployStatus();
    refreshLoopProblems();
    addLog('info', 'Dashboard loaded');
  }, [refreshConfig, refreshMessages, refreshFiles, refreshDeployStatus, refreshLoopProblems, addLog]);

  // Auto-refresh messages
  useEffect(() => {
    const interval = setInterval(refreshMessages, 15000);
    return () => clearInterval(interval);
  }, [refreshMessages]);

  // Auto-scroll GLM chat
  useEffect(() => {
    glmEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [glmMessages]);

  // Auto-scroll logs
  useEffect(() => {
    logEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [logs]);

  // ─── Start / Stop Bot ───
  const startBot = async () => {
    setBotSetupLoading(true);
    addLog('info', 'Starting bot...');
    try {
      // Setup bot commands and delete webhook (switch to polling)
      const setupRes = await fetch('/api/telegram/setup', { method: 'POST' });
      const setupData = await setupRes.json();
      if (setupData.success || setupData.mode === 'polling') {
        setBotActive(true);
        addLog('ok', `Bot activated (${setupData.mode || 'polling'} mode). Commands registered.`);
        toast.success('Bot activat! Trimite /start pe Telegram.');
      } else {
        addLog('err', setupData.error || 'Setup failed');
        toast.error(setupData.error || 'Eroare la activare');
      }
    } catch (e: any) {
      addLog('err', e.message);
      toast.error('Eroare de rețea');
    }
    setBotSetupLoading(false);
  };

  const stopBot = async () => {
    if (pollIntervalRef.current) {
      clearInterval(pollIntervalRef.current);
      pollIntervalRef.current = null;
    }
    setBotActive(false);
    setPollCount(0);
    addLog('warn', 'Bot stopped');
    toast.info('Bot oprit');
  };

  // ─── Bot Polling ───
  useEffect(() => {
    if (botActive) {
      const poll = async () => {
        try {
          const res = await fetch('/api/telegram/poll', { method: 'POST' });
          const data = await res.json();
          if (data.success && data.processed > 0) {
            setPollCount(prev => prev + data.processed);
            addLog('info', `Polled ${data.processed} update(s)`);
            refreshMessages();
          }
        } catch {}
      };
      // Poll every 3 seconds
      poll();
      pollIntervalRef.current = setInterval(poll, 3000);
    }
    return () => {
      if (pollIntervalRef.current) {
        clearInterval(pollIntervalRef.current);
        pollIntervalRef.current = null;
      }
    };
  }, [botActive]);

  // ─── Bot Commands ───
  const sendBotCommand = async (cmd?: string) => {
    const input = document.getElementById('botCmdInput') as HTMLInputElement;
    const command = cmd || input?.value?.trim();
    if (!command) return;
    if (input) input.value = '';
    setBotLoading(true);
    addLog('info', `Bot cmd: ${command.slice(0, 80)}`);
    try {
      const res = await fetch('/api/bot/command', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ command }),
      });
      const data = await res.json();
      if (data.ok) {
        toast.success('Command sent!');
        addLog('ok', `Sent: ${command}`);
      } else {
        toast.error(data.error || 'Failed to send');
        addLog('err', data.error || 'Failed');
      }
    } catch (e: any) {
      toast.error('Network error');
      addLog('err', e.message);
    }
    setBotLoading(false);
    setTimeout(refreshMessages, 2000);
  };

  // ─── GLM Chat ───
  const sendGLM = async () => {
    const input = document.getElementById('glmInput') as HTMLInputElement;
    const msg = input?.value?.trim();
    if (!msg) return;
    if (input) input.value = '';
    setGlmMessages(prev => [...prev, { role: 'user', content: msg }]);
    setGlmLoading(true);
    addLog('info', `GLM: ${msg.slice(0, 60)}`);
    try {
      const res = await fetch('/api/glm/chat', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ prompt: msg, model: glmModel, reasoning: agentReasoning, memory: agentMemory, cots: agentCots }),
      });
      const data = await res.json();
      if (data.response) {
        setGlmMessages(prev => [...prev, { role: 'assistant', content: data.response }]);
        addLog('ok', 'GLM response received');
      } else {
        setGlmMessages(prev => [...prev, { role: 'assistant', content: `Error: ${data.error || 'Unknown error'}` }]);
        addLog('err', `GLM error: ${data.error || ''}`);
      }
    } catch (e: any) {
      setGlmMessages(prev => [...prev, { role: 'assistant', content: `Error: ${e.message}` }]);
      addLog('err', e.message);
    }
    setGlmLoading(false);
  };

  // ─── Config Save ───
  const saveConfig = async (updates: Record<string, any>) => {
    try {
      const res = await fetch('/api/config', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(updates),
      });
      const data = await res.json();
      if (data.ok !== false) {
        toast.success('Saved!');
        addLog('ok', 'Config updated');
        refreshConfig();
      } else {
        toast.error('Failed to save');
      }
    } catch {
      toast.error('Network error');
    }
  };

  // ─── Navigation ───
  const navItems = [
    { id: 'overview', label: 'Overview', icon: LayoutDashboard },
    { id: 'bot', label: 'Bot Control', icon: Bot },
    { id: 'glm', label: 'GLM Engine', icon: Brain },
    { id: 'files', label: 'Files', icon: FolderOpen },
    { id: 'deploy', label: 'Deploy', icon: Rocket },
    { id: 'loops', label: 'Loop Problems', icon: Code },
    { id: 'settings', label: 'Settings', icon: Settings },
    { id: 'logs', label: 'Activity Log', icon: Activity },
  ];

  const navigate = (section: string) => {
    setActiveSection(section);
    setSidebarOpen(false);
    if (section === 'files') refreshFiles();
    if (section === 'deploy') refreshDeployStatus();
  };

  // ─── Format helpers ───
  const formatSize = (bytes: number) => {
    if (bytes > 1024 * 1024) return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    if (bytes > 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return bytes + ' B';
  };

  const formatGLM = (text: string) => {
    let html = text
      .replace(/```([a-zA-Z0-9_+-]*)\n?([\s\S]*?)```/g, '<pre class="bg-black/30 p-3 rounded-lg overflow-x-auto my-2"><code>$2</code></pre>')
      .replace(/`([^`]+)`/g, '<code class="bg-black/30 px-1.5 py-0.5 rounded text-xs font-mono">$1</code>')
      .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
      .replace(/\n/g, '<br/>');
    return html;
  };

  // ═══════════════════════════════════════════════
  // RENDER
  // ═══════════════════════════════════════════════

  return (
    <div className="min-h-screen bg-[#0a0e1a] text-slate-100 flex">
      {/* Sidebar overlay for mobile */}
      {sidebarOpen && (
        <div
          className="fixed inset-0 bg-black/50 z-40 lg:hidden"
          onClick={() => setSidebarOpen(false)}
        />
      )}

      {/* Sidebar */}
      <aside className={`fixed left-0 top-0 h-full w-60 bg-[#111827] border-r border-slate-700/50 flex flex-col z-50 transition-transform duration-300 ${sidebarOpen ? 'translate-x-0' : '-translate-x-full'} lg:translate-x-0`}>
        <div className="p-5 border-b border-slate-700/50">
          <h2 className="text-xl font-bold text-blue-400">🛡️ WHOAMISec AI</h2>
          <span className="text-[10px] bg-[#1a1f35] text-slate-500 px-2 py-0.5 rounded">v4.0 Expert</span>
        </div>
        <nav className="flex-1 py-3">
          {navItems.map(item => (
            <button
              key={item.id}
              onClick={() => navigate(item.id)}
              className={`w-full flex items-center gap-3 px-5 py-3 text-sm font-medium transition-all text-left ${
                activeSection === item.id
                  ? 'bg-blue-500/10 text-blue-400 border-l-3 border-l-blue-500'
                  : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200 border-l-3 border-l-transparent'
              }`}
            >
              <item.icon className="h-4 w-4" />
              {item.label}
            </button>
          ))}
        </nav>
        <div className="p-4 border-t border-slate-700/50">
          <button
            onClick={onLogout}
            className="w-full flex items-center justify-center gap-2 py-2.5 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 text-sm font-medium transition-colors"
          >
            <LogOut className="h-4 w-4" />
            Logout
          </button>
        </div>
      </aside>

      {/* Main Content */}
      <main className="flex-1 lg:ml-60 min-h-screen">
        {/* Top bar */}
        <header className="sticky top-0 z-30 bg-[#0a0e1a]/85 backdrop-blur-md border-b border-slate-700/50 px-5 py-4 flex items-center gap-4">
          <button
            onClick={() => setSidebarOpen(true)}
            className="lg:hidden p-2 border border-slate-600 rounded-lg text-slate-300 hover:bg-slate-800"
          >
            <Command className="h-5 w-5" />
          </button>
          <h1 className="text-lg font-semibold flex-1">
            {navItems.find(n => n.id === activeSection)?.icon && (
              <span className="inline-flex items-center gap-2">
                {React.createElement(navItems.find(n => n.id === activeSection)!.icon, { className: 'h-5 w-5' })}
                {navItems.find(n => n.id === activeSection)?.label}
              </span>
            )}
          </h1>
          <div className="flex items-center gap-2 text-sm text-slate-400">
            <span className="h-2 w-2 rounded-full bg-emerald-500 shadow-lg shadow-emerald-500/50" />
            Online
          </div>
        </header>

        <div className="p-5 max-w-7xl mx-auto">
          {/* ═══ OVERVIEW ═══ */}
          {activeSection === 'overview' && (
            <div className="space-y-5">
              <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                {[
                  {
                    icon: <Bot className="h-5 w-5" />,
                    label: 'Bot Status',
                    value: config?.telegram_token_masked ? '✅ Ready' : '❌ No Token',
                    color: config?.telegram_token_masked ? 'text-emerald-400' : 'text-red-400',
                  },
                  {
                    icon: <Brain className="h-5 w-5" />,
                    label: 'z.ai API',
                    value: '✅ Auto (SDK)',
                    color: 'text-emerald-400',
                  },
                  {
                    icon: <FolderOpen className="h-5 w-5" />,
                    label: 'Files',
                    value: `${(files.downloads?.length || 0) + (files.generated?.length || 0)} files`,
                    color: 'text-blue-400',
                  },
                  {
                    icon: <Rocket className="h-5 w-5" />,
                    label: 'Deploy Ready',
                    value: deployStatus ? `${deployStatus.summary?.ok || 0}/${deployStatus.summary?.total || 9}` : '—',
                    color: 'text-blue-400',
                  },
                  {
                    icon: <Shield className="h-5 w-5" />,
                    label: 'Auto-Repair',
                    value: autoRepair !== 'false' ? `✅ ON (${maxRepair})` : '❌ OFF',
                    color: autoRepair !== 'false' ? 'text-emerald-400' : 'text-red-400',
                  },
                  {
                    icon: <Cpu className="h-5 w-5" />,
                    label: 'Expert Mode',
                    value: expertMode === 'true' ? '👑 ON' : '🌱 OFF',
                    color: expertMode === 'true' ? 'text-amber-400' : 'text-slate-400',
                  },
                ].map((stat, i) => (
                  <Card key={i} className="bg-[#111827] border-slate-700/50 hover:border-slate-600 transition-colors">
                    <CardContent className="p-4 flex items-center gap-3">
                      <div className="text-2xl">{stat.icon}</div>
                      <div>
                        <div className={`text-base font-bold ${stat.color}`}>{stat.value}</div>
                        <div className="text-[11px] text-slate-500">{stat.label}</div>
                      </div>
                    </CardContent>
                  </Card>
                ))}
              </div>

              <Card className="bg-[#111827] border-slate-700/50">
                <CardHeader className="pb-3">
                  <CardTitle className="text-base">⚡ Quick Actions</CardTitle>
                </CardHeader>
                <CardContent className="flex flex-wrap gap-2">
                  {['/status', '/files', '/clear', '/progress'].map(cmd => (
                    <Button
                      key={cmd}
                      variant="outline"
                      size="sm"
                      className="bg-[#1a1f35] border-slate-700 text-slate-300 hover:bg-blue-600 hover:text-white hover:border-blue-600 text-xs"
                      onClick={() => sendBotCommand(cmd)}
                    >
                      <Terminal className="h-3 w-3 mr-1" />
                      {cmd}
                    </Button>
                  ))}
                  <Button
                    variant="outline"
                    size="sm"
                    className="bg-[#1a1f35] border-slate-700 text-slate-300 hover:bg-blue-600 hover:text-white hover:border-blue-600 text-xs"
                    onClick={() => navigate('deploy')}
                  >
                    <Rocket className="h-3 w-3 mr-1" />
                    🚀 Deploy
                  </Button>
                  <Button
                    variant="outline"
                    size="sm"
                    className="bg-[#1a1f35] border-slate-700 text-slate-300 hover:bg-blue-600 hover:text-white hover:border-blue-600 text-xs"
                    onClick={() => navigate('glm')}
                  >
                    <Brain className="h-3 w-3 mr-1" />
                    🧠 GLM Chat
                  </Button>
                </CardContent>
              </Card>

              <Card className="bg-[#111827] border-slate-700/50">
                <CardHeader className="pb-3">
                  <CardTitle className="text-base">💬 Recent Messages</CardTitle>
                </CardHeader>
                <CardContent>
                  <ScrollArea className="h-72">
                    {botMessages.length === 0 ? (
                      <p className="text-slate-500 text-sm text-center py-8">No messages yet</p>
                    ) : (
                      <div className="space-y-0">
                        {botMessages.map((m, i) => (
                          <div key={i} className="flex gap-3 py-2.5 border-b border-slate-800 last:border-0">
                            <span className="text-blue-400 font-semibold text-xs min-w-[50px]">{typeof m.from === 'object' ? m.from?.name || m.from?.username || '?' : m.from}</span>
                            <span className="text-slate-300 text-xs flex-1 break-all">{m.text}</span>
                            <span className="text-slate-600 text-[10px] whitespace-nowrap">
                              {m.date?.split('T')[1]?.slice(0, 5) || ''}
                            </span>
                          </div>
                        ))}
                      </div>
                    )}
                  </ScrollArea>
                </CardContent>
              </Card>
            </div>
          )}

          {/* ═══ BOT CONTROL ═══ */}
          {activeSection === 'bot' && (
            <div className="space-y-5">
              {/* Bot Power Card */}
              <Card className={`bg-[#111827] border-2 ${botActive ? 'border-emerald-500/50' : 'border-slate-700/50'}`}>
                <CardContent className="p-5">
                  <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                      <div className={`w-14 h-14 rounded-xl flex items-center justify-center text-2xl ${botActive ? 'bg-emerald-500/20' : 'bg-slate-800'}`}>
                        {botActive ? '🟢' : '🔴'}
                      </div>
                      <div>
                        <h3 className="font-bold text-lg">WHOAMISec AI — Bot Agent</h3>
                        <p className="text-xs text-slate-500">
                          {botActive ? `Polling activ · ${pollCount} update(s) procesate` : 'Bot oprit · Click Start pentru activare'}
                        </p>
                      </div>
                    </div>
                    <div className="flex gap-2">
                      {!botActive ? (
                        <Button
                          onClick={startBot}
                          disabled={botSetupLoading || !config?.telegram_token_masked}
                          className="bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-semibold px-6"
                        >
                          {botSetupLoading ? <Loader2 className="h-4 w-4 animate-spin mr-2" /> : <Zap className="h-4 w-4 mr-2" />}
                          Start Bot
                        </Button>
                      ) : (
                        <Button
                          onClick={stopBot}
                          variant="destructive"
                          className="px-6"
                        >
                          <XCircle className="h-4 w-4 mr-2" />
                          Stop Bot
                        </Button>
                      )}
                    </div>
                  </div>
                  {botActive && (
                    <div className="mt-4 flex flex-wrap gap-2">
                      {['/start', '/status', '/api', '/code', '/analyze', '/files', '/model', '/deploy', '/train_prompt', '/p1', '/p12'].map(cmd => (
                        <Button
                          key={cmd}
                          variant="outline"
                          size="sm"
                          className="bg-[#1a1f35] border-slate-700 text-slate-400 hover:text-white hover:border-slate-500 text-xs"
                          onClick={() => sendBotCommand(cmd)}
                        >
                          <Terminal className="h-3 w-3 mr-1" />
                          {cmd}
                        </Button>
                      ))}
                    </div>
                  )}
                </CardContent>
              </Card>

              <Card className="bg-[#111827] border-slate-700/50">
                <CardHeader className="pb-3">
                  <CardTitle className="text-base">💬 Send Command to Bot</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div className="flex gap-2">
                    <Input
                      id="botCmdInput"
                      placeholder="Type /command or any message..."
                      className="bg-[#0f172a] border-slate-600 text-slate-100 flex-1"
                      onKeyDown={e => { if (e.key === 'Enter') sendBotCommand(); }}
                    />
                    <Button
                      onClick={() => sendBotCommand()}
                      disabled={botLoading}
                      className="bg-blue-600 hover:bg-blue-500"
                    >
                      {botLoading ? <Loader2 className="h-4 w-4 animate-spin" /> : <Send className="h-4 w-4" />}
                    </Button>
                  </div>
                  <div className="flex flex-wrap gap-2 items-center">
                    <span className="text-[11px] text-slate-500 mr-1">Quick:</span>
                    {['/status', '/files', '/clear', '/progress', '/p1', '/p6', '/p12', '/analyze'].map(cmd => (
                      <Button
                        key={cmd}
                        variant="outline"
                        size="sm"
                        className="bg-[#1a1f35] border-slate-700 text-slate-400 hover:text-white hover:border-slate-500 text-xs"
                        onClick={() => sendBotCommand(cmd)}
                      >
                        {cmd}
                      </Button>
                    ))}
                  </div>
                </CardContent>
              </Card>

              <Card className="bg-[#111827] border-slate-700/50">
                <CardHeader className="pb-3 flex flex-row items-center justify-between">
                  <CardTitle className="text-base">📨 Bot Messages (Live)</CardTitle>
                  <Button variant="ghost" size="sm" onClick={refreshMessages} className="text-slate-400 hover:text-white text-xs">
                    <RefreshCw className="h-3 w-3 mr-1" /> Refresh
                  </Button>
                </CardHeader>
                <CardContent>
                  <ScrollArea className="h-80">
                    {botMessages.length === 0 ? (
                      <p className="text-slate-500 text-sm text-center py-8">No messages yet</p>
                    ) : (
                      <div className="space-y-0">
                        {botMessages.map((m, i) => (
                          <div key={i} className="flex gap-3 py-2.5 border-b border-slate-800 last:border-0">
                            <span className="text-blue-400 font-semibold text-xs min-w-[50px]">{typeof m.from === 'object' ? m.from?.name || m.from?.username || '?' : m.from}</span>
                            <span className="text-slate-300 text-xs flex-1 break-all">{m.text}</span>
                            <span className="text-slate-600 text-[10px] whitespace-nowrap">
                              {m.date?.split('T')[1]?.slice(0, 5) || ''}
                            </span>
                          </div>
                        ))}
                      </div>
                    )}
                  </ScrollArea>
                </CardContent>
              </Card>
            </div>
          )}

          {/* ═══ GLM ENGINE ═══ */}
          {activeSection === 'glm' && (
            <div className="space-y-5">
              {/* Model Selector & Agent Toggles */}
              <Card className="bg-[#111827] border-slate-700/50">
                <CardHeader className="pb-3">
                  <CardTitle className="text-base">🧠 Agent Settings</CardTitle>
                  <CardDescription className="text-slate-500 text-xs">
                    Multi-model AI Agent · Selectează modelul și capabilitățile
                  </CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  {/* Model Selection */}
                  <div>
                    <label className="text-slate-400 text-xs font-medium mb-2 block">Model curent: <span className="text-blue-400">{glmModel}</span></label>
                    <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 max-h-48 overflow-y-auto">
                      {[
                        { provider: 'Queen', models: ['queen-ultra', 'queen-max'] },
                        { provider: 'Nous Research', models: ['hermes-4-405B', 'hermes-4-70B'] },
                        { provider: 'OpenAI', models: ['gpt-5.4-pro', 'gpt-5.4', 'gpt-5.2'] },
                        { provider: 'Anthropic', models: ['claude-opus-4-6', 'claude-sonnet-4-6'] },
                        { provider: 'DeepSeek', models: ['DeepSeek-3.2'] },
                        { provider: 'Google', models: ['gemini-3.0-pro-preview', 'gemini-3-flash'] },
                        { provider: 'Kimi', models: ['kimi-k2.5'] },
                        { provider: 'MiniMax', models: ['minimax-m2.5'] },
                        { provider: 'Qwen', models: ['qwen3.6-plus', 'qwen3.5'] },
                        { provider: 'z-ai / GLM', models: ['glm-5-turbo', 'glm-4.6', 'glm-4-flash'] },
                      ].map(group => (
                        <React.Fragment key={group.provider}>
                          <div className="col-span-2 md:col-span-3 lg:col-span-4 text-[10px] text-slate-500 font-semibold uppercase tracking-wider mt-1">{group.provider}</div>
                          {group.models.map(m => (
                            <button
                              key={m}
                              onClick={() => { setGlmModel(m); saveConfig({ glm_model: m }); }}
                              className={`text-left px-3 py-2 rounded-lg text-xs font-medium transition-all border ${
                                glmModel === m
                                  ? 'bg-blue-500/20 border-blue-500/50 text-blue-300'
                                  : 'bg-[#0a0e1a] border-slate-700/50 text-slate-400 hover:border-slate-600 hover:text-slate-200'
                              }`}
                            >
                              {m === 'queen-ultra' ? '👑 ' : m === 'queen-max' ? '👑 ' : ''}{m}
                            </button>
                          ))}
                        </React.Fragment>
                      ))}
                    </div>
                  </div>

                  {/* Agent Capability Toggles */}
                  <Separator className="bg-slate-800" />
                  <div className="flex flex-wrap gap-4">
                    <button
                      onClick={() => { setAgentReasoning(!agentReasoning); addLog('info', `Reasoning: ${!agentReasoning ? 'ON' : 'OFF'}`); }}
                      className={`flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-medium border transition-all ${agentReasoning ? 'bg-amber-500/20 border-amber-500/50 text-amber-300' : 'bg-[#0a0e1a] border-slate-700/50 text-slate-500'}`}
                    >
                      💡 Reasoning {agentReasoning ? 'ON' : 'OFF'}
                    </button>
                    <button
                      onClick={() => { setAgentMemory(!agentMemory); addLog('info', `Memory: ${!agentMemory ? 'ON' : 'OFF'}`); }}
                      className={`flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-medium border transition-all ${agentMemory ? 'bg-purple-500/20 border-purple-500/50 text-purple-300' : 'bg-[#0a0e1a] border-slate-700/50 text-slate-500'}`}
                    >
                      🧠 Memory {agentMemory ? 'ON' : 'OFF'}
                    </button>
                    <button
                      onClick={() => { setAgentCots(!agentCots); addLog('info', `CoTs: ${!agentCots ? 'ON' : 'OFF'}`); }}
                      className={`flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-medium border transition-all ${agentCots ? 'bg-emerald-500/20 border-emerald-500/50 text-emerald-300' : 'bg-[#0a0e1a] border-slate-700/50 text-slate-500'}`}
                    >
                      🔗 CoTs in Context {agentCots ? 'ON' : 'OFF'}
                    </button>
                  </div>
                </CardContent>
              </Card>

              {/* GLM Chat */}
              <Card className="bg-[#111827] border-slate-700/50">
                <CardHeader className="pb-3">
                  <CardTitle className="text-base">🧠 AI Chat · <span className="text-blue-400">{glmModel}</span></CardTitle>
                  <CardDescription className="text-slate-500 text-xs">
                    {agentReasoning ? '💡 ' : ''}{agentMemory ? '🧠 ' : ''}{agentCots ? '🔗 ' : ''}
                    {agentReasoning && 'Reasoning '}{agentMemory && 'Memory '}{agentCots && 'CoTs '}
                    {!agentReasoning && !agentMemory && !agentCots && 'Standard mode'}
                  </CardDescription>
                </CardHeader>
                <CardContent className="space-y-3">
                <ScrollArea className="h-[420px]">
                  <div className="space-y-2 pr-3">
                    {glmMessages.map((m, i) => (
                      <div
                        key={i}
                        className={`p-3 rounded-lg text-sm leading-relaxed break-words ${
                          m.role === 'user'
                            ? 'bg-[#1e3a5f] ml-10'
                            : m.role === 'assistant'
                            ? 'bg-blue-500/10 mr-10 border border-blue-500/10'
                            : 'text-center text-slate-500 text-xs py-1'
                        }`}
                        dangerouslySetInnerHTML={m.role !== 'system' ? { __html: formatGLM(m.content) } : undefined}
                      >
                        {m.role === 'system' ? m.content : undefined}
                      </div>
                    ))}
                    {glmLoading && (
                      <div className="text-center py-2">
                        <Loader2 className="h-4 w-4 text-blue-400 animate-spin inline" />
                        <span className="text-slate-500 text-xs ml-2">Thinking...</span>
                      </div>
                    )}
                    <div ref={glmEndRef} />
                  </div>
                </ScrollArea>
                <div className="flex gap-2 pt-2">
                  <Input
                    id="glmInput"
                    placeholder="Ask anything..."
                    className="bg-[#0f172a] border-slate-600 text-slate-100 flex-1"
                    onKeyDown={e => { if (e.key === 'Enter') sendGLM(); }}
                    disabled={glmLoading}
                  />
                  <Button onClick={sendGLM} disabled={glmLoading} className="bg-blue-600 hover:bg-blue-500">
                    {glmLoading ? <Loader2 className="h-4 w-4 animate-spin" /> : <Send className="h-4 w-4" />}
                  </Button>
                </div>
                </CardContent>
              </Card>
            </div>
          )}

          {/* ═══ FILES ═══ */}
          {activeSection === 'files' && (
            <div className="space-y-5">
              <Card className="bg-[#111827] border-slate-700/50">
                <CardHeader className="pb-3 flex flex-row items-center justify-between">
                  <CardTitle className="text-base">📥 Downloaded Files</CardTitle>
                  <Button variant="ghost" size="sm" onClick={refreshFiles} className="text-slate-400 hover:text-white text-xs">
                    <RefreshCw className="h-3 w-3 mr-1" /> Refresh
                  </Button>
                </CardHeader>
                <CardContent>
                  {files.downloads?.length === 0 ? (
                    <p className="text-slate-500 text-sm text-center py-6">No downloaded files</p>
                  ) : (
                    <ScrollArea className="h-60">
                      <div className="space-y-0">
                        {files.downloads?.map((f, i) => (
                          <div key={i} className="flex items-center gap-3 py-2.5 border-b border-slate-800 last:border-0">
                            <FileText className="h-4 w-4 text-blue-400 shrink-0" />
                            <span className="text-slate-200 text-xs flex-1 break-all">{f.name}</span>
                            <span className="text-slate-500 text-xs">{formatSize(f.size)}</span>
                          </div>
                        ))}
                      </div>
                    </ScrollArea>
                  )}
                </CardContent>
              </Card>

              <Card className="bg-[#111827] border-slate-700/50">
                <CardHeader className="pb-3">
                  <CardTitle className="text-base">💻 Generated Code</CardTitle>
                </CardHeader>
                <CardContent>
                  {files.generated?.length === 0 ? (
                    <p className="text-slate-500 text-sm text-center py-6">No generated files</p>
                  ) : (
                    <ScrollArea className="h-60">
                      <div className="space-y-0">
                        {files.generated?.map((f, i) => (
                          <div key={i} className="flex items-center gap-3 py-2.5 border-b border-slate-800 last:border-0">
                            <Code className="h-4 w-4 text-emerald-400 shrink-0" />
                            <span className="text-slate-200 text-xs flex-1 break-all">{f.name}</span>
                            <span className="text-slate-500 text-xs">{formatSize(f.size)}</span>
                          </div>
                        ))}
                      </div>
                    </ScrollArea>
                  )}
                </CardContent>
              </Card>
            </div>
          )}

          {/* ═══ DEPLOY ═══ */}
          {activeSection === 'deploy' && (
            <div className="space-y-5">
              <Card className="bg-[#111827] border-slate-700/50">
                <CardHeader className="pb-3">
                  <CardTitle className="text-base">🚀 Deployment Center</CardTitle>
                  <CardDescription className="text-slate-500 text-xs">
                    Build & deploy workflows for GitHub, Expo, Docker, Render
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    {[
                      {
                        icon: <GitBranch className="h-7 w-7" />,
                        name: 'GitHub Actions',
                        desc: 'Auto build & deploy on push to main',
                        status: config?.github_repo ? '✅ Configured' : '❌ No repo',
                        actions: [
                          { label: 'Trigger Deploy', onClick: () => {
                            if (config?.github_repo) {
                              const parts = config.github_repo.replace('https://github.com/', '').replace('.git', '').split('/');
                              if (parts.length === 2) {
                                window.open(`https://github.com/${parts[0]}/${parts[1]}/actions`, '_blank');
                                addLog('ok', 'Opened GitHub Actions');
                              }
                            } else toast.error('Set repo in Settings first');
                          }},
                        ],
                      },
                      {
                        icon: <Smartphone className="h-7 w-7" />,
                        name: 'Expo.dev',
                        desc: 'Build Android/iOS/Web via EAS',
                        status: '✅ Workflow ready',
                        actions: [
                          { label: 'Open Expo', onClick: () => window.open('https://expo.dev', '_blank') },
                        ],
                      },
                      {
                        icon: <Monitor className="h-7 w-7" />,
                        name: 'Docker (VPS)',
                        desc: 'Container deployment for 24/7',
                        status: '✅ Dockerfile ready',
                        actions: [
                          { label: 'Copy Commands', onClick: () => {
                            const cmds = `# Docker Deploy 24/7
git clone YOUR_REPO.git hermes && cd hermes
cp .env.example .env
docker compose up -d --build
docker compose logs -f`;
                            navigator.clipboard.writeText(cmds);
                            toast.success('Docker commands copied!');
                            addLog('ok', 'Docker cmds copied');
                          }},
                        ],
                      },
                      {
                        icon: <Globe className="h-7 w-7" />,
                        name: 'Render / Railway',
                        desc: 'One-click PaaS hosting (free tier)',
                        status: '✅ Procfile ready',
                        actions: [
                          { label: 'Render', onClick: () => window.open('https://render.com', '_blank') },
                          { label: 'Railway', onClick: () => window.open('https://railway.app', '_blank') },
                        ],
                      },
                    ].map((d, i) => (
                      <div key={i} className="bg-[#0a0e1a] border border-slate-700/50 rounded-xl p-5 text-center">
                        <div className="text-3xl mb-3">{d.icon}</div>
                        <h4 className="text-sm font-semibold mb-1">{d.name}</h4>
                        <p className="text-[11px] text-slate-500 mb-2">{d.desc}</p>
                        <Badge variant="secondary" className="text-[10px] mb-3 bg-emerald-500/10 text-emerald-400">
                          {d.status}
                        </Badge>
                        <div className="space-y-2">
                          {d.actions.map((a, j) => (
                            <Button
                              key={j}
                              size="sm"
                              className="w-full bg-[#1a1f35] border border-slate-700 text-slate-300 hover:bg-blue-600 hover:text-white hover:border-blue-600 text-xs"
                              onClick={a.onClick}
                            >
                              {a.label}
                              <ExternalLink className="h-3 w-3 ml-1" />
                            </Button>
                          ))}
                        </div>
                      </div>
                    ))}
                  </div>

                  <Separator className="bg-slate-800" />

                  <div className="space-y-4 mt-6">
                    <h3 className="text-sm font-semibold text-slate-200">📋 Deploy Instructions</h3>
                    {[
                      {
                        title: 'Option A: Docker (VPS) - Recommended 24/7',
                        code: `git clone YOUR_REPO.git hermes && cd hermes
cp .env.example .env
# Set ADMIN_PASSWORD_HASH and API keys in .env
docker compose up -d --build`,
                      },
                      {
                        title: 'Option B: Render.com (Free tier)',
                        code: `1. Push to GitHub
2. render.com → New Web Service
3. Connect repo
4. Build: pip install -r requirements.txt
5. Start: gunicorn web.app:app
6. Set env vars from .env.example`,
                      },
                      {
                        title: 'Option C: GitHub Actions → VPS',
                        code: `1. Set secrets: VPS_HOST, VPS_USER, VPS_SSH_KEY
2. Push to main → auto deploy via SSH
3. Or use workflow_dispatch for manual`,
                      },
                    ].map((block, i) => (
                      <div key={i} className="bg-[#0a0e1a] rounded-lg p-4">
                        <h4 className="text-xs text-blue-400 font-semibold mb-2">{block.title}</h4>
                        <pre className="text-[11px] text-slate-400 font-mono whitespace-pre-wrap leading-relaxed">
                          {block.code}
                        </pre>
                      </div>
                    ))}
                  </div>
                </CardContent>
              </Card>
            </div>
          )}

          {/* ═══ LOOP PROBLEMS ═══ */}
          {activeSection === 'loops' && (
            <div className="space-y-5">
              <Card className="bg-[#111827] border-slate-700/50">
                <CardHeader className="pb-3">
                  <CardTitle className="text-base">🔄 Loop Problems (P1-P12)</CardTitle>
                  <CardDescription className="text-slate-500 text-xs">
                    Select a problem to view details, template, and hint
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    {loopProblems.map(p => (
                      <button
                        key={p.id}
                        onClick={() => setSelectedProblem(selectedProblem?.id === p.id ? null : p)}
                        className={`p-3 rounded-lg border text-left transition-all ${
                          selectedProblem?.id === p.id
                            ? 'border-blue-500 bg-blue-500/10'
                            : 'border-slate-700/50 bg-[#0a0e1a] hover:border-slate-600'
                        }`}
                      >
                        <div className="flex items-center gap-2 mb-1">
                          <Badge
                            variant="secondary"
                            className={`text-[9px] ${
                              p.difficulty === 'beginner'
                                ? 'bg-emerald-500/10 text-emerald-400'
                                : p.difficulty === 'intermediate'
                                ? 'bg-amber-500/10 text-amber-400'
                                : 'bg-red-500/10 text-red-400'
                            }`}
                          >
                            P{p.id}
                          </Badge>
                          <Badge variant="secondary" className="text-[9px] bg-slate-700/50 text-slate-400">
                            {p.category}
                          </Badge>
                        </div>
                        <p className="text-xs font-semibold text-slate-200">{p.title}</p>
                        <p className="text-[10px] text-slate-500 mt-0.5 line-clamp-2">{p.description}</p>
                      </button>
                    ))}
                  </div>

                  {selectedProblem && (
                    <div className="mt-5 bg-[#0a0e1a] rounded-lg p-5 space-y-4 border border-slate-700/50">
                      <div className="flex items-center gap-3">
                        <Badge className="bg-blue-500/20 text-blue-400">P{selectedProblem.id}</Badge>
                        <h3 className="text-lg font-bold">{selectedProblem.title}</h3>
                        <Badge
                          className={`${
                            selectedProblem.difficulty === 'beginner'
                              ? 'bg-emerald-500/10 text-emerald-400'
                              : selectedProblem.difficulty === 'intermediate'
                              ? 'bg-amber-500/10 text-amber-400'
                              : 'bg-red-500/10 text-red-400'
                          }`}
                        >
                          {selectedProblem.difficulty}
                        </Badge>
                      </div>
                      <p className="text-sm text-slate-300 leading-relaxed">{selectedProblem.description}</p>
                      <div>
                        <h4 className="text-xs text-blue-400 font-semibold mb-2">Template:</h4>
                        <pre className="bg-black/30 p-4 rounded-lg text-xs text-slate-300 font-mono overflow-x-auto">
                          {selectedProblem.template}
                        </pre>
                      </div>
                      {selectedProblem.hint && (
                        <div className="bg-amber-500/5 border border-amber-500/20 rounded-lg p-3">
                          <span className="text-amber-400 text-xs font-semibold">💡 Hint:</span>
                          <p className="text-xs text-slate-300 mt-1">{selectedProblem.hint}</p>
                        </div>
                      )}
                      <Button
                        size="sm"
                        className="bg-blue-600 hover:bg-blue-500 text-xs"
                        onClick={() => sendBotCommand(`/p${selectedProblem.id}`)}
                      >
                        <Send className="h-3 w-3 mr-1" /> Send to Bot
                      </Button>
                    </div>
                  )}
                </CardContent>
              </Card>
            </div>
          )}

          {/* ═══ SETTINGS ═══ */}
          {activeSection === 'settings' && (
            <div className="space-y-5">
              {/* API Keys */}
              <Card className="bg-[#111827] border-slate-700/50">
                <CardHeader className="pb-3">
                  <CardTitle className="text-base">🔑 API &amp; Keys</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  {/* z.ai SDK Auto Status */}
                  <div className="flex items-center gap-3 p-3 rounded-lg bg-emerald-900/20 border border-emerald-800/30">
                    <div className="text-2xl">🔗</div>
                    <div>
                      <p className="text-sm font-semibold text-emerald-400">z.ai API: AUTO-CONFIGURAT</p>
                      <p className="text-xs text-slate-400">Conectat automat prin GitHub → z.ai SDK. Nu e nevoie de cheie manuală.</p>
                    </div>
                  </div>
                  <div className="grid md:grid-cols-2 gap-4">
                    <div className="space-y-2">
                      <label className="text-xs text-slate-400 font-medium">Telegram Bot Token</label>
                      <Input
                        type="password"
                        value={telegramToken}
                        onChange={e => setTelegramToken(e.target.value)}
                        placeholder="123456789:ABCdef..."
                        className="bg-[#0f172a] border-slate-600 text-slate-100"
                      />
                      {config?.telegram_token_masked && (
                        <p className="text-[10px] text-slate-600">Current: {config.telegram_token_masked}</p>
                      )}
                    </div>
                    <div className="space-y-2">
                      <label className="text-xs text-slate-400 font-medium">GLM API Key <span className="text-slate-600">(opțional)</span></label>
                      <Input
                        type="password"
                        value={glmKey}
                        onChange={e => setGlmKey(e.target.value)}
                        placeholder="Opțional — API merge automat"
                        className="bg-[#0f172a] border-slate-600 text-slate-100"
                      />
                      <p className="text-[10px] text-slate-600">SDK auto-configurat. Cheia e opțională.</p>
                    </div>
                  </div>
                  <Button
                    className="bg-blue-600 hover:bg-blue-500 text-sm"
                    onClick={() => {
                      const updates: Record<string, string> = {};
                      if (telegramToken) updates.telegram_token = telegramToken;
                      if (glmKey) updates.glm_api_key = glmKey;
                      if (!telegramToken && !glmKey) { toast.info('No changes'); return; }
                      saveConfig(updates);
                      setTelegramToken('');
                      setGlmKey('');
                    }}
                  >
                    <Key className="h-3.5 w-3.5 mr-1.5" /> Save Keys
                  </Button>
                </CardContent>
              </Card>

              {/* GLM Settings */}
              <Card className="bg-[#111827] border-slate-700/50">
                <CardHeader className="pb-3">
                  <CardTitle className="text-base">🧠 GLM Settings</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div className="grid md:grid-cols-2 gap-4">
                    <div className="space-y-2">
                      <label className="text-xs text-slate-400 font-medium">Model</label>
                      <select
                        value={glmModel}
                        onChange={e => setGlmModel(e.target.value)}
                        className="w-full bg-[#0f172a] border border-slate-600 rounded-lg px-3 py-2 text-sm text-slate-100 outline-none focus:border-blue-500"
                      >
                        {['glm-4.6', 'glm-4.5-air', 'glm-4-plus', 'glm-5.1', 'glm-5-turbo'].map(m => (
                          <option key={m} value={m}>{m}</option>
                        ))}
                      </select>
                    </div>
                    <div className="space-y-2">
                      <label className="text-xs text-slate-400 font-medium">Endpoint</label>
                      <select
                        value={glmEndpoint}
                        onChange={e => setGlmEndpoint(e.target.value)}
                        className="w-full bg-[#0f172a] border border-slate-600 rounded-lg px-3 py-2 text-sm text-slate-100 outline-none focus:border-blue-500"
                      >
                        <option value="https://api.z.ai/api/coding/paas/v4/chat/completions">Coding API</option>
                        <option value="https://api.z.ai/api/paas/v4/chat/completions">General API</option>
                      </select>
                    </div>
                  </div>
                  <Button
                    className="bg-blue-600 hover:bg-blue-500 text-sm"
                    onClick={() => saveConfig({ glm_model: glmModel, glm_endpoint: glmEndpoint })}
                  >
                    <Brain className="h-3.5 w-3.5 mr-1.5" /> Save GLM Settings
                  </Button>
                </CardContent>
              </Card>

              {/* Auto-Repair & Expert */}
              <div className="grid md:grid-cols-2 gap-5">
                <Card className="bg-[#111827] border-slate-700/50">
                  <CardHeader className="pb-3">
                    <CardTitle className="text-base">🔧 Auto-Repair</CardTitle>
                  </CardHeader>
                  <CardContent className="space-y-4">
                    <div className="space-y-2">
                      <label className="text-xs text-slate-400 font-medium">Status</label>
                      <select
                        value={autoRepair}
                        onChange={e => setAutoRepair(e.target.value)}
                        className="w-full bg-[#0f172a] border border-slate-600 rounded-lg px-3 py-2 text-sm text-slate-100 outline-none focus:border-blue-500"
                      >
                        <option value="true">✅ ON</option>
                        <option value="false">❌ OFF</option>
                      </select>
                    </div>
                    <div className="space-y-2">
                      <label className="text-xs text-slate-400 font-medium">Max Iterations</label>
                      <Input
                        type="number"
                        value={maxRepair}
                        onChange={e => setMaxRepair(parseInt(e.target.value) || 3)}
                        min={1}
                        max={10}
                        className="bg-[#0f172a] border-slate-600 text-slate-100"
                      />
                    </div>
                    <Button
                      className="bg-blue-600 hover:bg-blue-500 text-sm"
                      onClick={() => saveConfig({ auto_repair, max_repair_iterations: maxRepair })}
                    >
                      <Shield className="h-3.5 w-3.5 mr-1.5" /> Save Repair
                    </Button>
                  </CardContent>
                </Card>

                <Card className="bg-[#111827] border-slate-700/50">
                  <CardHeader className="pb-3">
                    <CardTitle className="text-base">👑 Expert Mode</CardTitle>
                  </CardHeader>
                  <CardContent className="space-y-4">
                    <div className="space-y-2">
                      <label className="text-xs text-slate-400 font-medium">Mode</label>
                      <select
                        value={expertMode}
                        onChange={e => setExpertMode(e.target.value)}
                        className="w-full bg-[#0f172a] border border-slate-600 rounded-lg px-3 py-2 text-sm text-slate-100 outline-none focus:border-blue-500"
                      >
                        <option value="true">👑 Expert ON - Detailed prompts</option>
                        <option value="false">🌱 Normal</option>
                      </select>
                    </div>
                    <Button
                      className="bg-blue-600 hover:bg-blue-500 text-sm"
                      onClick={() => saveConfig({ expert_mode: expertMode })}
                    >
                      <Cpu className="h-3.5 w-3.5 mr-1.5" /> Save
                    </Button>
                  </CardContent>
                </Card>
              </div>

              {/* GitHub Repo */}
              <Card className="bg-[#111827] border-slate-700/50">
                <CardHeader className="pb-3">
                  <CardTitle className="text-base">🐙 GitHub Repo</CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="flex gap-2">
                    <Input
                      value={githubRepo}
                      onChange={e => setGithubRepo(e.target.value)}
                      placeholder="https://github.com/user/repo.git"
                      className="bg-[#0f172a] border-slate-600 text-slate-100 flex-1"
                    />
                    <Button
                      className="bg-blue-600 hover:bg-blue-500"
                      onClick={() => {
                        if (!githubRepo.trim()) { toast.info('Empty repo'); return; }
                        saveConfig({ github_repo: githubRepo });
                      }}
                    >
                      <GitBranch className="h-3.5 w-3.5 mr-1.5" /> Save
                    </Button>
                  </div>
                </CardContent>
              </Card>
            </div>
          )}

          {/* ═══ LOGS ═══ */}
          {activeSection === 'logs' && (
            <Card className="bg-[#111827] border-slate-700/50">
              <CardHeader className="pb-3">
                <CardTitle className="text-base">📋 Activity Log</CardTitle>
              </CardHeader>
              <CardContent>
                <ScrollArea className="h-[500px]">
                  <div className="space-y-0">
                    {logs.length === 0 ? (
                      <p className="text-slate-500 text-sm text-center py-8">No activity yet</p>
                    ) : (
                      logs.map((log, i) => (
                        <div
                          key={i}
                          className="flex gap-3 py-1.5 border-b border-slate-800/50 last:border-0 font-mono text-xs"
                        >
                          <span className="text-slate-600 min-w-[65px]">{log.time}</span>
                          <span
                            className={`min-w-[40px] font-bold ${
                              log.type === 'info'
                                ? 'text-blue-400'
                                : log.type === 'ok'
                                ? 'text-emerald-400'
                                : log.type === 'err'
                                ? 'text-red-400'
                                : 'text-amber-400'
                            }`}
                          >
                            {log.type.toUpperCase()}
                          </span>
                          <span className="text-slate-400 flex-1 break-all">{log.message}</span>
                        </div>
                      ))
                    )}
                    <div ref={logEndRef} />
                  </div>
                </ScrollArea>
              </CardContent>
            </Card>
          )}
        </div>
      </main>
    </div>
  );
}
