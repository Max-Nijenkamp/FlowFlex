/* Pricing page — calculator with domain groups + sticky receipt. Exports PricingDesktop, PricingMobile. */
const PRD = window.FF_DATA;

const PR_GROUPS = [
  {
    key: 'core', name: 'Core platform', note: 'always on, always free',
    mods: [
      { name: 'Authentication & identity', price: 0, on: true, locked: true },
      { name: 'Notifications', price: 0, on: true, locked: true },
      { name: 'Audit log', price: 0, on: true, locked: true },
      { name: 'Roles & permissions', price: 0, on: true, locked: true },
    ],
  },
  {
    key: 'hr', name: 'HR & people', open: true,
    mods: [
      { name: 'Employee profiles', price: 0, on: true },
      { name: 'Leave & absence', price: 150, on: true },
      { name: 'Payroll', price: 250, on: false },
      { name: 'Recruiting', price: 150, on: false },
      { name: 'Onboarding', price: 100, on: false },
      { name: 'Time tracking', price: 100, on: false },
    ],
  },
  {
    key: 'finance', name: 'Finance & accounting',
    mods: [
      { name: 'Invoicing', price: 200, on: true },
      { name: 'Expenses', price: 100, on: false },
      { name: 'AP / AR', price: 150, on: false },
      { name: 'Reporting', price: 100, on: false },
    ],
  },
  {
    key: 'crm', name: 'CRM & sales',
    mods: [
      { name: 'Contacts', price: 0, on: true },
      { name: 'Pipeline', price: 150, on: true },
      { name: 'Deals & quotes', price: 150, on: false },
    ],
  },
];

function PrGroup({ g, mobile }) {
  const selected = g.mods.filter((m) => m.on && m.price > 0);
  const subtotal = selected.reduce((s, m) => s + m.price, 0);
  const open = g.open || g.key === 'core' ? true : false;
  const expanded = g.open === true;
  return (
    <div style={{ border: '1px solid var(--line-strong)', borderRadius: 14, background: 'var(--card)', overflow: 'hidden', boxShadow: '0 1px 2px rgba(17,24,39,0.03)' }}>
      <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: 12, padding: '16px 22px', borderBottom: expanded ? '1px solid var(--line)' : 'none' }}>
        <span style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
          <span style={{ width: 11, height: 11, borderRadius: 3, background: PRD.domainColors[g.key] || '#94A3B8' }}></span>
          <span style={{ fontWeight: 700, fontFamily: "'Archivo', sans-serif", fontSize: 15.5 }}>{g.name}</span>
          <span className="mono" style={{ fontSize: 11, color: 'var(--ink-faint)' }}>{g.mods.length} modules</span>
        </span>
        <span style={{ display: 'flex', alignItems: 'center', gap: 14 }}>
          {g.note && <span className="mono" style={{ fontSize: 10.5, color: 'var(--ink-faint)' }}>{g.note}</span>}
          {!g.note && g.mods.some((m) => m.on) && (
            <span style={{ fontSize: 11.5, fontWeight: 700, color: 'var(--indigo)', background: 'var(--indigo-soft)', borderRadius: 999, padding: '3px 10px' }}>
              {g.mods.filter((m) => m.on).length} on
            </span>
          )}
          {subtotal > 0 && <span className="mono" style={{ fontSize: 11.5, color: 'var(--ink-soft)' }}>+{ffEuro(subtotal)}/user</span>}
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="#98A0AB" strokeWidth="1.8" strokeLinecap="round" style={{ transform: expanded ? 'rotate(180deg)' : 'none' }}><path d="M4 6l4 4 4-4"></path></svg>
        </span>
      </div>
      {expanded && (
        <div style={{ display: 'grid', gridTemplateColumns: mobile ? '1fr' : '1fr 1fr', gap: 10, padding: 18 }}>
          {g.mods.map((m) => (
            <div key={m.name} style={{
              display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: 10,
              border: '1px solid ' + (m.on ? 'rgba(79,70,229,0.45)' : 'var(--line)'),
              background: m.on ? 'var(--indigo-soft)' : 'var(--card)',
              borderRadius: 10, padding: '11px 14px',
            }}>
              <span style={{ display: 'flex', alignItems: 'center', gap: 10, fontSize: 14, fontWeight: 600 }}>
                <FFSw on={m.on} sm></FFSw>
                {m.name}
              </span>
              <span className="mono" style={{ fontSize: 11.5, color: 'var(--ink-faint)', whiteSpace: 'nowrap' }}>{m.price === 0 ? 'included' : ffEuro(m.price)}</span>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

function PrReceipt() {
  return (
    <div className="ff-receipt">
      <div className="rt">YOUR MONTHLY INVOICE</div>
      <div style={{ height: 18 }}></div>
      <div style={{ fontFamily: "'Instrument Sans', sans-serif" }}>
        <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: 13.5, fontWeight: 600 }}>
          <span>Team size</span>
          <span className="mono" style={{ fontWeight: 700 }}>80 people</span>
        </div>
        <div style={{ marginTop: 12, position: 'relative', height: 18 }}>
          <div style={{ position: 'absolute', top: 7, left: 0, right: 0, height: 4, borderRadius: 2, background: '#E9E6DE' }}></div>
          <div style={{ position: 'absolute', top: 7, left: 0, width: '15%', height: 4, borderRadius: 2, background: 'var(--indigo)' }}></div>
          <div style={{ position: 'absolute', top: 0, left: '15%', width: 18, height: 18, borderRadius: 9, background: '#fff', border: '2px solid var(--indigo)', boxSizing: 'border-box', boxShadow: '0 1px 4px rgba(17,24,39,0.25)' }}></div>
        </div>
        <div style={{ display: 'flex', justifyContent: 'space-between', marginTop: 4, fontSize: 10.5, color: 'var(--ink-faint)', fontFamily: "'JetBrains Mono', monospace" }}>
          <span>10</span><span>500</span>
        </div>
      </div>
      <div style={{ height: 18 }}></div>
      <div className="rl head"><span>module</span><span>/user</span></div>
      <div className="rl"><span>Employee profiles</span><span>€0,00</span></div>
      <div className="rl"><span>Leave &amp; absence</span><span>€1,50</span></div>
      <div className="rl"><span>Invoicing</span><span>€2,00</span></div>
      <div className="rl"><span>Pipeline</span><span>€1,50</span></div>
      <div className="rl"><span className="dim">Core platform</span><span className="dim">€0,00</span></div>
      <div className="rl total"><span>€5,00 × 80</span><span>€400</span></div>
      <div style={{ height: 16 }}></div>
      <span className="ff-btn primary" style={{ width: '100%', boxSizing: 'border-box' }}>Talk to us</span>
      <div style={{ height: 10 }}></div>
      <div className="rl" style={{ justifyContent: 'center', fontSize: 10.5, color: 'var(--ink-faint)' }}>change modules any month · no contracts</div>
    </div>
  );
}

const PR_FAQ = [
  { q: 'What happens when I deactivate a module?', a: 'Billing stops at the end of the month. Your data stays — reactivate and pick up where you left off, or export it.' },
  { q: 'Do prices change as we grow?', a: 'The per-module price stays the same at 50 or 500 users. You pay for more seats, not a higher tier.' },
  { q: 'Can we take our data out?', a: 'Yes — full export, any day, no exit fee. Data portability is a baseline feature, not an enterprise add-on.' },
  { q: 'Is there a free trial?', a: 'Yes. Every workspace starts on a trial with all activated modules accessible, so you can test the real thing.' },
  { q: 'What counts as an active user?', a: 'Anyone who can sign in. Deactivated employees stay in your records but are never billed.' },
  { q: 'Are there hidden platform fees?', a: 'No. Core platform — login, roles, notifications, audit log, file storage — is always on and always free.' },
];

function PricingDesktop() {
  return (
    <div className="ff" data-screen-label="Pricing desktop">
      <FFNav active="Pricing"></FFNav>
      <section className="ff-hero ff-grid-bg" style={{ paddingBottom: 72 }}>
        <div className="wrap">
          <span className="ff-kicker"><span className="sq"></span>Pricing</span>
          <h1 style={{ maxWidth: 760 }}>No tiers. No bundles.<br></br><span className="acc">One formula.</span></h1>
          <p className="ff-lede">Your invoice is the sum of the modules you switched on, times the people on your team. That's it.</p>
          <div style={{ marginTop: 30, display: 'inline-block', background: 'var(--ink)', color: '#fff', borderRadius: 12, padding: '14px 24px', fontFamily: "'JetBrains Mono', monospace", fontSize: 15 }}>
            invoice = <span style={{ color: '#A5A3FF' }}>Σ(module price)</span> × <span style={{ color: 'var(--sky)' }}>active users</span>
          </div>
        </div>
      </section>
      <section className="ff-section" style={{ background: 'var(--card)' }}>
        <div className="wrap">
          <div style={{ display: 'grid', gridTemplateColumns: '1fr 380px', gap: 48, alignItems: 'start' }}>
            <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
              {PR_GROUPS.map((g) => <PrGroup key={g.key} g={g}></PrGroup>)}
              <p className="mono" style={{ fontSize: 12, color: 'var(--ink-faint)', padding: '8px 4px' }}>+ 12 more departments in the marketplace once your workspace is live</p>
            </div>
            <PrReceipt></PrReceipt>
          </div>
        </div>
      </section>
      <section className="ff-section">
        <div className="wrap">
          <p className="ff-tag"><b>02</b> / FAIR PRINT</p>
          <h2>The fine print, minus the fine.</h2>
          <dl className="ff-faq" style={{ margin: '48px 0 0' }}>
            {PR_FAQ.map((f) => (
              <div className="ff-faq-row" key={f.q}><dt>{f.q}</dt><dd>{f.a}</dd></div>
            ))}
          </dl>
        </div>
      </section>
      <FFBand title="Your number is one minute away." sub="Pick your modules, set your team size, and the receipt writes itself."></FFBand>
      <FFFooter></FFFooter>
    </div>
  );
}

function PricingMobile() {
  return (
    <div className="ff ff-m" data-screen-label="Pricing mobile">
      <FFNav mobile></FFNav>
      <section className="ff-hero ff-grid-bg" style={{ padding: '54px 0 60px' }}>
        <div className="wrap">
          <span className="ff-kicker"><span className="sq"></span>Pricing</span>
          <h1>No tiers. No bundles. <span className="acc">One formula.</span></h1>
          <p className="ff-lede" style={{ fontSize: 16 }}>The modules you switched on, times the people on your team. That's it.</p>
          <div style={{ marginTop: 26, display: 'inline-block', background: 'var(--ink)', color: '#fff', borderRadius: 10, padding: '12px 16px', fontFamily: "'JetBrains Mono', monospace", fontSize: 11.5 }}>
            invoice = <span style={{ color: '#A5A3FF' }}>Σ(modules)</span> × <span style={{ color: 'var(--sky)' }}>users</span>
          </div>
        </div>
      </section>
      <section className="ff-section" style={{ background: 'var(--card)' }}>
        <div className="wrap">
          <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
            {PR_GROUPS.slice(1, 3).map((g) => <PrGroup key={g.key} g={{ ...g, open: g.key === 'hr' }} mobile></PrGroup>)}
            <PrReceipt></PrReceipt>
          </div>
        </div>
      </section>
      <section className="ff-section">
        <div className="wrap">
          <p className="ff-tag"><b>02</b> / FAIR PRINT</p>
          <h2>The fine print, minus the fine.</h2>
          <dl className="ff-faq" style={{ margin: '40px 0 0' }}>
            {PR_FAQ.slice(0, 4).map((f) => (
              <div className="ff-faq-row" key={f.q}><dt>{f.q}</dt><dd>{f.a}</dd></div>
            ))}
          </dl>
        </div>
      </section>
      <FFBand title="Your number is one minute away." sub="Pick your modules, set your team size."></FFBand>
      <FFFooter mobile></FFFooter>
    </div>
  );
}

Object.assign(window, { PricingDesktop, PricingMobile });
