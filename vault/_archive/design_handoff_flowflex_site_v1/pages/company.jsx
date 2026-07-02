/* About + Contact + Legal pages. Exports AboutDesktop, ContactDesktop, LegalDesktop. */
const CMD = window.FF_DATA;

/* ── About ───────────────────────────────────────────────────── */
const AB_VALUES = [
  { name: 'Flexible by design', detail: 'Activate modules one at a time; pay only for what is active.' },
  { name: 'Flow-first UX', detail: 'Speed and clarity on every screen. Nothing blocks the actual job.' },
  { name: 'Unified truth', detail: 'One database, one login. Data flows between departments automatically.' },
  { name: 'Transparent and fair', detail: 'Clear pricing, GDPR-compliant, your data is portable — always.' },
  { name: 'Customer-first', detail: 'Built for department managers and employees, not the IT department.' },
];

const AB_ISNOT = [
  { is: 'A modular platform you grow into', not: 'An ERP that imposes a process model' },
  { is: 'Built for 50 and for 500 people', not: 'Enterprise-only software' },
  { is: 'Focused — every addition serves Flow or Flex', not: 'A feature factory' },
];

function AboutDesktop() {
  return (
    <div className="ff" data-screen-label="About desktop">
      <FFNav active="About"></FFNav>
      <section className="ff-hero ff-grid-bg" style={{ paddingBottom: 76 }}>
        <div className="wrap">
          <span className="ff-kicker"><span className="sq"></span>About</span>
          <h1 style={{ maxWidth: 880, fontSize: 56 }}>Growing companies shouldn't need <span className="u">fifteen tools</span> to run one business.</h1>
          <div style={{ marginTop: 36, maxWidth: 640, display: 'flex', flexDirection: 'column', gap: 20, fontSize: 17.5, lineHeight: 1.7, color: 'var(--ink-soft)' }}>
            <p>FlowFlex started with that frustration. Somewhere between 40 and 80 employees, every company hits the same wall: the cost of switching, syncing and re-entering data across a patchwork of tools quietly outgrows the cost of the tools themselves.</p>
            <p>So we built the alternative — one platform where every operational tool shares one data model, one login and one bill. Modules switch on when you need them and off when you don't. Built in Europe, hosted in Europe, GDPR-compliant from the first line of code.</p>
          </div>
        </div>
      </section>
      <section className="ff-section" style={{ background: 'var(--card)' }}>
        <div className="wrap">
          <p className="ff-tag"><b>01</b> / VALUES</p>
          <h2>What we hold ourselves to.</h2>
          <div className="ff-cells" style={{ gridTemplateColumns: 'repeat(3, 1fr)' }}>
            {AB_VALUES.map((v) => (
              <div className="ff-cell" key={v.name}>
                <span className="corner"></span>
                <h3 style={{ marginTop: 0 }}>{v.name}</h3>
                <p>{v.detail}</p>
              </div>
            ))}
            <div className="ff-cell" style={{ display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
              <FFLogo size={30}></FFLogo>
            </div>
          </div>
        </div>
      </section>
      <section className="ff-section">
        <div className="wrap">
          <p className="ff-tag"><b>02</b> / IS / ISN'T</p>
          <h2>What FlowFlex is — and isn't.</h2>
          <div style={{ marginTop: 44, borderTop: '1px solid var(--line)', borderBottom: '1px solid var(--line)' }}>
            {AB_ISNOT.map((row) => (
              <div key={row.is} style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 24, padding: '22px 0', borderBottom: '1px solid var(--line)' }}>
                <p style={{ fontWeight: 600, fontSize: 16, display: 'flex', gap: 12, alignItems: 'baseline' }}>
                  <span className="ff-sw on sm" style={{ position: 'relative', top: 4 }}></span>{row.is}
                </p>
                <p style={{ color: 'var(--ink-faint)', fontSize: 15.5, textDecoration: 'line-through', textDecorationColor: 'rgba(152,160,171,0.5)' }}>{row.not}</p>
              </div>
            ))}
          </div>
        </div>
      </section>
      <section className="ff-section ff-grid-bg" style={{ padding: '64px 0' }}>
        <div className="wrap" style={{ display: 'flex', flexWrap: 'wrap', justifyContent: 'center', gap: 36, fontFamily: "'JetBrains Mono', monospace", fontSize: 12.5, color: 'var(--ink-soft)' }}>
          <span>EU-hosted</span><span style={{ color: 'var(--line-strong)' }}>·</span>
          <span>GDPR-compliant, DSAR built in</span><span style={{ color: 'var(--line-strong)' }}>·</span>
          <span>Two-factor authentication</span><span style={{ color: 'var(--line-strong)' }}>·</span>
          <span>Full audit log</span><span style={{ color: 'var(--line-strong)' }}>·</span>
          <span>Export your data any day</span>
        </div>
      </section>
      <FFBand title="Talk to the team." sub="Questions about modules, pricing or switching from your current stack — we reply within one business day." cta="Contact us"></FFBand>
      <FFFooter></FFFooter>
    </div>
  );
}

/* ── Contact ─────────────────────────────────────────────────── */
function CField({ label, optional, hint, children }) {
  return (
    <label className="ff-field">
      <span className="lbl"><span>{label}</span>{optional && <span className="opt">optional</span>}</span>
      {children}
      {hint && <span style={{ display: 'block', marginTop: 6, fontSize: 12.5, color: 'var(--ink-faint)' }}>{hint}</span>}
    </label>
  );
}

function ContactDesktop() {
  return (
    <div className="ff" data-screen-label="Contact desktop">
      <FFNav active="Contact"></FFNav>
      <section className="ff-section ff-grid-bg" style={{ borderBottom: 'none', padding: '84px 0 104px' }}>
        <div className="wrap">
          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1.15fr', gap: 72, alignItems: 'start' }}>
            <div>
              <span className="ff-kicker"><span className="sq"></span>Contact</span>
              <h1 style={{ fontSize: 54, marginTop: 24, lineHeight: 1.03, letterSpacing: '-0.03em', fontFamily: "'Archivo', sans-serif", fontWeight: 700 }}>Talk to us.</h1>
              <p className="ff-lede" style={{ maxWidth: 380 }}>Questions about modules, pricing or moving off your current stack — we reply within one business day.</p>
              <div style={{ marginTop: 40, display: 'flex', flexDirection: 'column', gap: 14 }}>
                <div style={{ background: 'var(--card)', border: '1px solid var(--line-strong)', borderRadius: 14, padding: '22px 24px', position: 'relative' }}>
                  <span style={{ position: 'absolute', top: -1, left: -1, width: 14, height: 14, borderTop: '2px solid var(--indigo)', borderLeft: '2px solid var(--indigo)' }}></span>
                  <h3 style={{ fontSize: 15 }}>Considering a switch?</h3>
                  <p style={{ marginTop: 6, fontSize: 14, lineHeight: 1.6, color: 'var(--ink-soft)' }}>Tell us which tools you run today. We'll map them to modules and give you a real monthly number — usually the same day.</p>
                </div>
                <div style={{ background: 'var(--card)', border: '1px solid var(--line-strong)', borderRadius: 14, padding: '22px 24px' }}>
                  <h3 style={{ fontSize: 15 }}>Already a customer?</h3>
                  <p style={{ marginTop: 6, fontSize: 14, lineHeight: 1.6, color: 'var(--ink-soft)' }}>Sign in and reach support from your workspace — it's faster.</p>
                </div>
              </div>
            </div>
            <div style={{ background: 'var(--card)', border: '1px solid var(--line-strong)', borderRadius: 20, padding: 40, boxShadow: '0 1px 2px rgba(17,24,39,0.04), 0 28px 56px -32px rgba(17,24,39,0.18)', display: 'flex', flexDirection: 'column', gap: 22 }}>
              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 18 }}>
                <CField label="Name"><span className="ff-input"><span className="ph">Your name</span></span></CField>
                <CField label="Work email"><span className="ff-input"><span className="ph">you@company.com</span></span></CField>
              </div>
              <CField label="Company size" optional>
                <span className="ff-input select"><span className="ph">How big is your team?</span>
                  <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="#98A0AB" strokeWidth="1.8" strokeLinecap="round"><path d="M4 6l4 4 4-4"></path></svg>
                </span>
              </CField>
              <CField label="What's on your mind" hint="The tools you use today, what's slowing you down — anything helps.">
                <span className="ff-input area" style={{ display: 'block' }}><span className="ph">We currently run separate tools for HR and invoicing, and…</span></span>
              </CField>
              <span className="ff-btn primary lg" style={{ width: '100%', boxSizing: 'border-box' }}>Send message</span>
              <p style={{ textAlign: 'center', fontSize: 12.5, color: 'var(--ink-faint)' }}>No newsletter, no drip campaign — just a reply.</p>
            </div>
          </div>
        </div>
      </section>
      <FFFooter></FFFooter>
    </div>
  );
}

/* ── Legal (Terms) ───────────────────────────────────────────── */
const LG_SECTIONS = [
  ['1. The service', 'FlowFlex provides a modular business platform. Modules are activated per workspace; pricing is per active user, per active module, per month.'],
  ['2. Billing', 'Your invoice is calculated at the start of each billing month from the modules active in your workspace and your active user count. Deactivating a module stops billing at the end of the current month.'],
  ['3. Your data', 'Your data remains yours. A full export is available at any time from company settings. Deactivating a module gates access but never deletes data.'],
  ['4. Cancellation', 'Cancelling archives your workspace. Data is retained for 90 days for export, then permanently purged in line with our GDPR retention policy.'],
  ['5. Privacy & GDPR', 'FlowFlex is hosted in the EU and GDPR-compliant. Data subject access requests are processed via the Legal domain and anonymise data across all modules.'],
];

function LegalDesktop() {
  return (
    <div className="ff" data-screen-label="Legal / Terms desktop">
      <FFNav></FFNav>
      <section className="ff-section" style={{ background: 'var(--card)', padding: '84px 0 104px' }}>
        <div className="wrap">
          <div style={{ display: 'grid', gridTemplateColumns: '260px 1fr', gap: 72, alignItems: 'start' }}>
            <div style={{ position: 'sticky', top: 40 }}>
              <span className="ff-kicker"><span className="sq"></span>Legal</span>
              <div style={{ marginTop: 28, display: 'flex', flexDirection: 'column', gap: 2 }}>
                {LG_SECTIONS.map(([t], i) => (
                  <span key={t} style={{ fontSize: 13.5, fontWeight: i === 0 ? 600 : 500, color: i === 0 ? 'var(--indigo)' : 'var(--ink-faint)', padding: '7px 12px', borderLeft: '2px solid ' + (i === 0 ? 'var(--indigo)' : 'var(--line)'), whiteSpace: 'nowrap' }}>{t}</span>
                ))}
              </div>
            </div>
            <div style={{ maxWidth: 640 }}>
              <h1 style={{ fontSize: 44 }}>Terms of service</h1>
              <p className="mono" style={{ marginTop: 12, fontSize: 12, color: 'var(--ink-faint)' }}>Last updated · 11 June 2026 · plain-language summary first, always</p>
              <div style={{ marginTop: 22, background: 'var(--indigo-soft)', border: '1px solid rgba(79,70,229,0.25)', borderRadius: 12, padding: '18px 22px', fontSize: 14.5, lineHeight: 1.65, color: 'var(--ink-soft)' }}>
                <b style={{ color: 'var(--ink)' }}>The short version:</b> you pay for the modules you switch on, your data is always yours and always exportable, and cancelling never holds your data hostage.
              </div>
              {LG_SECTIONS.map(([t, body]) => (
                <div key={t} style={{ marginTop: 40 }}>
                  <h3 style={{ fontSize: 19 }}>{t}</h3>
                  <p style={{ marginTop: 10, fontSize: 15, lineHeight: 1.75, color: 'var(--ink-soft)' }}>{body}</p>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>
      <FFFooter></FFFooter>
    </div>
  );
}

Object.assign(window, { AboutDesktop, ContactDesktop, LegalDesktop });
