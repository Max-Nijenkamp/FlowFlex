/* Proposed additions — public pages: module catalogue, switch-over, trust, changelog, 404.
   Exports ModulesIndexPage, SwitchOverPage, TrustPage, ChangelogPage, NotFoundPage. */
const XPD = window.FF_DATA;

/* ── Module catalogue ────────────────────────────────────────── */
const XP_CATALOG = [
  {
    key: 'hr', name: 'HR & people',
    mods: [
      ['Employee profiles', 'included', 'One record per person — contracts, documents, history.'],
      ['Leave & absence', '€1,50', 'Requests, balances, approvals, team calendar.'],
      ['Payroll', '€2,50', 'Salary runs that read contracts and approved leave.'],
      ['Recruiting', '€1,50', 'Vacancies, candidate pipeline, structured scoring.'],
      ['Onboarding', '€1,00', 'Checklists that provision IT, LMS and payroll.'],
      ['Time tracking', '€1,00', 'Hours that flow into payroll and billing.'],
    ],
  },
  {
    key: 'finance', name: 'Finance & accounting',
    mods: [
      ['Invoicing', '€2,00', 'Drafts itself from won deals and logged hours.'],
      ['Expenses', '€1,00', 'Receipts, approval chains, reimbursement runs.'],
      ['AP / AR', '€1,50', 'Bills in, payments out, ageing in one view.'],
      ['Reporting', '€1,00', 'P&L and cash flow from the live ledger.'],
    ],
  },
  {
    key: 'crm', name: 'CRM & sales',
    mods: [
      ['Contacts', 'included', 'Companies and people, shared with every module.'],
      ['Pipeline', '€1,50', 'Stages, forecasts, win analysis.'],
      ['Deals & quotes', '€1,50', 'Quotes that become invoices the moment they close.'],
    ],
  },
];

function XpFilterRow() {
  const pills = [['All', null, true], ['HR & people', 'hr'], ['Finance', 'finance'], ['CRM & sales', 'crm'], ['Projects', 'projects'], ['Support', 'support'], ['Marketing', 'marketing']];
  return (
    <div style={{ display: 'flex', flexWrap: 'wrap', gap: 10, marginTop: 36 }}>
      {pills.map(([label, key, on]) => (
        <span key={label} className="ff-dompill" style={on ? { background: 'var(--ink)', color: '#fff', borderColor: 'var(--ink)' } : {}}>
          {key && <span className="chip" style={{ background: XPD.domainColors[key] }}></span>}
          {label}
        </span>
      ))}
      <span className="ff-dompill" style={{ borderStyle: 'dashed', background: 'transparent', color: 'var(--ink-faint)' }}>+ 10 more</span>
    </div>
  );
}

function ModulesIndexPage() {
  return (
    <div className="ff" data-screen-label="Module catalogue">
      <FFNav active="Product"></FFNav>
      <section className="ff-hero ff-grid-bg" style={{ paddingBottom: 64 }}>
        <div className="wrap">
          <span className="ff-kicker"><span className="sq"></span>Module catalogue</span>
          <h1 style={{ maxWidth: 700 }}>Every switch<br></br>on the board.</h1>
          <p className="ff-lede">73 modules across 16 departments. Same database, same pricing model — flip any of them on from your billing page.</p>
          <XpFilterRow></XpFilterRow>
        </div>
      </section>
      {XP_CATALOG.map((d, i) => (
        <section className="ff-section" key={d.key} style={{ background: i % 2 ? 'transparent' : 'var(--card)', padding: '72px 0' }}>
          <div className="wrap">
            <div style={{ display: 'flex', alignItems: 'baseline', justifyContent: 'space-between', gap: 16 }}>
              <h2 style={{ fontSize: 28, display: 'flex', alignItems: 'center', gap: 12, marginTop: 0 }}>
                <span style={{ width: 12, height: 12, borderRadius: 4, background: XPD.domainColors[d.key], flex: 'none' }}></span>
                {d.name}
              </h2>
              <span className="ff-arrlink" style={{ fontSize: 14 }}>Domain overview <span className="arr">→</span></span>
            </div>
            <div style={{ marginTop: 28, display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 14 }}>
              {d.mods.map(([name, price, desc]) => (
                <div key={name} className="ff-tile" style={{ padding: 20 }}>
                  <div className="top">
                    <span className="chip" style={{ background: XPD.domainColors[d.key] }}><span></span></span>
                    <span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 11, color: 'var(--ink-faint)' }}>{price === 'included' ? 'included' : price + '/user'}</span>
                  </div>
                  <div className="nm" style={{ fontSize: 15 }}>{name}</div>
                  <p style={{ fontSize: 13.5, lineHeight: 1.55, color: 'var(--ink-soft)', marginTop: 6 }}>{desc}</p>
                </div>
              ))}
            </div>
          </div>
        </section>
      ))}
      <section className="ff-section ff-grid-bg" style={{ padding: '72px 0' }}>
        <div className="wrap" style={{ textAlign: 'center' }}>
          <p className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 12, color: 'var(--ink-faint)' }}>+ 13 more departments — projects, support, marketing, analytics, IT, legal, e-commerce, learning, AI…</p>
        </div>
      </section>
      <FFBand title="Found your first three?" sub="Price them against your team size — the receipt writes itself."></FFBand>
      <FFFooter></FFFooter>
    </div>
  );
}

/* ── Switch over (migration) ─────────────────────────────────── */
const XP_MAP = [
  { old: 'BambooHR', mods: [['HR & people', 'hr']], note: '4 modules' },
  { old: 'Xero', mods: [['Finance', 'finance']], note: '3 modules' },
  { old: 'HubSpot', mods: [['CRM & sales', 'crm']], note: '3 modules' },
  { old: 'Asana + Harvest', mods: [['Projects', 'projects']], note: '3 modules' },
  { old: 'Zapier (gluing it all)', mods: [], note: 'not needed — one database' },
];

function SwitchOverPage() {
  return (
    <div className="ff" data-screen-label="Switch over / migration">
      <FFNav active="Product"></FFNav>
      <section className="ff-hero ff-grid-bg" style={{ paddingBottom: 72 }}>
        <div className="wrap">
          <span className="ff-kicker"><span className="sq"></span>Switching</span>
          <h1 style={{ maxWidth: 760 }}>Leave the patchwork.<br></br><span className="u">Keep the data.</span></h1>
          <p className="ff-lede">Tell us what you run today. We map every tool to its FlowFlex modules, import your data, and you go live one department at a time — not in a big-bang weekend.</p>
          <div className="ff-hero-ctas">
            <span className="ff-btn primary lg">Map my stack</span>
            <span className="ff-arrlink">How migration works <span className="arr">→</span></span>
          </div>
        </div>
      </section>
      <section className="ff-section" style={{ background: 'var(--card)' }}>
        <div className="wrap">
          <p className="ff-tag"><b>01</b> / THE MAP</p>
          <h2>Your stack, translated.</h2>
          <div style={{ marginTop: 48, border: '1px solid var(--line-strong)', borderRadius: 16, overflow: 'hidden' }}>
            {XP_MAP.map((m, i) => (
              <div key={m.old} style={{ display: 'grid', gridTemplateColumns: '1fr 70px 1fr', alignItems: 'center', gap: 16, padding: '16px 26px', borderBottom: i < XP_MAP.length - 1 ? '1px solid var(--line)' : 'none', background: i % 2 ? '#FAF9F5' : '#fff' }}>
                <span style={{ fontSize: 15.5, fontWeight: 500, color: 'var(--ink-faint)', textDecoration: 'line-through', textDecorationColor: 'rgba(79,70,229,0.45)' }}>{m.old}</span>
                <span style={{ display: 'flex', justifyContent: 'center', color: 'var(--indigo)' }}>
                  <svg width="28" height="12" viewBox="0 0 28 12" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round"><path d="M0 6h24M20 1.5L25 6l-5 4.5"></path></svg>
                </span>
                <span style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                  {m.mods.map(([name, key]) => (
                    <span key={name} className="ff-dompill">
                      <span className="chip" style={{ background: XPD.domainColors[key] }}></span>{name}
                    </span>
                  ))}
                  <span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 11.5, color: m.mods.length ? 'var(--ink-faint)' : '#0E8C61', fontWeight: m.mods.length ? 400 : 600 }}>{m.note}</span>
                </span>
              </div>
            ))}
          </div>
        </div>
      </section>
      <section className="ff-section">
        <div className="wrap">
          <p className="ff-tag"><b>02</b> / THE PLAN</p>
          <h2>Domain by domain, never big-bang.</h2>
          <div className="ff-cells">
            <div className="ff-cell">
              <span className="corner"></span>
              <div className="big">1<em> · week 1</em></div>
              <h3>Export & map</h3>
              <p>Pull exports from your current tools. We map fields to FlowFlex modules with you — employees, contacts, open invoices, balances.</p>
            </div>
            <div className="ff-cell">
              <span className="corner"></span>
              <div className="big">2<em> · week 2</em></div>
              <h3>Import & verify</h3>
              <p>Your data lands in a trial workspace. Your team checks it against the old system while both still run.</p>
            </div>
            <div className="ff-cell">
              <span className="corner"></span>
              <div className="big">3<em> · per domain</em></div>
              <h3>Go live, cancel one tool</h3>
              <p>Switch a domain on, cancel the tool it replaces, pocket the difference. Then the next one — at your pace.</p>
            </div>
          </div>
        </div>
      </section>
      <FFFlowBand tag="03" title="The day you switch, the flows start." lede="The moment two domains share the database, the re-typing between them ends." flows={[
        { from: 'CRM', to: 'Finance', event: 'First deal won on FlowFlex', effect: 'First invoice that drafted itself' },
        { from: 'HR', to: 'Payroll', event: 'First hire on FlowFlex', effect: 'Zero forms re-entered downstream' },
        { from: 'You', to: 'Zapier', event: 'Last sync pipeline deleted', effect: 'Nothing to break at 2am anymore' },
      ]}></FFFlowBand>
      <FFBand title="Bring one export. We'll show you the rest." sub="A mapping call takes 30 minutes and produces your migration plan plus your exact monthly price." cta="Plan my switch"></FFBand>
      <FFFooter></FFFooter>
    </div>
  );
}

/* ── Trust & security ────────────────────────────────────────── */
function TrustPage() {
  const cells = [
    ['EU-hosted', 'All data lives in EU data centres. It never leaves the region — not for backups, not for support.'],
    ['GDPR by design', 'DSAR handling is a built-in workflow: one request anonymises a person across every module at once.'],
    ['2FA & SSO', 'Two-factor for everyone, enforced for staff. SSO via your identity provider on request.'],
    ['Full audit log', 'Every read and write, by whom, from where. Core platform — free, always on, exportable.'],
    ['Backups & uptime', 'Continuous backups with point-in-time restore. Status page public, incidents post-mortemed.'],
    ['Export any day', 'Your entire workspace as open formats, self-serve, no exit fee. Portability is the product.'],
  ];
  const faq = [
    ['Where exactly is our data stored?', 'In EU data centres (Netherlands primary, Ireland backup). Region pinning per workspace is on the roadmap.'],
    ['Who can see our data?', 'Your team, per the roles you set. FlowFlex staff access requires your consent per incident and is logged in your audit trail.'],
    ['What happens on deletion?', 'Cancelling archives your workspace for 90 days for export, then a hard purge — backups included — in line with GDPR retention.'],
    ['Do you use subprocessors?', 'A short, public list (hosting, email delivery, payments) — all EU-based or under SCCs. Changes announced 30 days ahead.'],
  ];
  return (
    <div className="ff" data-screen-label="Trust & security">
      <FFNav active="About"></FFNav>
      <section className="ff-hero ff-grid-bg" style={{ paddingBottom: 72 }}>
        <div className="wrap">
          <span className="ff-kicker"><span className="sq"></span>Trust &amp; security</span>
          <h1 style={{ maxWidth: 720 }}>Your data.<br></br>Your rules. <span className="acc">Our job.</span></h1>
          <p className="ff-lede">FlowFlex holds your HR files, your books and your customer records in one place. That concentration is exactly why security and portability are baseline features, not enterprise add-ons.</p>
        </div>
      </section>
      <section className="ff-section" style={{ background: 'var(--card)' }}>
        <div className="wrap">
          <p className="ff-tag"><b>01</b> / GUARANTEES</p>
          <h2>Six things that are always true.</h2>
          <div className="ff-cells" style={{ gridTemplateColumns: 'repeat(3, 1fr)' }}>
            {cells.map(([t, body]) => (
              <div className="ff-cell" key={t}>
                <span className="corner"></span>
                <h3 style={{ marginTop: 0 }}>{t}</h3>
                <p>{body}</p>
              </div>
            ))}
          </div>
        </div>
      </section>
      <section className="ff-section">
        <div className="wrap">
          <p className="ff-tag"><b>02</b> / STRAIGHT ANSWERS</p>
          <h2>What procurement will ask.</h2>
          <dl className="ff-faq" style={{ margin: '44px 0 0' }}>
            {faq.map(([q, a]) => (
              <div className="ff-faq-row" key={q}><dt>{q}</dt><dd>{a}</dd></div>
            ))}
          </dl>
        </div>
      </section>
      <FFBand title="Need the paperwork?" sub="DPA, subprocessor list and security overview — ready to send to your DPO today." cta="Request the pack"></FFBand>
      <FFFooter></FFFooter>
    </div>
  );
}

/* ── Changelog ───────────────────────────────────────────────── */
const XP_LOG = [
  {
    month: 'June 2026', entries: [
      { d: '11 Jun', domain: 'hr', tag: ['New module', 'on'], t: 'Recruiting', p: 'Vacancies, candidate pipeline and structured scoring — hired candidates flow straight into onboarding.' },
      { d: '05 Jun', domain: 'finance', tag: ['Improved', 'off'], t: 'Invoicing — payment links', p: 'Invoices now carry a pay-now link; payments reconcile themselves against the ledger.' },
    ],
  },
  {
    month: 'May 2026', entries: [
      { d: '27 May', domain: 'projects', tag: ['New module', 'on'], t: 'Time tracking', p: 'Hours land on projects and flow into payroll and billable invoice lines.' },
      { d: '15 May', domain: 'crm', tag: ['Improved', 'off'], t: 'Pipeline — health signals', p: 'Accounts now surface support-ticket spikes before renewal conversations.' },
      { d: '02 May', domain: 'hr', tag: ['Improved', 'off'], t: 'Leave — coverage warnings', p: 'Approving leave now flags scheduling gaps in the same dialog.' },
    ],
  },
];

function ChangelogPage() {
  return (
    <div className="ff" data-screen-label="Changelog">
      <FFNav></FFNav>
      <section className="ff-hero ff-grid-bg" style={{ padding: '72px 0 56px' }}>
        <div className="wrap">
          <span className="ff-kicker"><span className="sq"></span>Changelog</span>
          <h1 style={{ fontSize: 52 }}>New on the switchboard.</h1>
          <p className="ff-lede">Every module and improvement as it ships. New modules appear on your billing page the day they land here — switched off, until you say otherwise.</p>
        </div>
      </section>
      <section className="ff-section" style={{ background: 'var(--card)' }}>
        <div className="wrap" style={{ maxWidth: 880 }}>
          {XP_LOG.map((g) => (
            <div key={g.month} style={{ marginBottom: 48 }}>
              <p className="ff-tag" style={{ letterSpacing: '0.22em' }}>{g.month.toUpperCase()}</p>
              <div style={{ marginTop: 18, borderLeft: '1px solid var(--line-strong)', display: 'flex', flexDirection: 'column' }}>
                {g.entries.map((e) => (
                  <div key={e.t} style={{ position: 'relative', padding: '18px 0 18px 36px' }}>
                    <span style={{ position: 'absolute', left: -6, top: 26, width: 11, height: 11, borderRadius: '50%', background: '#fff', border: '2px solid ' + XPD.domainColors[e.domain], boxSizing: 'border-box' }}></span>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 12, flexWrap: 'wrap' }}>
                      <span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 11.5, color: 'var(--ink-faint)' }}>{e.d}</span>
                      <span className={'ff-state ' + e.tag[1]}>{e.tag[0].toUpperCase()}</span>
                      <span className="ff-dompill" style={{ padding: '4px 12px', fontSize: 12 }}>
                        <span className="chip" style={{ background: XPD.domainColors[e.domain] }}></span>
                        {e.domain === 'hr' ? 'HR & people' : e.domain === 'finance' ? 'Finance' : e.domain === 'crm' ? 'CRM & sales' : 'Projects'}
                      </span>
                    </div>
                    <h3 style={{ fontSize: 19, marginTop: 10 }}>{e.t}</h3>
                    <p style={{ marginTop: 6, fontSize: 15, lineHeight: 1.65, color: 'var(--ink-soft)', maxWidth: 620 }}>{e.p}</p>
                  </div>
                ))}
              </div>
            </div>
          ))}
          <span className="ff-btn outline">Older entries</span>
        </div>
      </section>
      <FFFooter></FFFooter>
    </div>
  );
}

/* ── 404 ─────────────────────────────────────────────────────── */
function NotFoundPage() {
  return (
    <div className="ff ff-grid-bg" data-screen-label="404" style={{ width: 1440, height: 760, display: 'flex', flexDirection: 'column' }}>
      <FFNav></FFNav>
      <div style={{ flex: 1, display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', textAlign: 'center', gap: 0 }}>
        <div style={{ display: 'inline-flex', alignItems: 'center', gap: 14, border: '1px solid var(--line-strong)', background: 'var(--card)', borderRadius: 999, padding: '12px 22px', boxShadow: '0 1px 2px rgba(17,24,39,0.04)' }}>
          <span style={{ fontWeight: 600, fontSize: 14.5, color: 'var(--ink-faint)', whiteSpace: 'nowrap' }}>this page</span>
          <span className="ff-sw"></span>
          <span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 10, letterSpacing: '0.14em', background: '#EFEDE7', color: 'var(--ink-faint)', borderRadius: 5, padding: '3px 8px' }}>OFF</span>
        </div>
        <h1 style={{ fontFamily: "'Archivo', sans-serif", fontSize: 120, letterSpacing: '-0.05em', margin: '28px 0 0', lineHeight: 1, fontWeight: 800 }}>404</h1>
        <p style={{ marginTop: 18, fontSize: 17, color: 'var(--ink-soft)', maxWidth: 380, lineHeight: 1.6 }}>This page is switched off — or it never existed. Your data is fine; it's just this URL that flows nowhere.</p>
        <div style={{ display: 'flex', gap: 14, marginTop: 30 }}>
          <span className="ff-btn primary">Back to home</span>
          <span className="ff-btn outline">Contact us</span>
        </div>
      </div>
      <div style={{ borderTop: '1px solid var(--line)', padding: '18px 0', textAlign: 'center' }}>
        <span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 11, color: 'var(--ink-faint)' }}>error 404 · everything else flows</span>
      </div>
    </div>
  );
}

Object.assign(window, { ModulesIndexPage, SwitchOverPage, TrustPage, ChangelogPage, NotFoundPage });
