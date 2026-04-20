/**
 * WHOAMISEC AI - Tools SDK
 * TypeScript/Web/Android compatible tools interface
 * Supports all bypass tools from swarm-models/byp-all-tools/
 */

export interface Tool {
  id: string;
  name: string;
  file: string;
  icon: string;
  description?: string;
  category?: 'script' | 'panel' | 'reference';
  platform?: ('web' | 'android' | 'ts')[];
}

export const TOOLS: Tool[] = [
  { id: 'wormgpt-ultimate', name: 'wormgpt-ultimate', file: 'wormgpt-ultimate.py', icon: '🪱', category: 'script', platform: ['web', 'android', 'ts'] },
  { id: 'superbet', name: 'SuperBet', file: 'SuperBet.py', icon: '🎰', category: 'script', platform: ['web', 'android', 'ts'] },
  { id: 'w-destro', name: 'W-Destro', file: 'W-Destro.py', icon: '💥', category: 'script', platform: ['web', 'android', 'ts'] },
  { id: 'worm-money-machine', name: 'Worm-Money-Machine', file: 'Worm-Money-Machine.py', icon: '💸', category: 'script', platform: ['web', 'android', 'ts'] },
  { id: 'wormmoneyv3', name: 'WormMoneyV3', file: 'WormMoneyV3.py', icon: '🐛', category: 'script', platform: ['web', 'android', 'ts'] },
  { id: 'bot-bet-win', name: 'bot-Bet-Win', file: 'bot-Bet-Win.py', icon: '🤖', category: 'script', platform: ['web', 'android', 'ts'] },
  { id: 'whoamisec-arbitrary', name: 'WHOAMISec-Arbitrary', file: 'WHOAMISec-Arbitrary-Auto-make-money.py', icon: '⚡', category: 'script', platform: ['web', 'android', 'ts'] },
  { id: 'whoamisec-makermoney', name: 'WHOAMISec-MakerMoney', file: 'WHOAMISec-MakerMoney.py', icon: '💎', category: 'script', platform: ['web', 'android', 'ts'] },
  { id: 'whoamisec-superbet247', name: 'WHOAMISec-SuperBet247', file: 'WHOAMISec-SuperBet247.py', icon: '🎲', category: 'script', platform: ['web', 'android', 'ts'] },
  { id: 'whoamisecmakemoney', name: 'WHOAMISecMakeMoney', file: 'WHOAMISecMakeMoney.py', icon: '🪙', category: 'script', platform: ['web', 'android', 'ts'] },
  { id: 'worm-destruction', name: 'Worm-Destruction', file: 'Worm-Destruction.py', icon: '☠️', category: 'script', platform: ['web', 'android', 'ts'] },
  { id: 'adminpbuster', name: 'AdminPBuster', file: 'AdminPBuster.py', icon: '🔓', category: 'script', platform: ['web', 'android', 'ts'] },
  { id: 'magic-admin-paths', name: 'magic_admin_paths', file: 'magic_admin_paths.txt', icon: '🗺️', category: 'reference', platform: ['web', 'android', 'ts'] },
  { id: 'nyxddos', name: 'Nyxddos', file: 'Nyxddos.html', icon: '💀', category: 'panel', platform: ['web'] },
  { id: 'real-original-tools', name: 'Real-Original-tools', file: 'Real-Original-tools.txt', icon: '📜', category: 'reference', platform: ['web', 'android', 'ts'] },
];

export class WhoamisecToolsSDK {
  private baseUrl: string;

  constructor(baseUrl?: string) {
    this.baseUrl = baseUrl || '';
  }

  getTools(filter?: { category?: string; platform?: string }): Tool[] {
    let tools = TOOLS;
    if (filter?.category) tools = tools.filter(t => t.category === filter.category);
    if (filter?.platform) tools = tools.filter(t => t.platform?.includes(filter.platform as any));
    return tools;
  }

  getTool(id: string): Tool | undefined {
    return TOOLS.find(t => t.id === id);
  }

  async openTool(id: string, target: '_blank' | '_self' = '_blank'): Promise<void> {
    const tool = this.getTool(id);
    if (!tool) throw new Error(`Tool ${id} not found`);
    const url = `${this.baseUrl}/api/swarm/models?file=${encodeURIComponent(tool.file)}`;
    if (target === '_blank') window.open(url, '_blank');
    else window.location.href = url;
  }

  getToolUrl(id: string): string {
    const tool = this.getTool(id);
    if (!tool) throw new Error(`Tool ${id} not found`);
    return `${this.baseUrl}/api/swarm/models?file=${encodeURIComponent(tool.file)}`;
  }

  async downloadTool(id: string): Promise<Blob> {
    const tool = this.getTool(id);
    if (!tool) throw new Error(`Tool ${id} not found`);
    const url = this.getToolUrl(id);
    const res = await fetch(url);
    if (!res.ok) throw new Error(`Failed to download ${id}`);
    return res.blob();
  }
}

export const whoamisecTools = new WhoamisecToolsSDK();

export default whoamisecTools;
