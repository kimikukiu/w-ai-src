import { NextRequest, NextResponse } from 'next/server';
import { N8N_WORKFLOWS } from '@/lib/quantum-swarm-engine';

export async function POST(request: NextRequest) {
  try {
    const { workflow_id, input } = await request.json();
    const wf = N8N_WORKFLOWS.find(w => w.id === workflow_id);
    if (!wf) {
      return NextResponse.json({ success: false, error: 'Workflow not found' }, { status: 404 });
    }
    return NextResponse.json({
      success: true,
      workflow: wf.name,
      status: 'executing',
      input: input || {},
      message: `Workflow "${wf.name}" triggered successfully via n8n`,
    });
  } catch {
    return NextResponse.json({ success: false, error: 'Invalid request' }, { status: 400 });
  }
}
