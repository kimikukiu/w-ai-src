---
Task ID: 1
Agent: Super Z (main)
Task: Fix 502 API error auto-retry + Mobile chat UI optimization

Work Log:
- Analyzed existing 502 handling: backend (ai-engine.ts) has 3-tier fallback, API routes return `retry: true`, but frontend NEVER consumes the retry flag
- Added `fetchWithAutoRetry()` helper function that wraps fetch+safeJson with automatic retry on 502 errors (max 2 retries with exponential backoff: 1.5s, 3s)
- Replaced raw `fetch + safeJson` in both `sendGLM()` and `sendCoPilotGLM()` with `fetchWithAutoRetry()` 
- On retry, shows system message "⟳ Reîncercare X/2 — gateway 502..." in chat
- Co-Pilot retry also logs to terminal panel
- Made chat UI mobile-responsive:
  - Chat messages: `h-[50dvh] sm:h-[420px]` (dynamic viewport height on mobile)
  - Message padding: `p-2 sm:p-4` (compact on mobile)
  - Message font: `text-xs sm:text-sm` 
  - Message bubbles: `max-w-[85%/90%]` on mobile vs `max-w-[75%/80%]` on desktop
  - Chat header: `px-3 sm:px-5 py-2 sm:py-3` (compact padding on mobile)
  - Co-pilot mode buttons: `hidden sm:flex` (hidden on very small screens)
  - Feature buttons (Security Audit, Code Review, etc.): `hidden md:flex` (hidden on mobile)
  - Agent Settings card: `hidden lg:block` (desktop only)
  - Mode toggle labels: `hidden sm:inline` (icons only on mobile)
  - Input action buttons: compact with `whitespace-nowrap` and `overflow-x-auto`
  - Terminal: `h-[100px] sm:h-[160px]` (shorter on mobile)
  - File upload buttons: emoji-only on mobile (📎, 📷, ⬛), full labels on desktop
- Verified HMR compilation successful

Stage Summary:
- 502 auto-retry: Frontend now automatically retries up to 2 times with exponential backoff when API returns `retry: true`
- Mobile UI: Chat window is now compact and responsive, using dvh units and responsive Tailwind classes
- No existing code was deleted — only added new code and responsive modifiers
