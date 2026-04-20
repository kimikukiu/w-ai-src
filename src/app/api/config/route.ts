import { NextRequest, NextResponse } from 'next/server';
import { loadConfig, saveConfig, maskSecret } from '@/lib/config';
import { emitModelChange, emitEndpointChange, emitConfigUpdate } from '@/lib/sync-bus';
import type { HermesConfig } from '@/lib/config';

const SECRET_FIELDS = ['glm_api_key', 'telegram_token'];

export async function GET() {
  try {
    const config = loadConfig();
    const masked: Record<string, any> = { ...config };
    for (const field of SECRET_FIELDS) {
      if (masked[field]) {
        masked[field] = maskSecret(masked[field]);
      }
    }
    return NextResponse.json({ config: masked });
  } catch (error) {
    return NextResponse.json(
      { error: 'Failed to load configuration' },
      { status: 500 }
    );
  }
}

export async function POST(request: NextRequest) {
  try {
    const body = await request.json();
    const currentConfig = loadConfig();

    const updatedConfig: HermesConfig = {
      ...currentConfig,
      ...body,
    };

    saveConfig(updatedConfig);

    if (body.glm_model) emitModelChange('web', body.glm_model);
    if (body.glm_endpoint) emitEndpointChange('web', body.glm_endpoint);
    emitConfigUpdate('web', body);

    const masked: Record<string, any> = { ...updatedConfig };
    for (const field of SECRET_FIELDS) {
      if (masked[field]) {
        masked[field] = maskSecret(masked[field]);
      }
    }

    return NextResponse.json({
      success: true,
      message: 'Configuration updated',
      config: masked,
    });
  } catch {
    return NextResponse.json(
      { error: 'Failed to save configuration' },
      { status: 500 }
    );
  }
}
