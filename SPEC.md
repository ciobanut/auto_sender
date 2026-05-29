# AI Recruitment Automation Dashboard — Rabota.md

> **Spec v1** — Product design document.
> Role: Senior Product Designer + Full-Stack Architect.

---

## Table of Contents

1. [Product Vision](#1-product-vision)
2. [User Flow Overview](#2-user-flow-overview)
3. [Database Schema](#3-database-schema)
4. [Dashboard Layout & UI](#4-dashboard-layout--ui)
5. [Core Modules](#5-core-modules)
6. [Pipeline Workflow](#6-pipeline-workflow)
7. [AI Prompts Architecture](#7-ai-prompts-architecture)
8. [Queue & Job Architecture](#8-queue--job-architecture)
9. [Automations & Scheduler](#9-automations--scheduler)
10. [Rate Limiting & Cooldowns](#10-rate-limiting--cooldowns)
11. [Analytics Engine](#11-analytics-engine)
12. [Visual Design Direction](#12-visual-design-direction)
13. [Mobile Responsiveness](#13-mobile-responsiveness)
14. [Component Tree](#14-component-tree)
15. [Route Map](#15-route-map)
16. [Implementation Phases](#16-implementation-phases)

---

## 1. Product Vision

A single-page pipeline dashboard that transforms the chaotic job-hunting workflow into a calm, AI-powered automation flow. Designed for job seekers on Rabota.md who want to apply at scale while maintaining quality and personalisation.

**Core promise:** Set your keywords, upload your CVs, configure AI rules — and let the system fetch, analyze, write, and send applications while you review and approve.

**Key differentiators from a CRUD admin panel:**
- Pipeline-first UX, not table-first
- AI explainability (show _why_ a match happened)
- Repost intelligence (detect stale listings and follow up differently)
- Human-in-the-loop at every decision point
- Analytics that feed back into strategy (which CV works best, which keywords perform)

---

## 2. User Flow Overview

```
ONBOARDING
  └─ Create account → Set keywords → Upload CVs → Configure AI rules
       │
       ▼
DAILY LOOP
  1. FETCH JOBS ────→ Scrape Rabota.md job listings for each keyword
  2. ANALYZE ───────→ Extract full details, detect reposts, classify
  3. GENERATE ──────→ AI writes personalized cover letters per job+CV
  4. REVIEW ────────→ Human reviews, edits, approves in queue
  5. SEND ──────────→ Submit application via email/form, log result
       │
       ▼
  6. TRACK ─────────→ Monitor replies, interviews, follow-ups
```

---

## 3. Database Schema

### 3.1 `job_keywords`

Stores search keywords/categories with per-keyword configuration.

| Column | Type | Description |
|---|---|---|
| `id` | id | Primary key |
| `user_id` | FK -> users | Owner |
| `keyword` | string | e.g. "PHP", "Laravel" |
| `cv_path` | string | Path to uploaded CV file |
| `ai_instructions` | text | Custom AI instructions for this keyword |
| `auto_apply_enabled` | boolean | Toggle auto-apply |
| `cooldown_hours` | integer | Hours between re-applications |
| `is_active` | boolean | Soft enable/disable |
| `sort_order` | integer | Display ordering |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

**Indexes:** `(user_id, keyword)` unique, `(user_id, is_active)`

### 3.2 `job_links`

Raw scraped job listings from Rabota.md.

| Column | Type | Description |
|---|---|---|
| `id` | id | |
| `keyword_id` | FK -> job_keywords | Originating keyword |
| `job_url` | string | Unique URL on Rabota.md |
| `external_job_id` | string | Rabota.md internal ID if extractable |
| `title` | string | Job title |
| `company_name` | string | Company name |
| `location` | string | Location text |
| `short_preview` | text | Description snippet |
| `status` | enum | `new`, `re_fetched`, `processed`, `ignored` |
| `fetch_count` | integer | Times this link has been fetched |
| `first_seen_at` | datetime | First discovery |
| `re_fetched_at` | datetime | Last re-fetch |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

**Indexes:** `job_url` unique, `(keyword_id, status)`, `(status, fetch_count)`

**Duplicate detection on fetch:**
- `job_url` unique → insert fails → increment `fetch_count`, update `re_fetched_at`, set `status = re_fetched`

### 3.3 `job_details`

Full enriched data after analyzing a job page.

| Column | Type | Description |
|---|---|---|
| `id` | id | |
| `job_link_id` | FK -> job_links | Parent link |
| `full_description` | text | Complete job description |
| `technologies` | json | Extracted tech stack array |
| `salary_from` | integer | Minimum salary |
| `salary_to` | integer | Maximum salary |
| `salary_currency` | string | MDL/EUR/USD |
| `company_name` | string | Normalised |
| `contact_email` | string | Recruiter/company email |
| `recruiter_name` | string | |
| `phone` | string | |
| `requirements` | json | Structured requirements |
| `responsibilities` | json | Structured responsibilities |
| `seniority` | string | Junior/Middle/Senior/Lead |
| `work_type` | string | remote/hybrid/office |
| `publication_date` | datetime | From job page |
| `reposted` | boolean | True if similarity detected |
| `repost_count` | integer | Times reposted |
| `reposted_after_days` | integer | Days since first seen |
| `similarity_hash` | string | Content fingerprint |
| `similarity_score` | float | % similarity to previous |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

**Indexes:** `job_link_id` unique, `(reposted, repost_count)`, `similarity_hash`

**Repost detection logic:**
```php
// On analyze:
$previous = JobDetail::whereHas('jobLink', fn($q) => $q->whereCompanyName($company))
    ->whereSimilarityHash($hash)
    ->first();

if ($previous) {
    $current->reposted = true;
    $current->repost_count = $previous->repost_count + 1;
    $current->reposted_after_days = $firstSeen->diffInDays(now());
}
```

Similarity hash can be computed as:
- `md5(normalize_title + company_name)` for exact
- Or use `md5(sentence_embedding_quantized)` for semantic

### 3.4 `cover_letters`

AI-generated cover letters.

| Column | Type | Description |
|---|---|---|
| `id` | id | |
| `job_link_id` | FK -> job_links | |
| `job_detail_id` | FK -> job_details | |
| `keyword_id` | FK -> job_keywords | |
| `content` | text | Generated letter text |
| `version` | integer | 1 = first, 2 = follow-up |
| `is_follow_up` | boolean | True if repost follow-up |
| `ai_model` | string | Model used (gpt-4o-mini etc.) |
| `ai_confidence_score` | float | 0-1 AI confidence |
| `match_explanation` | text | Why candidate matches |
| `extra_skills_injected` | json | Extra skills added by AI |
| `editable_content` | text | User-editable version |
| `status` | enum | `draft`, `edited`, `approved`, `sent` |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

**Indexes:** `(job_link_id, version)` unique, `(keyword_id, status)`

### 3.5 `applications`

Sent applications tracking.

| Column | Type | Description |
|---|---|---|
| `id` | id | |
| `job_link_id` | FK -> job_links | |
| `cover_letter_id` | FK -> cover_letters | |
| `keyword_id` | FK -> job_keywords | |
| `sent_at` | datetime | When sent |
| `delivery_status` | enum | `pending`, `delivered`, `failed`, `bounced` |
| `response_received` | boolean | |
| `response_at` | datetime | |
| `response_type` | enum | `rejected`, `interview`, `no_reply` |
| `recruiter_reply_text` | text | |
| `follow_up_sent` | boolean | |
| `follow_up_at` | datetime | |
| `notes` | text | User notes |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

**Indexes:** `(job_link_id, user_id)` unique, `(keyword_id, response_type)`

### 3.6 `extra_skills`

Technologies NOT in CV that the AI can inject.

| Column | Type | Description |
|---|---|---|
| `id` | id | |
| `user_id` | FK -> users | |
| `name` | string | e.g. "Docker" |
| `category` | string | backend/frontend/devops/other |
| `proficiency` | enum | `beginner`, `intermediate`, `advanced` |
| `sort_order` | integer | |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

**Indexes:** `(user_id, name)` unique

### 3.7 `ai_settings`

Per-user AI configuration.

| Column | Type | Description |
|---|---|---|
| `id` | id | |
| `user_id` | FK -> users | Unique |
| `model` | string | Default: `gpt-4o-mini` |
| `temperature` | float | Default: 0.7 |
| `max_tokens` | integer | Default: 500 |
| `language` | string | Default: `ro` (Romanian) |
| `tone` | string | `professional`, `friendly`, `enthusiastic` |
| `signature_block` | text | Default email signature |
| `default_instructions` | text | Global AI instructions |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

### 3.8 `analytics_events`

Immutable audit log + analytics events.

| Column | Type | Description |
|---|---|---|
| `id` | id | |
| `user_id` | FK -> users | |
| `event_type` | string | `jobs_fetched`, `application_sent`, `interview_received`, etc. |
| `payload` | json | Arbitrary event data |
| `created_at` | timestamp | |

**Indexes:** `(user_id, event_type, created_at)`

### 3.9 `cooldown_rules`

Override cooldown rules per company or keyword.

| Column | Type | Description |
|---|---|---|
| `id` | id | |
| `user_id` | FK -> users | |
| `keyword_id` | FK -> job_keywords | nullable |
| `company_domain` | string | nullable, e.g. "*@company.md" |
| `cooldown_hours` | integer | |
| `max_applications` | integer | Per period |
| `period_hours` | integer | Rolling window |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

## 4. Dashboard Layout & UI

### 4.1 Global Layout

```
┌──────────────────────────────────────────────────────┐
│  Header: Logo | Pipeline Progress (5 steps) | Avatar │
├────────┬─────────────────────────────────────────────┤
│        │                                              │
│ LEFT   │  MAIN CONTENT AREA                           │
│ SIDEBAR│  (Switches between pipeline stages,          │
│        │   or shows focused views)                    │
│ ─────  │                                              │
│        │                                              │
│ 📋 Cat.│                                              │
│ 📄 CVs │                                              │
│ ⚡ Extra│                                              │
│ 🤖 AI  │                                              │
│ ⏱ Rules│                                              │
│ 📊 An. │                                              │
│        │                                              │
└────────┴──────────────────────────────────────────────┘
```

**Left Sidebar (collapsible, 240px → 64px icon-only):**

1. **Job Categories** — Active keywords with badges (new jobs count)
2. **CV Manager** — Upload/manage CVs per keyword
3. **Extra Skills** — Editable list of skills not in CV
4. **AI Settings** — Model, tone, language, instructions
5. **Sending Rules** — Cooldown config, safe mode toggle
6. **Analytics** — Stats dashboard

### 4.2 Pipeline Progress Bar

Sticky top of main content area (below header):

```
│ ◉ Fetch Jobs  │ ◉ Analyze  │ ◉ Generate  │ ◉ Review  │ ◉ Send  │
│   23 new       │   12 ready  │    8 drafts  │  5 queued  │  3 sent │
│   ✓ done      │   ⟳ retry  │             │  ⚠ 2 need  │         │
│               │             │             │   review   │         │
```

States per step: `idle` | `processing` | `completed` | `error` | `partial`

Clicking a step filters the main content to that stage.

### 4.3 Jobs Table (Main Content)

Smart data table with:

```
┌───┬──────────────────────┬──────────┬────────┬──────┬───────┬───────┐
│ # │ Job Title + Company  │ Match ⭐ │ Status │ Tech │ Age   │ Actions│
├───┼──────────────────────┼──────────┼────────┼──────┼───────┼───────┤
│ 1 │ Senior PHP Dev       │ 94%      │ ● New  │ PHP, │ 2d    │ [▶]   │
│   │ TechCorp, Chisinau   │          │        │ Lar  │       │       │
│   │                      │          │        │      │       │       │
│ 2 │ React Developer      │ 78%      │ ◷ Repst│ React│ 8d 🔔│ [▶]   │
│   │ WebStudio, Remote    │          │        │ JS   │       │       │
└───┴──────────────────────┴──────────┴────────┴──────┴───────┴───────┘
```

**Status badges:**
- `● New` — green, unprocessed
- `◷ Repost` — amber/orange with repost count
- `✓ Applied` — blue
- `✉ Replied` — purple
- `★ Interview` — emerald
- `✕ Ignored` — grey
- `⚠ Error` — red

**Row actions:** Generate Message | Preview | Edit & Send | Ignore | Add Note

### 4.4 AI Match Score Column

Hover reveals breakdown card:

```
┌─────────────────────┐
│ ⭐ 94% Match          │
│                      │
│ ✓ Laravel experience  │
│ ✓ Livewire found      │
│ ✓ Remote position     │
│ ✓ Docker preferred    │
│ ✗ Kubernetes missing  │
└─────────────────────┘
```

### 4.5 Repost Warning Card

Inline highlight on reposted jobs:

```
┌─── ⚠ Repost Detected ─────────────────────────────────┐
│ This company reposted the job after 8 days.             │
│ They likely haven't found the right candidate yet.      │
│ [Generate Follow-up Message]  [Send Again]              │
└─────────────────────────────────────────────────────────┘
```

---

## 5. Core Modules

### 5.1 Job Keywords Manager

- Sortable list of keywords with drag-to-reorder
- Per-keyword: CV upload (PDF/DOCX), AI instructions textarea, auto-apply toggle, cooldown slider
- Bulk import keywords
- Quick enable/disable toggle

### 5.2 CV Manager

- Upload CV per keyword (PDF, DOCX, plain text)
- Auto-convert to text for AI processing
- Preview CV content
- Version history
- "Default CV" fallback option

### 5.3 Extra Skills Manager

- Simple tag-style input
- Categorised (Backend / Frontend / DevOps / Other)
- Proficiency level per skill
- Skills that get selectively injected into follow-up messages

### 5.4 AI Settings Panel

- Model selection dropdown (gpt-4o-mini, gpt-4o, claude-3-haiku, etc.)
- Temperature slider
- Max tokens
- Language preference (Romanian / English / Russian)
- Tone selection
- Default signature block
- Global AI instructions
- Token usage & cost tracker

### 5.5 Sending Rules

- Default cooldown (hours between re-applications to same company)
- Per-keyword overrides
- Per-company overrides
- Max applications per day/week
- Safe mode: require manual review before send
- Auto-apply toggle per keyword
- "Skip if already applied" toggle

### 5.6 Timeline Panel

Per-company/job timeline view:

```
TechCorp — Senior PHP Developer
─────────────────────────────────
● 12 May — First seen
● 14 May — CV sent
● 18 May — Job reposted
● 19 May — Follow-up sent
● 22 May — Interview invitation
● 25 May — Interview completed
```

---

## 6. Pipeline Workflow

### Step 1: Fetch Jobs

**Trigger:** Manual ("Fetch Jobs") or scheduled (Cron)

**Action:** Scrape `https://www.rabota.md/ro/jobs-moldova-{keyword}` per active keyword.

**Extraction rules:**
- CSS selectors for job listing cards
- Extract: URL, title, company, location, preview, date
- Deduplicate by `job_url`

**Error handling:**
- Network failures → queue retry (3 attempts, exponential backoff)
- Rate limiting → respect `Retry-After` header
- Empty results → log and notify
- Parse failures → log raw HTML for debugging

**UI feedback:** Progress bar per keyword, live count of new vs duplicates found.

### Step 2: Analyze Jobs

**Trigger:** Manual ("Analyze New Jobs") or auto after fetch

**Action:** For each `new` or `re_fetched` job:
1. Open individual job page
2. Extract full description, salary, technologies, contact info, work type, etc.
3. Run similarity detection against existing records
4. Mark reposts

**Extraction:**
- Parse full HTML description
- Extract technologies via keyword matching or AI
- Normalise company name
- Detect work type from text patterns

**Similarity detection:**
```python
# Approach: hybrid text similarity
1. Normalise title (lowercase, strip punctuation)
2. Normalise company name
3. Compute Jaccard similarity on description tokens
4. If similarity > 85% → repost
```

**UI feedback:** Progress bar, detected reposts highlighted in real-time.

### Step 3: Generate AI Messages

**Trigger:** Manual ("Generate AI Messages") or auto after analysis

**Action:** For each job needing a cover letter:
1. Select CV by `keyword_id`
2. Gather: job description, CV text, extra skills, AI instructions
3. Determine if first-time or follow-up (repost)
4. Send to LLM via queue
5. Store result with confidence score and match explanation

**UI feedback:** Streaming generation queue, drafts ready counter, error highlights.

### Step 4: Review Applications

**Trigger:** Manual review

**Action:** Browse generated cover letters in a review queue.

**Features:**
- Inline editor for each message
- Preview pane showing job + CV + message side-by-side
- Approve / Reject / Edit buttons
- Batch approve selected
- "Send After Review" mode

**UI layout:**

```
┌─────────────┬──────────────────────┬──────────────────┐
│ JOB PANEL   │ COVER LETTER EDITOR  │ CV PREVIEW       │
│             │                      │                  │
│ Title       │ [Editable textarea]   │ [CV content]     │
│ Company     │                      │                  │
│ Match: 94%  │                      │                  │
│ Tech stack  │ Confidence: 0.92     │                  │
│ Description │ [Approve] [Edit] [X] │                  │
└─────────────┴──────────────────────┴──────────────────┘
```

### Step 5: Send Applications

**Trigger:** Manual ("Send") or automatic

**Action:** For each approved application:
1. Prepare email or direct message
2. Attach CV
3. Attach cover letter as text or PDF
4. Send via mail (SMTP or job portal's messaging)
5. Log delivery status
6. Send analytics event

**Delivery methods (v1 vs future):**
- v1: Email via SMTP (if email extracted)
- v2: Direct messaging through Rabota.md (browser automation with Playwright)

**UI feedback:** Sent confirmation, delivery status, error reporting.

---

## 7. AI Prompts Architecture

### 7.1 First-Time Cover Letter Prompt

```
SYSTEM: You are a professional cover letter writer. Write in {{language}}.
Use a {{tone}} tone. Keep it concise (max {{max_tokens}} tokens).

CONTEXT:
Job Description:
{{job_description}}

Technologies Required:
{{technologies}}

Candidate CV:
{{cv_text}}

Additional Skills (only mention if relevant):
{{extra_skills}}

User Instructions:
{{custom_instructions}}

RULES:
- Do not invent experience the candidate doesn't have
- Be specific about why this candidate fits THIS job
- No generic filler sentences
- Mention 1-2 specific technologies from the job description
- Keep it to 3-4 short paragraphs

OUTPUT FORMAT:
{
  "cover_letter": "text",
  "confidence_score": 0.0-1.0,
  "match_reasons": ["reason1", "reason2", ...],
  "matched_technologies": ["tech1", "tech2"]
}
```

### 7.2 Follow-Up / Repost Prompt

```
SYSTEM: You are writing a FOLLOW-UP cover letter.
The candidate ALREADY applied to this job previously.
Write in {{language}}. Use {{tone}} tone. Keep it concise.

CONTEXT:
Job Description:
{{job_description}}

Technologies Required:
{{technologies}}

Candidate CV:
{{cv_text}}

Previously Sent Message:
{{previous_cover_letter}}

NEW Extra Skills (not in original CV, acquired since):
{{extra_skills}}

User Instructions:
{{custom_instructions}}

RULES:
- Reference the previous application politely
- Acknowledge no response was received (do not sound bitter)
- Express continued interest
- Mention 1-2 NEW skills/experiences not in the original application
- Keep it to 2-3 short paragraphs
- Do not apologize for re-applying

OUTPUT FORMAT:
{
  "cover_letter": "text",
  "confidence_score": 0.0-1.0,
  "new_skills_highlighted": ["skill1", "skill2"],
  "match_reasons": ["reason1", ...]
}
```

### 7.3 Technology Extraction Prompt

```
SYSTEM: Extract all technologies, frameworks, and tools mentioned
in this job description. Categorize them.

Job Description:
{{text}}

OUTPUT FORMAT:
{
  "technologies": ["PHP", "Laravel", ...],
  "seniority": "Middle" | "Senior" | "Lead",
  "work_type": "remote" | "hybrid" | "office",
  "salary_mentioned": true | false,
  "salary_from": number | null,
  "salary_to": number | null
}
```

### 7.4 Caching Strategy

- Cache AI responses by `md5(job_description + cv_text + instructions)`
- Cache TTL: 7 days (or until job is reposted)
- Cache key includes keyword_id and version (first/follow-up)
- Bust cache when CV or instructions change

---

## 8. Queue & Job Architecture

### 8.1 Jobs

| Job Class | Queue | Description | Retries | Timeout |
|---|---|---|---|---|
| `FetchKeywordJobs` | `scraping` | Scrape one keyword's listing page | 3 | 120s |
| `AnalyzeSingleJob` | `scraping` | Extract full details from one job page | 3 | 60s |
| `DetectSimilarity` | `analysis` | Run similarity check | 2 | 30s |
| `GenerateCoverLetter` | `ai` | LLM call for one cover letter | 3 | 120s |
| `SendApplication` | `sending` | Send CV + letter | 3 | 60s |
| `CheckApplicationStatus` | `sending` | Poll for reply | 5 (daily) | 30s |
| `SendFollowUp` | `sending` | Follow-up after N days | 3 | 60s |

### 8.2 Queue Architecture

```php
// config/queue.php
'connections' => [
    'rabbitmq' => [ // or database for simplicity
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
    ],
],

// Define queues:
// - scraping:     High concurrency, job fetching
// - analysis:     Lower concurrency, CPU-heavy
// - ai:           Rate-limited, API key sensitive
// - sending:      Sequential per-company to avoid spam flags
```

### 8.3 Chaining

```php
// Fetch all keywords →
$keywords->each(fn ($kw) =>
    FetchKeywordJobs::dispatch($kw)->chain([
        new AnalyzeNewJobs($kw),     // After fetch completes
    ])
);

// AnalyzeNewJobs internally chains per-job analysis →
// → GenerateCoverLetter →
// → SendApplication (if auto-apply enabled)
```

### 8.4 Rate Limiting

```php
// Per-key rate limiting for AI API
Redis::throttle('ai-api')
    ->allow(10)     // 10 requests
    ->every(60)     // per 60 seconds
    ->then(fn() => dispatch, fn() => $this->release(30));

// Per-job-site rate limiting
Redis::throttle('rabota-md')
    ->allow(20)
    ->every(60)
    ->then(...);
```

### 8.5 Failure Handling

- Failed jobs go to `failed_jobs` table → visible in dashboard with error message
- Manual "Retry" button in dashboard per failed job
- Batch retry for all failed in a stage
- Fail after N retries → notification to user

---

## 9. Automations & Scheduler

### 9.1 Scheduled Commands

| Frequency | Command | Description |
|---|---|---|
| Every 6h | `auto-sender:fetch-jobs` | Fetch new jobs for all active keywords |
| Every 6h | `auto-sender:analyze-new` | Analyze unprocessed jobs |
| Every 12h | `auto-sender:generate-messages` | Generate cover letters for analyzed jobs |
| Hourly | `auto-sender:send-approved` | Send approved applications |
| Daily | `auto-sender:check-followups` | Check for replies, schedule follow-ups |
| Daily | `auto-sender:cleanup` | Prune old logs, rotate cache |

### 9.2 Conditional Automations

- If `auto_apply_enabled` is on for a keyword → after generation, auto-send if confidence > threshold (e.g., 85%)
- If safe mode is on → only generate, never auto-send
- If cooldown active for company → skip

### 9.3 Notifications

- Browser notification (via Livewire events)
- Email summary: "23 new jobs, 8 analyzed, 5 messages ready for review"
- In-dashboard notification bell

---

## 10. Rate Limiting & Cooldowns

### 10.1 Scraping Rate Limits

- Max 1 request per 2 seconds to Rabota.md
- Configurable delay between requests
- Respect `robots.txt`
- User-agent rotation (optional)

### 10.2 Application Cooldowns

- Default: 30 days before re-applying to same company
- Per-keyword override
- Per-company override (company domain based)
- Track by: `normalized_company_email_domain` or `normalized_company_name`
- Exception: reposts bypass cooldown (with user approval)

### 10.3 AI API Rate Limits

- Per-user: 10 RPM, 10,000 TPM (configurable)
- Queue-based with auto-throttle
- Cost tracking per user

---

## 11. Analytics Engine

### 11.1 Metrics Tracked

| Metric | Source | Aggregation |
|---|---|---|
| Jobs fetched per day | `analytics_events` | Count |
| Applications sent | `applications` | Count |
| Reply rate | `applications.response_received` | % |
| Interview rate | `applications.response_type = 'interview'` | % |
| Best CV by reply rate | Group by `keyword_id` | % |
| Best technology | Group by `job_details.technologies` | Count |
| Companies that repost most | Group by `company_name` | Count |
| AI confidence distribution | `cover_letters.ai_confidence_score` | Avg |
| Average time to reply | `applications` | Hours |
| Cooldown effectiveness | `applications` | Comparison |

### 11.2 Visualisations

- **Funnel chart:** Jobs Fetched → Analyzed → Generated → Sent → Replied → Interview
- **Line chart:** Applications over time (daily/weekly)
- **Pie chart:** Response distribution (no reply / rejected / interview)
- **Bar chart:** Best performing keywords
- **Table:** Companies that repost most frequently

### 11.3 Analytics Page Layout

```
┌──────────────────────────────────────────────────────────┐
│ 📊 Analytics Dashboard                                    │
│                                                            │
│ ┌──────┬──────┬──────┬──────┬──────┐                      │
│ │ 1,247│   342│   156│    23│   12%│                      │
│ │ Jobs │ Apps │Replie│Interv│  Rate│                      │
│ └──────┴──────┴──────┴──────┴──────┘                      │
│                                                            │
│ ┌─── Funnel Chart ────────────────────────────────────┐   │
│ │                                                     │   │
│ │  1247 ━━━━━━━━━━━━━━━━━━━━━━━━                      │   │
│ │   523 ━━━━━━━━━━━━                                 │   │
│ │   342 ━━━━━━━━━                                    │   │
│ │   298 ━━━━━━━━                                     │   │
│ │   156 ━━━━━                                         │   │
│ │    23 ━━━                                           │   │
│ └─────────────────────────────────────────────────────┘   │
│                                                            │
│ ┌─── Best CVs ──────────────┬─── Top Technologies ────┐   │
│ │ Keyword    │ Reply Rate   │ Tech        │ Count     │   │
│ │ Laravel    │ 18%          │ Laravel     │ 89        │   │
│ │ React      │ 14%          │ React       │ 67        │   │
│ │ PHP        │ 11%          │ Docker      │ 45        │   │
│ └───────────────────────────┴─────────────────────────┘   │
└──────────────────────────────────────────────────────────┘
```

---

## 12. Visual Design Direction

### 12.1 Design System

**Color palette (dark-first, but supports light):**

| Token | Dark | Light | Usage |
|---|---|---|---|
| `--bg-primary` | `#0A0A0A` | `#F8F8F6` | Page background |
| `--bg-surface` | `#141416` | `#FFFFFF` | Card/surface |
| `--bg-elevated` | `#1C1C1E` | `#F3F3F1` | Sidebar, nav |
| `--border` | `#2A2A2D` | `#E5E5E3` | Borders |
| `--text-primary` | `#F5F5F3` | `#1A1A18` | Main text |
| `--text-secondary` | `#8B8B90` | `#6B6B70` | Supporting |
| `--accent` | `#6366F1` | `#4F46E5` | Primary actions |
| `--accent-soft` | `rgba(99,102,241,0.12)` | same | Subtle bg |
| `--success` | `#22C55E` | `#16A34A` | Success |
| `--warning` | `#F59E0B` | `#D97706` | Warning/repost |
| `--danger` | `#EF4444` | `#DC2626` | Error |

**Typography:**
- UI: Inter (sans-serif), system font stack fallback
- Code/monospace: JetBrains Mono (for technical details)
- Scale: 12 / 14 / 16 / 18 / 24 / 30 / 36 px

**Spacing:**
- 4px grid base
- Card padding: 20px
- Section gap: 24px
- Sidebar width: 260px → collapsed 64px

**Corners:**
- Cards: 12px
- Buttons/inputs: 8px
- Badges: 6px
- Pills: 9999px

### 12.2 Aesthetic Principles

1. **Dark-first but not gamer**: Subtle dark theme with warm greys, not pure black. Accessible contrast ratios throughout.
2. **Density with breathing room**: Dense data (job tables) but generous whitespace between sections.
3. **Glassy surfaces**: Cards with subtle backdrop blur and soft inner borders for depth.
4. **Monochromatic with accent sparingly**: Indigo/purple accent only for primary actions; status colors carry semantic meaning.
5. **Micro-animations**: Staggered list reveals, smooth pipeline transitions, subtle hover lifts.
6. **Data as texture**: Charts and stats are visual elements, not afterthoughts.

### 12.3 Component Styling (using existing stack)

Since the project uses **Mary UI + daisyUI v5 + Tailwind v4**:

```blade
<!-- Example: job card component -->
<x-card class="border border-base-300 bg-base-100 shadow-sm hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between gap-4">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <h3 class="font-semibold text-base-content truncate">{{ $job->title }}</h3>
                @if($job->details?->reposted)
                    <span class="badge badge-warning badge-sm gap-1">
                        <x-icon name="tabler.refresh" class="w-3 h-3" />
                        Reposted {{ $job->details->repost_count }}x
                    </span>
                @endif
            </div>
            <p class="text-sm text-base-content/60 mt-0.5">{{ $job->company_name }}</p>
            <div class="flex items-center gap-3 mt-2 text-xs text-base-content/50">
                <span>{{ $job->location }}</span>
                <span>•</span>
                <span>{{ $job->created_at->diffForHumans() }}</span>
            </div>
        </div>

        <!-- AI Match Score -->
        <div class="flex-shrink-0 text-right">
            <div class="radial-progress text-primary"
                 style="--value: {{ $job->coverLetter?->ai_confidence_score * 100 ?? 0 }}; --size: 3rem;">
                {{ $job->coverLetter?->ai_confidence_score ? ($job->coverLetter->ai_confidence_score * 100) . '%' : '—' }}
            </div>
        </div>
    </div>
</x-card>
```

---

## 13. Mobile Responsiveness

### 13.1 Breakpoints

| Breakpoint | Layout |
|---|---|
| < 768px (mobile) | Single column, sidebar as drawer, pipeline as horizontal scroll |
| 768–1024px (tablet) | Sidebar collapsed by default, 2-column grid |
| > 1024px (desktop) | Full 3-column layout with sidebar |

### 13.2 Mobile Adaptations

- **Sidebar** → Bottom tab bar with icons
- **Pipeline steps** → Horizontal scrollable tabs
- **Job table** → Card list (each job row becomes a card)
- **Review pane** → Stacked vertically instead of side-by-side
- **AI editor** → Full-screen modal

---

## 14. Component Tree

```
AppLayout
├── Header
│   ├── Logo / App Brand
│   ├── PipelineProgress (5 steps with counts)
│   └── UserDropdown (avatar, settings, logout)
├── Sidebar
│   ├── SidebarNavItem (Job Categories)
│   ├── SidebarNavItem (CV Manager)
│   ├── SidebarNavItem (Extra Skills)
│   ├── SidebarNavItem (AI Settings)
│   ├── SidebarNavItem (Sending Rules)
│   └── SidebarNavItem (Analytics)
└── MainContent (slot)
    │
    ├── JobsPipeline (stage = fetch)
    │   ├── FetchJobsPanel
    │   │   ├── FetchButton
    │   │   └── FetchProgress (per-keyword progress)
    │   └── JobLinksTable
    │       ├── JobLinkRow
    │       │   ├── StatusBadge
    │       │   ├── MatchScoreBadge
    │       │   ├── RepostIndicator
    │       │   └── RowActions (dropdown)
    │       └── BulkActionsBar
    │
    ├── JobsPipeline (stage = analyze)
    │   ├── AnalyzeButton
    │   ├── AnalysisProgress
    │   └── JobDetailsTable
    │
    ├── JobsPipeline (stage = generate)
    │   ├── GenerateButton
    │   ├── GenerationProgress
    │   └── CoverLetterList
    │       ├── CoverLetterCard
    │       │   ├── JobInfoPanel
    │       │   ├── LetterEditor (editable textarea)
    │       │   ├── MatchExplanationCard
    │       │   └── ApprovalActions (approve/edit/ignore)
    │       └── BulkApproveBar
    │
    ├── JobsPipeline (stage = review)
    │   └── ReviewQueue
    │       └── ReviewSplitPane
    │           ├── JobPreview
    │           ├── LetterEditor
    │           └── CvPreview
    │
    ├── JobsPipeline (stage = send)
    │   ├── SendButton
    │   ├── SendProgress
    │   └── ApplicationLog
    │
    ├── SidebarView (Job Categories)
    │   ├── KeywordList (sortable)
    │   │   └── KeywordCard
    │   │       ├── CvUpload
    │   │       ├── AiInstructions
    │   │       ├── AutoApplyToggle
    │   │       └── CooldownSlider
    │   └── AddKeywordForm
    │
    ├── SidebarView (CV Manager)
    │   ├── CvList
    │   └── CvUploadForm
    │
    ├── SidebarView (Extra Skills)
    │   ├── SkillTagInput (categorized)
    │   └── SkillList
    │
    ├── SidebarView (AI Settings)
    │   ├── ModelSelector
    │   ├── TemperatureSlider
    │   ├── ToneSelector
    │   ├── LanguageSelector
    │   ├── InstructionsTextarea
    │   └── CostDisplay
    │
    ├── SidebarView (Sending Rules)
    │   ├── DefaultCooldown
    │   ├── PerKeywordOverrides
    │   └── PerCompanyOverrides
    │
    └── SidebarView (Analytics)
        ├── StatsRow (4 metric cards)
        ├── FunnelChart
        ├── BestCvsTable
        ├── TopTechnologiesTable
        ├── RepostingCompaniesTable
        └── TimelineChart
```

---

## 15. Route Map

### 15.1 Web Routes (Livewire)

```php
// Authenticated + verified
Route::middleware(['auth', 'verified'])->prefix('app')->group(function () {
    // Main dashboard — pipeline root
    Route::get('/dashboard', App\Livewire\Dashboard\Index::class)->name('dashboard');

    // Pipeline stages (same component, different stage parameter)
    Route::get('/pipeline/{stage?}', App\Livewire\Pipeline\Index::class)
        ->whereIn('stage', ['fetch', 'analyze', 'generate', 'review', 'send'])
        ->name('pipeline');

    // Sidebar full-page views
    Route::get('/keywords', App\Livewire\Keywords\Index::class)->name('keywords');
    Route::get('/cvs', App\Livewire\Cvs\Index::class)->name('cvs');
    Route::get('/skills', App\Livewire\Skills\Index::class)->name('skills');
    Route::get('/ai-settings', App\Livewire\AiSettings\Index::class)->name('ai-settings');
    Route::get('/rules', App\Livewire\Rules\Index::class)->name('rules');
    Route::get('/analytics', App\Livewire\Analytics\Index::class)->name('analytics');

    // Actions (Livewire component methods or dedicated endpoints)
    Route::post('/jobs/fetch', [App\Http\Controllers\JobController::class, 'fetch'])->name('jobs.fetch');
    Route::post('/jobs/analyze', [App\Http\Controllers\JobController::class, 'analyze'])->name('jobs.analyze');
    Route::post('/messages/generate', [App\Http\Controllers\MessageController::class, 'generate'])->name('messages.generate');
    Route::post('/applications/send', [App\Http\Controllers\ApplicationController::class, 'send'])->name('applications.send');
});
```

### 15.2 Livewire Component Architecture

The UI is designed as a **single-page reactive app** using Livewire v4 full-page components:

- **`Pipeline/Index`** — The primary view. Contains the pipeline progress bar and conditionally renders sub-components based on the active stage. Uses Livewire's `$refresh` for polling during active jobs.

- Each sidebar view is a **full-page component** (e.g., `Keywords/Index`, `Cvs/Index`).

- Heavy operations (fetch, analyze, generate) dispatch **background jobs** and use Laravel's broadcasting or polling to update progress.

---

## 16. Implementation Phases

### Phase 1 — Foundation (Week 1-2)

- [x] Laravel app setup (done)
- [ ] Create migrations for all tables
- [ ] Create models with relationships
- [ ] Create Filament/Livewire keyword manager
- [ ] CV upload & storage
- [ ] Extra skills CRUD
- [ ] Basic dashboard layout with sidebar

### Phase 2 — Scraping Engine (Week 3-4)

- [ ] Rabota.md scraper service
- [ ] FetchKeywordJobs job
- [ ] Job links table population
- [ ] Duplicate detection
- [ ] AnalyzeSingleJob job
- [ ] Similarity/repost detection
- [ ] Queue monitoring UI

### Phase 3 — AI Integration (Week 5-6)

- [ ] OpenAI API integration
- [ ] Cover letter generation prompt pipeline
- [ ] First-time + follow-up prompt variants
- [ ] AI settings management
- [ ] Cover letter storage and editing
- [ ] Match explanation display
- [ ] AI generation caching

### Phase 4 — Sending & Tracking (Week 7-8)

- [ ] Application sending subsystem
- [ ] Email delivery (SMTP integration)
- [ ] Application status tracking
- [ ] Timeline panel
- [ ] Follow-up scheduling
- [ ] Cooldown enforcement

### Phase 5 — Analytics & Polish (Week 9-10)

- [ ] Analytics engine
- [ ] Dashboard charts and visualisations
- [ ] Reporting (daily/weekly summaries)
- [ ] Bulk actions
- [ ] Performance optimization
- [ ] Mobile responsiveness
- [ ] Testing coverage

---

## Key Architectural Decisions

| Decision | Choice | Rationale |
|---|---|---|
| Frontend framework | Livewire 4 | Leverages existing project stack, server-rendered, no SPA complexity |
| Styling | Mary UI + daisyUI v5 + Tailwind v4 | Already in project, covers all needed components |
| Queue driver | Database (v1) → Redis (v2) | Start simple, upgrade when needed |
| Scraping | Laravel HTTP Client (v1), Playwright (v2) | HTTP client for static HTML, Playwright for JS-rendered pages later |
| AI provider | OpenAI API | Market leader, reliable, well-documented |
| Caching | Database (v1) → Redis (v2) | Simple cache table for AI responses initially |
| Search/filter | Laravel Scout (database driver) | Full-text search on job descriptions |
| File storage | Local (v1) → S3 (v2) | CV files stored locally initially |
| Notifications | Livewire events + Mail | Real-time in-app, email summaries |
