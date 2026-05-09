---
tags: [flowflex, design, ai, conversational-ui, phase/6]
domain: Design System
status: planned
last_updated: 2026-05-08
---

# AI & Conversational UI

Design guidelines for all AI-powered features in FlowFlex. Chat interfaces, streaming text, loading states, empty states with AI suggestions, and how AI surfaces itself without being annoying.

---

## Core Principles

- **AI should feel inevitable, not intrusive** — appear when helpful, stay quiet otherwise
- **Transparency always** — the user must always know they're talking to AI, and what it's doing
- **Confidence communicates** — show certainty level, never bluff
- **Reversible by default** — AI actions should be undoable. Always show what the AI is about to do before it does it
- **Progressive disclosure** — suggest, don't overwhelm. One primary suggestion, with "show more" option

---

## AI Surfaces in FlowFlex

| Surface | Where Used | Interaction Pattern |
|---|---|---|
| Command bar (⌘K) | Global | Type → natural language → action |
| Sidebar chat panel | All panels | Persistent conversation |
| Inline field suggestions | Forms, editors | Ghost text on cursor |
| Proactive insight cards | Dashboard | Dismiss or act |
| AI action confirmation modal | Before writes | Show what will happen → confirm |
| AI-generated content preview | Documents, emails | Show draft → edit or send |
| Smart empty states | List views | "No tasks yet. Want AI to suggest some?" |

---

## Chat Interface Design

### Message Layout

```
┌─────────────────────────────────────────┐
│ [Avatar] User message here              │
│         12:34 PM                        │
│                                         │
│         ┌───────────────────────────┐   │
│         │ AI response here.         │   │
│         │                           │   │
│         │ • Bullet point one        │   │
│         │ • Bullet point two        │   │
│         │                           │   │
│         │ [👍] [👎] [Copy]          │   │
│         └───────────────────────────┘   │
│                          12:34 PM   AI  │
└─────────────────────────────────────────┘
```

- User messages: right-aligned (or left with user avatar)
- AI messages: left-aligned, `ocean-50` background, `slate-700` text
- AI avatar: FlowFlex `F` mark in `ocean-500` circle
- Timestamps: `text-caption`, `slate-400`
- Feedback row (thumbs up/down + copy): show on hover, fade in with `opacity` transition 200ms

### Streaming Text

- Text streams character-by-character (Server-Sent Events)
- Cursor indicator: pulsing `|` at stream end, `ocean-500` colour, `1s` blink animation
- Never show partial markdown — buffer until complete block rendered
- Code blocks: show skeleton frame first, then render when block complete
- Smooth scroll: auto-scroll chat to bottom as content streams in

### Input Area

```css
/* Chat input */
.ai-chat-input {
  border: 1px solid var(--slate-300);
  border-radius: 12px;
  padding: 12px 16px;
  min-height: 48px;
  max-height: 200px;
  resize: vertical;
  font-size: 14px;
  line-height: 1.6;
}

.ai-chat-input:focus {
  border-color: var(--ocean-500);
  box-shadow: 0 0 0 3px rgba(33, 153, 200, 0.1);
}
```

- Send button: `ocean-500` background, `↑` arrow icon, only active when text present
- Keyboard: Enter to send, Shift+Enter for new line
- Placeholder: "Ask anything about your business..."
- Character counter: show when > 500 characters

---

## Loading & Thinking States

### Thinking Indicator (before first token streams)

Three dot animation:
```css
.ai-thinking {
  display: flex;
  gap: 4px;
  align-items: center;
  padding: 12px 16px;
}

.ai-thinking-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--ocean-400);
  animation: ai-pulse 1.2s ease-in-out infinite;
}

.ai-thinking-dot:nth-child(2) { animation-delay: 0.2s; }
.ai-thinking-dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes ai-pulse {
  0%, 80%, 100% { opacity: 0.3; transform: scale(0.8); }
  40% { opacity: 1; transform: scale(1); }
}
```

### Skeleton Loading (AI-generated list/table)

- Show skeleton rows while AI query executes
- Skeleton width: vary between 40%–85% (natural feel)
- Animation: left-to-right shimmer, 1.5s, `ocean-50` → `ocean-100` → `ocean-50`

### Progress States for Longer Operations

- For AI operations >2 seconds: show progress label
- Example: "Reading 247 transactions..." → "Analysing patterns..." → "Done"
- Progress steps in `text-body-sm`, `slate-500`

---

## AI Action Confirmation Modal

Before any AI write action:

```
┌────────────────────────────────────┐
│ ⚡ AI Action Confirmation           │
│                                    │
│ About to create the following:     │
│                                    │
│ ✅ Task: "Follow up with Acme Corp" │
│    Assigned to: You                │
│    Due date: Friday, 9 May         │
│    Project: Q2 Outreach            │
│                                    │
│ [Cancel]          [Confirm & Create]│
└────────────────────────────────────┘
```

- Modal width: 480px max
- Always show exactly what will happen in plain language
- Destructive actions: show in `danger-500`, require explicit confirmation
- Batch actions: show count + sample ("Create 3 tasks like these...")

---

## Inline Field Suggestions

### Ghost Text Pattern

```css
.ai-ghost-text {
  color: var(--slate-400);
  pointer-events: none;
  position: absolute;
  /* positioned to align exactly with cursor */
}
```

- Appears 400ms after user stops typing
- Tab key accepts the full suggestion
- Right arrow accepts one word at a time
- Escape dismisses
- Never appears when user is in the middle of a word (only at end of sentence/line)

### Suggested Tags / Labels

- Appear as chips below input field
- `ocean-100` background, `ocean-700` text, `×` to dismiss
- Click chip to accept into field
- Max 3 suggestions shown at once

---

## Proactive Insight Cards

Cards that appear on dashboards when AI detects something worth knowing:

```
┌────────────────────────────────────────┐
│ 💡  AI Insight                    [×] │
│                                        │
│ 3 invoices became overdue overnight    │
│ totalling €12,400. Oldest: Acme Corp  │
│ (€8,000 — 7 days overdue).            │
│                                        │
│ [View invoices →]  [Remind all]        │
└────────────────────────────────────────┘
```

- Background: `ocean-50`
- Border: `1px solid ocean-200`
- Border-radius: `12px`
- Icon: `sparkles` from Heroicons, `ocean-500`
- CTA buttons: one primary (action), one secondary (view)
- Dismiss `×`: `slate-400`, hover `slate-600`
- Max 3 insight cards visible on any dashboard — older ones archived
- Each card type can be permanently dismissed ("Don't show this type again")

---

## Empty States with AI

When a list is empty, AI can help:

```
┌────────────────────────────────────────┐
│                                        │
│         [🔮 Sparkles icon]             │
│                                        │
│         No tasks yet                   │
│                                        │
│  Start by creating a task, or let AI   │
│  suggest some based on this project.   │
│                                        │
│  [+ Add task]  [Ask AI to suggest]     │
│                                        │
└────────────────────────────────────────┘
```

- Icon: `sparkles` or domain-relevant icon, `slate-300` colour, 48px
- Heading: `text-h4`, `slate-700`
- Body: `text-body`, `slate-500`
- Primary CTA: manual creation
- Secondary CTA: AI assistance (ghost button style)
- AI suggestion opens inline mini-chat or generates and shows draft items

---

## AI Badge & Attribution

When content is AI-generated:
- Small badge: `✨ AI-generated` in `text-caption`, `ocean-600`, `ocean-50` background
- Shown on: drafted emails, generated summaries, auto-created tasks
- Click badge: "This was generated by FlowFlex AI. Review before using."
- Badge not shown on AI-assisted (spell check, suggestions) — only fully AI-generated

---

## Colour & Tokens for AI

| Token | Value | Usage |
|---|---|---|
| `ai-primary` | `ocean-500` `#2199C8` | AI CTAs, icons |
| `ai-surface` | `ocean-50` `#EBF8FD` | AI message backgrounds, insight cards |
| `ai-border` | `ocean-200` `#AADFF3` | AI card borders |
| `ai-ghost` | `slate-400` `#9CA3AF` | Inline ghost text |
| `ai-badge-bg` | `ocean-50` | AI attribution badge |
| `ai-badge-text` | `ocean-700` `#135F7F` | AI attribution badge text |

---

## Accessibility

- All AI loading states: `aria-busy="true"` on container, `aria-live="polite"` for status updates
- Streaming text: `aria-live="polite"` region with debounced updates (not every character)
- Ghost text: `aria-label="AI suggestion: [text]"`, `aria-hidden="true"` for visual element
- AI confirmation modals: focus trap, focus moves to first interactive element on open
- Insight card dismiss: keyboard accessible, `aria-label="Dismiss insight: [title]"`
- Reduced motion: disable shimmer animations, disable streaming character-by-character (show complete response)

---

## Related

- [[Component Library]]
- [[Motion & Animation]]
- [[AI Assistant & Copilot]]
- [[AI Infrastructure]]
- [[Writing Style & Voice]]
