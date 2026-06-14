/* More additions — public: patchwork-tax calculator, case study, status, help center.
   Exports PatchworkCalcPage, CaseStudyPage, StatusPage, HelpCenterPage. */
const X2D = window.FF_DATA;

/* ── The patchwork tax calculator ────────────────────────────── */
const PC_TOOLS = [
  { name: 'BambooHR', cat: 'HR', cost: '€499', domain: 'hr' },
  { name: 'Xero', cat: 'Accounting', cost: '€185', domain: 'finance' },
  { name: 'HubSpot Starter', cat: 'CRM', cost: '€368', domain: 'crm' },
  { name: 'Asana', cat: 'Projects', cost: '€299', domain: 'projects' },
  { name: 'Zapier', cat: 'Glue', cost: '€189', domain: null },
  { name: 'Freshdesk', cat: 'Support', cost: '€236', domain: 'support' },
];

function PatchworkCalcPage() {
  return (
    <div className="ff" data-screen-label="Patchwork tax calculator">
      <FFNav active="Pricing"></FFNav>
      <section className="ff-hero ff-grid-bg" style={{ paddingBottom: 64 }}>
        <div className="wrap">
          <span className="ff-kicker"><span className="sq"></span>The patchwork tax</span>
          <h1 style={{ maxWidth: 760 }}>What is your stack<br></br><span className="u">really</span> costing you?</h1>
          <p className="ff-lede">Add the tools you pay for today. We'll line them up against the FlowFlex modules that replace them — subscriptions and the hidden tax both.</p>
        </div>
      </section>
      <section className="ff-section" style={{ background: 'var(--card)' }}>
        <div className="wrap">
          <div style={{ display: 'grid', gridTemplateColumns: '1fr 400px', gap: 48, alignItems: 'start' }}>
            <div>
              <p className="ff-tag"><b>01</b> / YOUR TOOLS TODAY</p>
              <div style={{ marginTop: 24, border: '1px solid var(--line-strong)', borderRadius: 14, overflow: 'hidden' }}>
                {PC_TOOLS.map((t, i) => (
                  <div key={t.name} style={{ display: 'grid', gridTemplateColumns: '1fr 130px 110px 40px', gap: 14, alignItems: 'center', padding: '13px 22px', borderBottom: '1px solid var(--line)', background: i % 2 ? '#FAF9F5' : '#fff' }}>
                    <span style={{ fontWeight: 600, fontSize: 14.5 }}>{t.name}</span>
                    <span style={{ fontSize: 12.5, color: 'var(--ink-faint)', fontWeight: 500 }}>{t.cat}</span>
                    <span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 13, color: 'var(--ink-soft)', textAlign: 'right' }}>{t.cost}/mo</span>
                    <span style={{ color: 'var(--ink-faint)', textAlign: 'center', fontSize: 16 }}>×</span>
                  </div>
                ))}
                <div style={{ display: 'flex', alignItems: 'center', gap: 10, padding: '13px 22px', color: 'var(--ink-faint)', fontSize: 14, fontWeight: 500, borderTop: '1px dashed var(--line-strong)' }}>
                  <span style={{ width: 22, height: 22, borderRadius: 6, border: '1px dashed var(--line-strong)', display: 'inline-flex', alignItems: 'center', justifyContent: 'center', fontSize: 14 }}>+</span>
                  Add another tool…
                </div>
              </div>
              <div style={{ marginTop: 32 }}>
                <p className="ff-tag"><b>02</b> / THE HIDDEN PART</p>
                <div className="ff-cells" style={{ marginTop: 22 }}>
                  <div className="ff-cell" style={{ padding: '22px 22px' }}>
                    <span className="corner"></span>
                    <div className="big" style={{ fontSize: 30 }}>~6 h<em>/week</em></div>
                    <p style={{ marginTop: 6 }}>Re-typing the same data between systems, at 80 people.</p>
                  </div>
                  <div className="ff-cell" style={{ padding: '22px 22px' }}>
                    <span className="corner"></span>
                    <div className="big" style={{ fontSize: 30 }}>6×</div>
                    <p style={{ marginTop: 6 }}>Vendors to chase when something breaks between two tools.</p>
                  </div>
                  <div className="ff-cell" style={{ padding: '22px 22px' }}>
                    <span className="corner"></span>
                    <div className="big" style={{ fontSize: 30 }}>0</div>
                    <p style={{ marginTop: 6 }}>Reports that can join HR, sales and finance data today.</p>
                  </div>
                </div>
              </div>
            </div>
            <div className="ff-receipt">
              <div className="rt">SIDE BY SIDE · MONTHLY</div>
              <div style={{ height: 16 }}></div>
              <div className="rl head"><span>today</span><span></span></div>
              <div className="rl"><span>6 subscriptions</span><span>€1.776</span></div>
              <div className="rl"><span className="dim">+ sync glue (Zapier)</span><span className="dim">included above</span></div>
              <div style={{ height: 14 }}></div>
              <div className="rl head"><span>flowflex</span><span></span></div>
              <div className="rl"><span>11 modules × 80 users</span><span>€960</span></div>
              <div className="rl"><span className="dim">integrations needed</span><span className="dim">€0</span></div>
              <div className="rl total"><span>you keep</span><span style={{ color: '#0E8C61' }}>€816/mo</span></div>
              <div style={{ height: 8 }}></div>
              <div className="rl" style={{ justifyContent: 'center', fontSize: 11, color: 'var(--ink-faint)', whiteSpace: 'normal', textAlign: 'center' }}>≈ €9.792 a year, before the hidden part</div>
              <div style={{ height: 12 }}></div>
              <span className="ff-btn primary" style={{ width: '100%', boxSizing: 'border-box' }}>Get this as a PDF</span>
            </div>
          </div>
        </div>
      </section>
      <FFBand title="Numbers this good deserve a second opinion." sub="Send us your real stack — we'll do the mapping live on a 30-minute call." cta="Book the call"></FFBand>
      <FFFooter></FFFooter>
    </div>
  );
}

/* ── Case study ──────────────────────────────────────────────── */
function CaseStudyPage() {
  return (
    <div className="ff" data-screen-label="Case study">
      <FFNav active="About"></FFNav>
      <section className="ff-hero ff-grid-bg" style={{ paddingBottom: 72 }}>
        <div className="wrap">
          <div className="ff-crumb"><span>Customers</span><span>/</span><span className="here">Veldkamp Logistics</span></div>
          <h1 style={{ maxWidth: 880, marginTop: 24 }}>Nine tools became one platform.<br></br><span className="acc">The Tuesday copy-paste shift didn't survive.</span></h1>
          <div style={{ marginTop: 28, display: 'flex', gap: 28, flexWrap: 'wrap', fontFamily: "'JetBrains Mono', monospace", fontSize: 12, color: 'var(--ink-faint)' }}>
            <span style={{ whiteSpace: 'nowrap' }}>logistics · 127 people</span><span>·</span><span style={{ whiteSpace: 'nowrap' }}>switched over 4 months</span><span>·</span><span style={{ whiteSpace: 'nowrap' }}>9 tools replaced</span>
          </div>
        </div>
      </section>
      <section className="ff-section" style={{ background: 'var(--card)' }}>
        <div className="wrap">
          <div className="ff-cells">
            <div className="ff-cell">
              <span className="corner"></span>
              <div className="big">9 → 1</div>
              <h3>Tools consolidated</h3>
              <p>BambooHR, Xero, HubSpot, Asana, Harvest, Freshdesk, Zapier, TalentLMS and a shared drive.</p>
            </div>
            <div className="ff-cell">
              <span className="corner"></span>
              <div className="big">−46%</div>
              <h3>Monthly software spend</h3>
              <p>From €1.840 across nine invoices to €990 on one — at the same headcount.</p>
            </div>
            <div className="ff-cell">
              <span className="corner"></span>
              <div className="big">6 h<em>/week</em></div>
              <h3>Re-typing eliminated</h3>
              <p>One employee record, one customer record. The duplicate-entry shift simply stopped existing.</p>
            </div>
          </div>
        </div>
      </section>
      <section className="ff-section">
        <div className="wrap">
          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 72, alignItems: 'start' }}>
            <div>
              <p className="ff-tag"><b>01</b> / THE SWITCH</p>
              <h2 style={{ fontSize: 34 }}>Domain by domain, four months.</h2>
              <div style={{ marginTop: 36, borderLeft: '1px solid var(--line-strong)' }}>
                {[
                  ['Month 1', 'hr', 'HR & people live', 'Employee profiles + leave imported from BambooHR. First tool cancelled.'],
                  ['Month 2', 'finance', 'Finance live', 'Open invoices and balances from Xero. Deal-won → draft-invoice flow starts immediately.'],
                  ['Month 3', 'crm', 'CRM live', 'HubSpot pipeline mapped across. Support tickets begin feeding account health.'],
                  ['Month 4', 'projects', 'Projects + the rest', 'Asana boards and Harvest hours land. Zapier account deleted — nothing left to glue.'],
                ].map(([m, d, t, p]) => (
                  <div key={m} style={{ position: 'relative', padding: '16px 0 16px 32px' }}>
                    <span style={{ position: 'absolute', left: -6, top: 24, width: 11, height: 11, borderRadius: '50%', background: '#fff', border: '2px solid ' + X2D.domainColors[d], boxSizing: 'border-box' }}></span>
                    <span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 11, color: 'var(--ink-faint)' }}>{m.toUpperCase()}</span>
                    <h3 style={{ fontSize: 18, marginTop: 4 }}>{t}</h3>
                    <p style={{ marginTop: 5, fontSize: 14.5, lineHeight: 1.6, color: 'var(--ink-soft)' }}>{p}</p>
                  </div>
                ))}
              </div>
            </div>
            <div style={{ position: 'sticky', top: 40, display: 'flex', flexDirection: 'column', gap: 18 }}>
              <div style={{ background: 'var(--card)', border: '1px solid var(--line-strong)', borderRadius: 18, padding: '30px 32px', boxShadow: '0 1px 2px rgba(17,24,39,0.04)' }}>
                <p style={{ fontFamily: "'Archivo', sans-serif", fontSize: 22, lineHeight: 1.45, fontWeight: 600, letterSpacing: '-0.01em' }}>"I found the patchwork during an annual cost review. Thirteen logins, nine invoices, and nobody could tell me why. Now I have one number, and I can defend it."</p>
                <div style={{ marginTop: 20, display: 'flex', alignItems: 'center', gap: 12 }}>
                  <span style={{ width: 40, height: 40, borderRadius: '50%', background: '#6366F1', color: '#fff', display: 'flex', alignItems: 'center', justifyContent: 'center', fontWeight: 700, fontFamily: "'Archivo', sans-serif" }}>T</span>
                  <span>
                    <span style={{ fontWeight: 700, fontSize: 14.5, display: 'block' }}>Tom de Vries</span>
                    <span style={{ fontSize: 13, color: 'var(--ink-faint)' }}>Operations director, Veldkamp Logistics</span>
                  </span>
                </div>
              </div>
              <div style={{ background: 'var(--card)', border: '1px solid var(--line-strong)', borderRadius: 18, padding: '24px 28px' }}>
                <p className="ff-tag" style={{ letterSpacing: '0.18em' }}>RUNNING TODAY</p>
                <div style={{ marginTop: 14, display: 'flex', flexWrap: 'wrap', gap: 8 }}>
                  {[['hr', 'HR & people'], ['finance', 'Finance'], ['crm', 'CRM'], ['projects', 'Projects'], ['support', 'Support'], ['lms', 'Learning']].map(([k, n]) => (
                    <span key={k} className="ff-dompill" style={{ padding: '6px 14px', fontSize: 12.5 }}>
                      <span className="chip" style={{ background: X2D.domainColors[k] }}></span>{n}
                    </span>
                  ))}
                </div>
                <p className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 11.5, color: 'var(--ink-faint)', marginTop: 14 }}>14 modules · €9,90/user · 127 users</p>
              </div>
            </div>
          </div>
        </div>
      </section>
      <FFBand title="Your stack could be a story like this." sub="Map it on a 30-minute call — the plan and the price come out the other end." cta="Plan my switch"></FFBand>
      <FFFooter></FFFooter>
    </div>
  );
}

/* ── Status page ─────────────────────────────────────────────── */
function StBars({ bad }) {
  const bars = Array.from({ length: 60 }, (_, i) => bad !== undefined && i === bad);
  return (
    <span style={{ display: 'flex', gap: 2, alignItems: 'center' }}>
      {bars.map((isBad, i) => (
        <span key={i} style={{ width: 4, height: 18, borderRadius: 1.5, background: isBad ? '#F59E0B' : '#10B981', opacity: isBad ? 1 : 0.8 }}></span>
      ))}
    </span>
  );
}

function StatusPage() {
  const rows = [
    ['Core platform — auth, files, notifications', undefined, '100%'],
    ['HR & people', undefined, '100%'],
    ['Finance & accounting', 41, '99.98%'],
    ['CRM & sales', undefined, '100%'],
    ['Projects & work', undefined, '100%'],
    ['Support & help desk', undefined, '100%'],
  ];
  return (
    <div className="ff" data-screen-label="Status page">
      <div className="ff-nav-outer">
        <div className="wrap">
          <nav className="ff-nav">
            <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
              <FFLogo size={24}></FFLogo>
              <span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 11, letterSpacing: '0.16em', color: 'var(--ink-faint)' }}>STATUS</span>
            </div>
            <span className="ff-btn sm outline">Subscribe to updates</span>
          </nav>
        </div>
      </div>
      <section className="ff-section ff-grid-bg" style={{ padding: '64px 0' }}>
        <div className="wrap" style={{ maxWidth: 980 }}>
          <div style={{ display: 'flex', alignItems: 'center', gap: 16, background: 'var(--card)', border: '1px solid rgba(16,185,129,0.4)', borderRadius: 16, padding: '22px 28px', boxShadow: '0 1px 2px rgba(17,24,39,0.04)' }}>
            <span style={{ width: 14, height: 14, borderRadius: '50%', background: '#10B981', boxShadow: '0 0 0 5px rgba(16,185,129,0.15)' }}></span>
            <div>
              <h1 style={{ fontFamily: "'Archivo', sans-serif", fontSize: 24, letterSpacing: '-0.02em', margin: 0 }}>All systems flowing</h1>
              <p className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 11.5, color: 'var(--ink-faint)', marginTop: 4 }}>last checked 12 Jun 2026 · 09:41 CEST · EU-west</p>
            </div>
          </div>
          <div style={{ marginTop: 28, border: '1px solid var(--line-strong)', borderRadius: 16, background: 'var(--card)', overflow: 'hidden' }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', padding: '14px 24px', borderBottom: '1px solid var(--line)' }}>
              <span style={{ fontWeight: 700, fontFamily: "'Archivo', sans-serif", fontSize: 14, whiteSpace: 'nowrap' }}>Uptime by domain</span>
              <span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 11, color: 'var(--ink-faint)', whiteSpace: 'nowrap' }}>last 60 days</span>
            </div>
            {rows.map(([name, bad, pct]) => (
              <div key={name} style={{ display: 'grid', gridTemplateColumns: '1fr auto 70px', gap: 20, alignItems: 'center', padding: '13px 24px', borderBottom: '1px solid var(--line)' }}>
                <span style={{ fontSize: 14, fontWeight: 600, whiteSpace: 'nowrap' }}>{name}</span>
                <StBars bad={bad}></StBars>
                <span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 12, color: bad !== undefined ? '#B45309' : '#0E8C61', textAlign: 'right' }}>{pct}</span>
              </div>
            ))}
          </div>
          <div style={{ marginTop: 28 }}>
            <p className="ff-tag" style={{ letterSpacing: '0.18em' }}>PAST INCIDENTS</p>
            <div style={{ marginTop: 14, border: '1px solid var(--line-strong)', borderRadius: 14, background: 'var(--card)', padding: '18px 24px' }}>
              <div style={{ display: 'flex', alignItems: 'center', gap: 12, flexWrap: 'wrap' }}>
                <span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 11.5, color: 'var(--ink-faint)' }}>02 MAY 2026 · 14:10–14:52</span>
                <span style={{ fontSize: 11.5, fontWeight: 700, background: '#FDF1DC', color: '#B45309', borderRadius: 999, padding: '3px 10px' }}>DEGRADED · RESOLVED</span>
              </div>
              <h3 style={{ fontFamily: "'Archivo', sans-serif", fontSize: 16, marginTop: 10 }}>Slow invoice PDF generation in Finance</h3>
              <p style={{ marginTop: 6, fontSize: 14, lineHeight: 1.6, color: 'var(--ink-soft)' }}>PDF rendering queued behind a long export job. No data was lost; all queued PDFs were delivered by 15:05. Post-mortem and the queue isolation fix are linked below.</p>
              <span style={{ display: 'inline-block', marginTop: 10, fontSize: 13, fontWeight: 600, color: 'var(--indigo)' }}>Read the post-mortem →</span>
            </div>
            <p className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 12, color: 'var(--ink-faint)', marginTop: 18, textAlign: 'center' }}>that's the whole list for 2026</p>
          </div>
        </div>
      </section>
      <FFFooter></FFFooter>
    </div>
  );
}

/* ── Help center ─────────────────────────────────────────────── */
function HelpCenterPage() {
  const cats = [
    ['Getting started', null, 'Workspace setup, inviting your team, first modules', 12],
    ['Billing & modules', null, 'Switching modules on and off, invoices, user counts', 9],
    ['HR & people', 'hr', 'Profiles, leave, payroll runs, onboarding flows', 16],
    ['Finance', 'finance', 'Invoicing, expenses, exports to your accountant', 14],
    ['CRM & sales', 'crm', 'Contacts, pipeline, quotes that become invoices', 11],
    ['Account & security', null, '2FA, roles & permissions, GDPR requests, exports', 10],
  ];
  return (
    <div className="ff" data-screen-label="Help center">
      <FFNav></FFNav>
      <section className="ff-section ff-grid-bg" style={{ padding: '76px 0 64px', borderBottom: '1px solid var(--line)' }}>
        <div className="wrap" style={{ textAlign: 'center' }}>
          <span className="ff-kicker"><span className="sq"></span>Help center</span>
          <h1 style={{ fontFamily: "'Archivo', sans-serif", fontSize: 46, letterSpacing: '-0.03em', marginTop: 22 }}>How can we help?</h1>
          <div style={{ margin: '28px auto 0', maxWidth: 560, display: 'flex', alignItems: 'center', gap: 12, background: 'var(--card)', border: '1px solid var(--line-strong)', borderRadius: 14, padding: '16px 20px', boxShadow: '0 1px 2px rgba(17,24,39,0.04), 0 16px 32px -20px rgba(17,24,39,0.15)', textAlign: 'left' }}>
            <svg width="17" height="17" viewBox="0 0 16 16" fill="none" stroke="#98A0AB" strokeWidth="1.6" strokeLinecap="round"><circle cx="7" cy="7" r="4.5"></circle><path d="M10.5 10.5L14 14"></path></svg>
            <span style={{ fontSize: 15.5, color: 'var(--ink-faint)' }}>Search articles — "approve leave", "change modules"…</span>
          </div>
          <p className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 11.5, color: 'var(--ink-faint)', marginTop: 16 }}>72 articles · answered by humans within one business day if you're stuck</p>
        </div>
      </section>
      <section className="ff-section" style={{ background: 'var(--card)' }}>
        <div className="wrap">
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 14 }}>
            {cats.map(([name, domain, desc, count]) => (
              <div key={name} className="ff-tile" style={{ padding: 22 }}>
                <div className="top">
                  <span className="chip" style={{ background: domain ? X2D.domainColors[domain] : 'var(--indigo)' }}><span></span></span>
                  <span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 10.5, color: 'var(--ink-faint)' }}>{count} articles</span>
                </div>
                <div className="nm" style={{ fontSize: 15.5 }}>{name}</div>
                <p style={{ fontSize: 13.5, lineHeight: 1.55, color: 'var(--ink-soft)', marginTop: 6 }}>{desc}</p>
              </div>
            ))}
          </div>
          <div style={{ marginTop: 44 }}>
            <p className="ff-tag" style={{ letterSpacing: '0.18em' }}>MOST READ THIS WEEK</p>
            <div style={{ marginTop: 14, border: '1px solid var(--line-strong)', borderRadius: 14, overflow: 'hidden' }}>
              {[
                'What happens to our data when we switch a module off?',
                'Setting up approval chains for leave requests',
                'Why is my user count different from my headcount?',
                'Exporting everything — the full workspace export',
              ].map((q, i) => (
                <div key={q} style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', gap: 14, padding: '14px 22px', borderBottom: i < 3 ? '1px solid var(--line)' : 'none', background: i % 2 ? '#FAF9F5' : '#fff' }}>
                  <span style={{ fontSize: 14.5, fontWeight: 600 }}>{q}</span>
                  <span style={{ color: 'var(--indigo)', flex: 'none' }}>→</span>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>
      <FFBand title="Still stuck?" sub="A human replies within one business day — usually much faster." cta="Contact support"></FFBand>
      <FFFooter></FFFooter>
    </div>
  );
}

Object.assign(window, { PatchworkCalcPage, CaseStudyPage, StatusPage, HelpCenterPage });
