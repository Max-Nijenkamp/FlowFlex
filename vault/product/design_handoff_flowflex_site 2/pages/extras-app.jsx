/* Proposed additions — app surfaces: first-run module wizard + billing switchboard.
   Exports OnboardingWizard, BillingSettings. Fixed frames. */
const XAD = window.FF_DATA;

/* ── First-run wizard (after invite/registration, owner only) ── */
const WZ_TILES = [
  { name: 'Employee profiles', domain: 'hr', price: 'included', on: true, desc: 'One record per person' },
  { name: 'Leave & absence', domain: 'hr', price: '€1,50', on: true, desc: 'Requests & approvals' },
  { name: 'Invoicing', domain: 'finance', price: '€2,00', on: true, desc: 'Drafts itself from deals' },
  { name: 'Pipeline', domain: 'crm', price: '€1,50', on: true, desc: 'Stages & forecasts' },
  { name: 'Payroll', domain: 'hr', price: '€2,50', on: false, desc: 'Reads contracts & leave' },
  { name: 'Expenses', domain: 'finance', price: '€1,00', on: false, desc: 'Receipts & approvals' },
  { name: 'Contacts', domain: 'crm', price: 'included', on: false, desc: 'Shared with every module' },
  { name: 'Projects & boards', domain: 'projects', price: '€1,50', on: false, desc: 'Kanban & sprints' },
  { name: 'Tickets', domain: 'support', price: '€1,50', on: false, desc: 'Support inbox & SLAs' },
];

function OnboardingWizard() {
  return (
    <div className="ff" data-screen-label="First-run wizard — modules" style={{ width: 1440, height: 900, display: 'flex', flexDirection: 'column', background: 'var(--paper)' }}>
      <div style={{ borderBottom: '1px solid var(--line)', background: 'var(--card)' }}>
        <div style={{ maxWidth: 1240, margin: '0 auto', padding: '0 56px', height: 68, display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
          <FFLogo size={23}></FFLogo>
          <div style={{ display: 'flex', alignItems: 'center', gap: 22 }}>
            {[['1', 'Workspace', 'done'], ['2', 'Modules', 'on'], ['3', 'Invite team', '']].map(([n, label, st]) => (
              <span key={n} style={{ display: 'flex', alignItems: 'center', gap: 9, fontSize: 13.5, fontWeight: 600, color: st === 'on' ? 'var(--ink)' : 'var(--ink-faint)' }}>
                <span style={{
                  width: 24, height: 24, borderRadius: '50%', display: 'flex', alignItems: 'center', justifyContent: 'center',
                  fontFamily: "'JetBrains Mono', monospace", fontSize: 11, fontWeight: 700,
                  background: st === 'done' ? '#10B981' : st === 'on' ? 'var(--indigo)' : 'var(--paper-deep)',
                  color: st ? '#fff' : 'var(--ink-faint)',
                }}>{st === 'done' ? '✓' : n}</span>
                {label}
              </span>
            ))}
          </div>
          <span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 11, color: 'var(--ink-faint)' }}>Veldkamp Logistics</span>
        </div>
      </div>

      <div style={{ flex: 1, overflow: 'hidden' }}>
        <div style={{ maxWidth: 1240, margin: '0 auto', padding: '44px 56px 0', display: 'grid', gridTemplateColumns: '1fr 360px', gap: 48, alignItems: 'start' }}>
          <div>
            <span className="ff-kicker"><span className="sq"></span>Step 2 of 3</span>
            <h1 style={{ fontFamily: "'Archivo', sans-serif", fontSize: 36, letterSpacing: '-0.025em', margin: '18px 0 0' }}>Switch on your first modules.</h1>
            <p style={{ marginTop: 12, fontSize: 15.5, lineHeight: 1.6, color: 'var(--ink-soft)', maxWidth: 520 }}>Start small — most teams begin with three or four. You can change this any month, and nothing here is a contract.</p>
            <div style={{ marginTop: 28, display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 12 }}>
              {WZ_TILES.map((t) => (
                <div key={t.name} style={{
                  border: '1px solid ' + (t.on ? 'rgba(79,70,229,0.5)' : 'var(--line-strong)'),
                  background: t.on ? 'var(--indigo-soft)' : 'var(--card)',
                  borderRadius: 12, padding: '14px 16px',
                  boxShadow: t.on ? '0 0 0 1px rgba(79,70,229,0.15)' : '0 1px 2px rgba(17,24,39,0.03)',
                }}>
                  <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                    <span style={{ width: 10, height: 10, borderRadius: 3, background: XAD.domainColors[t.domain] }}></span>
                    <FFSw on={t.on} sm></FFSw>
                  </div>
                  <div style={{ fontWeight: 600, fontSize: 13.5, marginTop: 10, whiteSpace: 'nowrap' }}>{t.name}</div>
                  <div style={{ fontSize: 12, color: 'var(--ink-soft)', marginTop: 2 }}>{t.desc}</div>
                  <div className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 10.5, color: 'var(--ink-faint)', marginTop: 8 }}>{t.price === 'included' ? 'included' : t.price + '/user'}</div>
                </div>
              ))}
            </div>
            <p className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 11.5, color: 'var(--ink-faint)', marginTop: 16 }}>+ 64 more in the catalogue — browse them any time from billing</p>
          </div>

          <div className="ff-receipt" style={{ position: 'sticky', top: 0 }}>
            <div className="rt">STARTING SELECTION</div>
            <div style={{ height: 14 }}></div>
            <div className="rl head"><span>module</span><span>/user</span></div>
            <div className="rl"><span>Employee profiles</span><span>€0,00</span></div>
            <div className="rl"><span>Leave &amp; absence</span><span>€1,50</span></div>
            <div className="rl"><span>Invoicing</span><span>€2,00</span></div>
            <div className="rl"><span>Pipeline</span><span>€1,50</span></div>
            <div className="rl total"><span>per user</span><span>€5,00</span></div>
            <div style={{ height: 8 }}></div>
            <div className="rl" style={{ justifyContent: 'center', fontSize: 11, color: 'var(--ink-faint)', whiteSpace: 'normal', textAlign: 'center' }}>trial first — billing starts when you do</div>
          </div>
        </div>
      </div>

      <div style={{ borderTop: '1px solid var(--line)', background: 'var(--card)' }}>
        <div style={{ maxWidth: 1240, margin: '0 auto', padding: '0 56px', height: 76, display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
          <span style={{ fontSize: 14, fontWeight: 600, color: 'var(--ink-soft)' }}>← Back</span>
          <div style={{ display: 'flex', alignItems: 'center', gap: 18 }}>
            <span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 12, color: 'var(--ink-faint)' }}>4 modules · €5,00/user</span>
            <span className="ff-btn primary lg">Continue — invite your team →</span>
          </div>
        </div>
      </div>
    </div>
  );
}

/* ── Billing & modules (App panel — the in-product switchboard) ── */
const BL_GROUPS = [
  {
    key: 'hr', name: 'HR & people',
    rows: [['Employee profiles', 'included', true], ['Leave & absence', '€1,50', true], ['Payroll', '€2,50', false], ['Recruiting', '€1,50', false]],
  },
  {
    key: 'finance', name: 'Finance & accounting',
    rows: [['Invoicing', '€2,00', true], ['Expenses', '€1,00', false]],
  },
  {
    key: 'crm', name: 'CRM & sales',
    rows: [['Contacts', 'included', true], ['Pipeline', '€1,50', true]],
  },
];

function BlSidebar() {
  const ic = (d) => <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round">{d}</svg>;
  const nav = [
    ['Dashboard', ic(<><rect x="3" y="3" width="6" height="6" rx="1.5"></rect><rect x="11" y="3" width="6" height="6" rx="1.5"></rect><rect x="3" y="11" width="6" height="6" rx="1.5"></rect><rect x="11" y="11" width="6" height="6" rx="1.5"></rect></>)],
    ['Members', ic(<><circle cx="7.5" cy="7" r="3"></circle><path d="M2.5 17c.6-3 2.6-4.5 5-4.5s4.4 1.5 5 4.5M13.5 4.5a3 3 0 010 5M15 12.7c1.6.6 2.6 1.9 3 4.3"></path></>), false],
    ['Billing & modules', ic(<><rect x="2.5" y="5" width="15" height="10" rx="2"></rect><path d="M2.5 8.5h15"></path></>), true],
    ['Settings', ic(<><circle cx="10" cy="10" r="2.6"></circle><path d="M10 3v2.2M10 14.8V17M17 10h-2.2M5.2 10H3M14.9 5.1l-1.6 1.6M6.7 13.3l-1.6 1.6M14.9 14.9l-1.6-1.6M6.7 6.7L5.1 5.1"></path></>)],
  ];
  return (
    <aside className="pn-side">
      <div className="pn-side-brand"><FFLogo light size={21}></FFLogo></div>
      <div className="pn-side-panel">WORKSPACE · /APP</div>
      <nav className="pn-nav" style={{ marginTop: 8 }}>
        {nav.map(([label, icon, on]) => (
          <span key={label} className={'pn-nav-item' + (on ? ' on' : '')}>{icon}{label}</span>
        ))}
      </nav>
      <div style={{ marginTop: 'auto' }}>
        <div className="pn-nav-group">Your panels</div>
        <div className="pn-panels">
          {[['APP', true], ['HR'], ['FIN'], ['CRM']].map(([p, on]) => <span key={p} className={on ? 'on' : ''}>{p}</span>)}
        </div>
        <div className="pn-side-foot">
          <span className="ava" style={{ background: '#4F46E5' }}>T</span>
          <span>
            <span className="nm" style={{ display: 'block' }}>Tom de Vries</span>
            <span className="co">Owner · Veldkamp Logistics</span>
          </span>
        </div>
      </div>
    </aside>
  );
}

function BillingSettings() {
  return (
    <div className="pn" data-screen-label="Billing & modules — /app" style={{ '--violet': '#4F46E5', '--violet-soft': '#EEF2FF' }}>
      <BlSidebar></BlSidebar>
      <div className="pn-main">
        <div className="pn-top">
          <div className="pn-crumb"><span>Workspace</span><span>/</span><span className="here">Billing &amp; modules</span></div>
          <div className="pn-top-right">
            <span className="pn-iconbtn">
              <svg width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round"><path d="M10 3a5 5 0 015 5c0 4 1.5 5 1.5 5h-13S5 12 5 8a5 5 0 015-5zM8.5 16.5a1.8 1.8 0 003 0"></path></svg>
            </span>
            <span className="pn-ava" style={{ background: '#4F46E5' }}>T</span>
          </div>
        </div>
        <div className="pn-body" style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-end' }}>
            <div>
              <h1 style={{ fontSize: 23 }}>Billing &amp; modules</h1>
              <p style={{ marginTop: 4, fontSize: 13.5, color: 'var(--ink-faint)' }}>The switchboard — changes apply immediately, billing follows at month-end</p>
            </div>
            <span className="pn-btn ghost">Browse module catalogue</span>
          </div>

          <div style={{ display: 'grid', gridTemplateColumns: '1fr 340px', gap: 18, alignItems: 'start' }}>
            <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
              {BL_GROUPS.map((g) => (
                <div className="pn-widget" key={g.key}>
                  <div className="pn-widget-head">
                    <h3 style={{ display: 'flex', alignItems: 'center', gap: 10, whiteSpace: 'nowrap' }}>
                      <span style={{ width: 10, height: 10, borderRadius: 3, background: XAD.domainColors[g.key] }}></span>
                      {g.name}
                    </h3>
                    <span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 11, color: 'var(--ink-faint)', whiteSpace: 'nowrap' }}>{g.rows.filter((r) => r[2]).length} of {g.rows.length} on</span>
                  </div>
                  {g.rows.map(([name, price, on]) => (
                    <div key={name} style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: 14, padding: '11px 20px', borderBottom: '1px solid var(--line)', opacity: on ? 1 : 0.55 }}>
                      <span style={{ fontSize: 13.5, fontWeight: 600, whiteSpace: 'nowrap' }}>{name}</span>
                      <span style={{ display: 'flex', alignItems: 'center', gap: 16, whiteSpace: 'nowrap' }}>
                        <span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 11.5, color: 'var(--ink-faint)' }}>{price === 'included' ? 'included' : price + '/user'}</span>
                        <FFSw on={on} sm></FFSw>
                      </span>
                    </div>
                  ))}
                </div>
              ))}
              <div style={{ border: '1px dashed var(--line-strong)', borderRadius: 12, padding: '13px 20px', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                <span style={{ fontSize: 13, color: 'var(--ink-faint)', fontWeight: 500 }}>13 more departments available</span>
                <span className="pn-btn xs ghost">Open catalogue</span>
              </div>
            </div>

            <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
              <div className="pn-widget" style={{ padding: '18px 20px' }}>
                <p className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 10, letterSpacing: '0.18em', color: 'var(--ink-faint)' }}>NEXT INVOICE · 1 JUL</p>
                <div style={{ display: 'flex', alignItems: 'baseline', gap: 8, marginTop: 10 }}>
                  <span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 32, fontWeight: 700, letterSpacing: '-0.04em' }}>€400,00</span>
                  <span style={{ fontSize: 12.5, color: 'var(--ink-faint)' }}>/month</span>
                </div>
                <div className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 11.5, color: 'var(--ink-soft)', marginTop: 8 }}>€5,00/user × 80 active users</div>
                <div style={{ marginTop: 14, paddingTop: 12, borderTop: '1px solid var(--line)', display: 'flex', flexDirection: 'column', gap: 6, fontSize: 12.5 }}>
                  {[['Leave & absence', '€1,50'], ['Invoicing', '€2,00'], ['Pipeline', '€1,50'], ['Included modules', '€0,00']].map(([n, p]) => (
                    <div key={n} style={{ display: 'flex', justifyContent: 'space-between', color: 'var(--ink-soft)' }}>
                      <span>{n}</span><span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace" }}>{p}</span>
                    </div>
                  ))}
                </div>
              </div>
              <div className="pn-widget" style={{ padding: '16px 20px' }}>
                <h3 style={{ fontSize: 13.5 }}>Payment method</h3>
                <div style={{ marginTop: 10, display: 'flex', alignItems: 'center', gap: 10, fontSize: 13 }}>
                  <span style={{ width: 34, height: 22, borderRadius: 4, background: 'var(--sb)', color: '#fff', display: 'flex', alignItems: 'center', justifyContent: 'center', fontFamily: "'JetBrains Mono', monospace", fontSize: 8 }}>SEPA</span>
                  <span style={{ color: 'var(--ink-soft)' }}>NL91 ABNA ···· 0417</span>
                  <span style={{ marginLeft: 'auto', fontSize: 12, fontWeight: 600, color: 'var(--violet)' }}>Change</span>
                </div>
              </div>
              <div className="pn-widget">
                <div className="pn-widget-head"><h3>Invoices</h3><span className="all">All →</span></div>
                {[['Jun 2026', '€400,00'], ['May 2026', '€400,00'], ['Apr 2026', '€280,00']].map(([m, v]) => (
                  <div key={m} style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '10px 20px', borderBottom: '1px solid var(--line)', fontSize: 12.5 }}>
                    <span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", color: 'var(--ink-soft)', whiteSpace: 'nowrap' }}>{m}</span>
                    <span style={{ display: 'flex', gap: 12, alignItems: 'center', whiteSpace: 'nowrap' }}>
                      <span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontWeight: 700 }}>{v}</span>
                      <span style={{ color: 'var(--violet)', fontWeight: 600, fontSize: 12 }}>PDF</span>
                    </span>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

Object.assign(window, { OnboardingWizard, BillingSettings });
