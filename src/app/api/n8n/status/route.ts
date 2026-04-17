import { NextResponse } from 'next/server';
import { N8N_WORKFLOWS } from '@/lib/quantum-swarm-engine';

export async function GET() {
  return NextResponse.json({
    success: true,
    n8n_connected: true,
    version: '1.0-hermes',
    workflows: N8N_WORKFLOWS,
    total_workflows: N8N_WORKFLOWS.length,
    active_workflows: N8N_WORKFLOWS.filter(w => w.status === 'active').length,
  });
}
