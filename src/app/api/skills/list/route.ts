import { NextResponse } from 'next/server';
import { ALL_SKILLS, SKILL_CATEGORIES } from '@/lib/quantum-swarm-engine';

export async function GET() {
  return NextResponse.json({
    success: true,
    categories: SKILL_CATEGORIES,
    skills: ALL_SKILLS,
    total_skills: ALL_SKILLS.length,
    total_categories: SKILL_CATEGORIES.length,
    active_skills: ALL_SKILLS.filter(s => s.status === 'active').length,
  });
}
