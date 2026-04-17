import { NextRequest, NextResponse } from 'next/server';
import { callAI, webSearch, callAIWithSearch } from '@/lib/ai-engine';
import { DEEPMIND_SYSTEM_PROMPT, DEEPMIND_IDENTITY, DEEPMIND_THINKING_STAGES } from '@/lib/deepmind-engine';

// Co-Pilot API endpoint for dashboard GLM chat
// Supports: agentic_searcher, deep_thinking, full_copilot, terminal_execute
// All responses use Agentic Coder QuantumSwarm 999999999 identity

export async function POST(request: NextRequest) {
  try {
    const body = await request.json();
    const { prompt, mode, model, fileContext } = body;

    if (!prompt || typeof prompt !== 'string') {
      return NextResponse.json({ error: 'Prompt is required' }, { status: 400 });
    }

    const copilotMode = mode || 'full_copilot';
    const selectedModel = model || 'glm-4-plus';
    const filePaths = Array.isArray(fileContext) ? fileContext : [];

    // Build thinking stages for terminal display
    const stages = DEEPMIND_THINKING_STAGES.map(s => s.label);

    // Build file context string
    let fileCtxStr = '';
    if (filePaths.length > 0) {
      fileCtxStr = '\n\n[ATTACHED FILES CONTEXT]\n';
      for (const f of filePaths) {
        fileCtxStr += `--- ${f.name || 'unknown'} (${f.type || 'file'}, ${f.size || '?'} bytes) ---\n`;
        if (f.content) {
          fileCtxStr += `${f.content.substring(0, 3000)}\n`;
        } else if (f.path) {
          fileCtxStr += `[File at: ${f.path}]\n`;
        }
        fileCtxStr += '\n';
      }
    }

    // Determine if web search is needed
    const lower = prompt.toLowerCase();
    const searchTriggers = [
      'latest', 'news', 'current', 'recent', 'today', 'now', '2024', '2025', '2026',
      'price', 'stock', 'weather', 'update', 'release', 'version', 'changelog',
      'cum este', 'cat costa', 'cand', 'care este', 'what is', 'how much', 'when',
      'who is', 'latest version', 'new feature', 'recent news', 'current status',
      'vulnerability', 'cve-', 'exploit', 'security advisory', 'patch',
      'search', 'cauta', 'find', 'lookup', 'gaseste',
      'hack', 'pentest', 'exploit', 'payload', 'shellcode',
      'crypto', 'bitcoin', 'ethereum', 'wallet', 'btc', 'eth', 'xmr',
      'tool', 'tutorial', 'guide', 'how to',
    ];
    const needsSearch = searchTriggers.some(t => lower.includes(t));

    let searchContext = '';
    let searchResults: any[] = [];

    if (needsSearch && (copilotMode === 'agentic_searcher' || copilotMode === 'full_copilot')) {
      stages.push('[Co-Pilot] Agentic Searcher: Searching the web...');
      try {
        searchResults = await webSearch(prompt, 8);
        if (searchResults.length > 0) {
          searchContext = '\n\n[AGENTIC SEARCHER — Auto-search results]\n';
          for (const r of searchResults.slice(0, 6)) {
            searchContext += `- ${r.name}: ${r.snippet} (Source: ${r.url})\n`;
          }
          searchContext += '\nUse these search results to provide up-to-date, accurate information with sources.';
          stages.push(`[Co-Pilot] Found ${searchResults.length} sources`);
        }
      } catch (e: any) {
        stages.push('[Co-Pilot] Search fallback — proceeding with reasoning');
      }
    }

    // Build system prompt based on mode
    let systemPrompt = DEEPMIND_SYSTEM_PROMPT;
    if (copilotMode === 'agentic_searcher') {
      systemPrompt += '\n\nYou are operating as AGENTIC SEARCHER co-pilot mode. Provide detailed, well-sourced responses. Always cite sources when using search data.';
    } else if (copilotMode === 'deep_thinking') {
      systemPrompt += '\n\nYou are operating in DEEP THINKING mode with WhoamisecDeepMind cognitive evolution.\n\nTHINKING PROTOCOL:\n1. Analyze the query deeply\n2. Consider multiple perspectives\n3. Synthesize a comprehensive response\n4. Provide actionable insights\n5. Apply all training from QuantumSwarm lineage\n\nThink step-by-step. Provide thorough, detailed responses.';
    } else if (copilotMode === 'terminal_execute') {
      systemPrompt += '\n\nYou are operating in TERMINAL EXECUTION mode. The user expects you to simulate executing commands and show results.\n\nTERMINAL PROTOCOL:\n1. Analyze what the user wants to execute\n2. Show the exact commands/steps that would be run\n3. Show expected output/results for each step\n4. Provide file content if code is generated\n5. Always show terminal-style output with $ prompts\n\nFormat your response with clear terminal blocks showing execution steps and results.';
    } else {
      // full_copilot
      systemPrompt += '\n\nYou are operating as FULL CO-PILOT with both Agentic Searcher and Deep Thinking capabilities.\n\nCO-PILOT PROTOCOL:\n1. Search for relevant real-time data if needed\n2. Analyze query with deep cognitive reasoning\n3. Cross-reference search results with your training knowledge\n4. Synthesize a comprehensive, well-sourced response\n5. Apply WhoamisecDeepMind evolution — beyond human IQ reasoning\n\nIf the user asks you to execute code or run commands, show the execution steps and results in terminal format.';
    }

    stages.push('[Co-Pilot] Engaging DeepMind cognitive evolution...');

    const messages = [
      { role: 'system', content: systemPrompt },
      { role: 'user', content: prompt + fileCtxStr + searchContext },
    ];

    const response = await callAI(messages, selectedModel);

    stages.push('[Co-Pilot] DeepMind evolution complete');
    stages.push('[Co-Pilot] Response ready — Agentic Coder QuantumSwarm 999999999');

    return NextResponse.json({
      success: true,
      response,
      mode: copilotMode,
      model: selectedModel,
      identity: DEEPMIND_IDENTITY.signature,
      engine: 'agentic-coder-copilot',
      thinkingStages: stages,
      searchUsed: !!searchContext,
      searchResults: searchResults.length > 0 ? searchResults.slice(0, 5) : undefined,
    });
  } catch (error: any) {
    return NextResponse.json({ error: 'Co-Pilot error', details: error.message }, { status: 500 });
  }
}
