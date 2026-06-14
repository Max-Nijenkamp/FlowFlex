/* More additions — emails + in-app: transactional email templates, employee profile,
   command palette, fresh-module empty state.
   Exports EmailTemplates, EmployeeProfile, CommandPalette, EmptyStateTickets. */
const X3D = window.FF_DATA;

/* ── Transactional email templates ───────────────────────────── */
function EmShell({ title, children, preheader }) {
  return (
    <div style={{ width: 600, background: '#FFFFFF', borderRadius: 14, border: '1px solid #E7E4DD', overflow: 'hidden', boxShadow: '0 14px 32px -18px rgba(17,24,39,0.18)' }}>
      <div style={{ padding: '22px 36px', borderBottom: '1px solid #E7E4DD', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <FFLogo size={20}></FFLogo>
        <span style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 10, letterSpacing: '0.16em', color: '#98A0AB' }}>{preheader}</span>
      </div>
      <div style={{ padding: '32px 36px' }}>{children}</div>
      <div style={{ padding: '18px 36px', borderTop: '1px solid #E7E4DD', background: '#FBFAF8', display: 'flex', justifyContent: 'space-between', fontFamily: "'JetBrains Mono', monospace", fontSize: 10, color: '#98A0AB' }}>
        <span>© 2026 FlowFlex · everything flows</span>
        <span>preferences · unsubscribe</span>
      </div>
    </div>
  );
}

function EmH({ children }) {
  return <h2 style={{ fontFamily: "'Archivo', sans-serif", fontSize: 21, letterSpacing: '-0.02em', margin: 0, color: '#111827' }}>{children}</h2>;
}
function EmP({ children, style }) {
  return <p style={{ margin: '12px 0 0', fontSize: 14.5, lineHeight: 1.65, color: '#4B5563', ...style }}>{children}</p>;
}
function EmBtn({ children, dark }) {
  return <span style={{ display: 'inline-block', whiteSpace: 'nowrap', marginTop: 22, background: dark ? '#111827' : '#4F46E5', color: '#fff', fontWeight: 600, fontSize: 14.5, borderRadius: 10, padding: '13px 26px' }}>{children}</span>;
}

function EmailTemplates() {
  return (
    <div data-screen-label="Email templates" style={{ background: '#F4F2EC', padding: '56px 60px', fontFamily: "'Instrument Sans', sans-serif", display: 'grid', gridTemplateColumns: '600px 600px', gap: 40, justifyContent: 'center' }}>
      <EmShell preheader="WORKSPACE INVITE">
        <EmH>Tom invited you to Veldkamp Logistics</EmH>
        <EmP>Tom de Vries set you up on FlowFlex — the place where Veldkamp now runs HR, invoicing and sales. One login, and your leave requests, payslips and projects live in the same place.</EmP>
        <div style={{ marginTop: 20, border: '1px solid #E7E4DD', borderRadius: 12, padding: '14px 18px', display: 'flex', alignItems: 'center', gap: 12, background: '#FBFAF8' }}>
          <span style={{ width: 34, height: 34, borderRadius: '50%', background: '#8B5CF6', color: '#fff', display: 'flex', alignItems: 'center', justifyContent: 'center', fontWeight: 700, fontFamily: "'Archivo', sans-serif", fontSize: 13 }}>M</span>
          <span>
            <span style={{ fontWeight: 600, fontSize: 13.5, color: '#111827', display: 'block' }}>marieke@veldkamp.eu</span>
            <span style={{ fontSize: 12, color: '#98A0AB' }}>HR manager · joins 3 panels</span>
          </span>
        </div>
        <EmBtn>Accept invitation</EmBtn>
        <EmP style={{ fontSize: 12.5, color: '#98A0AB' }}>This link is personal and expires in 7 days. Didn't expect this? You can ignore it safely.</EmP>
      </EmShell>

      <EmShell preheader="LEAVE · APPROVED">
        <div style={{ display: 'inline-flex', alignItems: 'center', gap: 8, background: '#E5F5EE', color: '#0E8C61', borderRadius: 999, padding: '5px 14px', fontSize: 12, fontWeight: 700 }}>
          <span style={{ width: 7, height: 7, borderRadius: '50%', background: '#0E8C61' }}></span>APPROVED
        </div>
        <div style={{ height: 14 }}></div>
        <EmH>Your holiday is booked, Sanne</EmH>
        <EmP>Femke approved your request. Scheduling already knows — your shifts for that week are unassigned and your team calendar is updated.</EmP>
        <div style={{ marginTop: 20, border: '1px solid #E7E4DD', borderRadius: 12, overflow: 'hidden', fontSize: 13.5 }}>
          {[['Dates', '24 – 28 June 2026 · 5 days'], ['Type', 'Holiday'], ['Balance after', '11 days remaining'], ['Approved by', 'Femke de Boer · HR']].map(([k, v], i) => (
            <div key={k} style={{ display: 'flex', justifyContent: 'space-between', padding: '11px 18px', borderBottom: i < 3 ? '1px solid #E7E4DD' : 'none', background: i % 2 ? '#FBFAF8' : '#fff' }}>
              <span style={{ color: '#98A0AB', fontWeight: 500 }}>{k}</span>
              <span style={{ color: '#111827', fontWeight: 600 }}>{v}</span>
            </div>
          ))}
        </div>
        <EmBtn dark>View in FlowFlex</EmBtn>
      </EmShell>

      <EmShell preheader="INVOICE · JUNE 2026">
        <EmH>Your June invoice — €400,00</EmH>
        <EmP>Same as last month: four active modules, 80 active users. The PDF is attached and in your billing page.</EmP>
        <div style={{ marginTop: 20, border: '1px dashed #D8D4CA', borderRadius: 12, padding: '18px 22px', fontFamily: "'JetBrains Mono', monospace", fontSize: 12.5, color: '#4B5563' }}>
          {[['Employee profiles', '€0,00'], ['Leave & absence', '€1,50'], ['Invoicing', '€2,00'], ['Pipeline', '€1,50']].map(([m, p]) => (
            <div key={m} style={{ display: 'flex', justifyContent: 'space-between', padding: '4px 0' }}>
              <span>{m}</span><span>{p}</span>
            </div>
          ))}
          <div style={{ display: 'flex', justifyContent: 'space-between', borderTop: '1px dashed #D8D4CA', marginTop: 10, paddingTop: 10, fontWeight: 700, color: '#111827', fontSize: 14 }}>
            <span>€5,00 × 80 users</span><span>€400,00</span>
          </div>
        </div>
        <EmBtn dark>Download PDF</EmBtn>
        <EmP style={{ fontSize: 12.5, color: '#98A0AB' }}>Collected by SEPA direct debit around 1 July. Change modules any time — it shows up on the next invoice, never mid-month.</EmP>
      </EmShell>

      <EmShell preheader="MODULE · SWITCHED ON">
        <EmH>Payroll is now on for Veldkamp Logistics</EmH>
        <EmP>Tom switched on <b style={{ color: '#111827' }}>Payroll</b> (€2,50/user/month, first invoice 1 July). It's already reading contracts and approved leave from HR — your first salary run can start today.</EmP>
        <div style={{ marginTop: 20, display: 'flex', gap: 10 }}>
          {[['Contracts found', '127'], ['Ready for first run', 'July'], ['Setup left', '2 steps']].map(([k, v]) => (
            <div key={k} style={{ flex: 1, border: '1px solid #E7E4DD', borderRadius: 10, padding: '12px 14px' }}>
              <div style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 16, fontWeight: 700, color: '#111827' }}>{v}</div>
              <div style={{ fontSize: 11.5, color: '#98A0AB', marginTop: 3 }}>{k}</div>
            </div>
          ))}
        </div>
        <EmBtn>Finish payroll setup</EmBtn>
        <EmP style={{ fontSize: 12.5, color: '#98A0AB' }}>Not what you expected? Switch it off in billing — you won't be charged if it's off before 1 July.</EmP>
      </EmShell>
    </div>
  );
}

/* ── Employee profile (Filament record view) ─────────────────── */
function EpInfoRow({ k, v, mono }) {
  return (
    <div style={{ display: 'flex', justifyContent: 'space-between', gap: 16, padding: '10px 20px', borderBottom: '1px solid var(--line)', fontSize: 13 }}>
      <span style={{ color: 'var(--ink-faint)', fontWeight: 500, whiteSpace: 'nowrap' }}>{k}</span>
      <span className={mono ? 'mono' : ''} style={{ fontWeight: 600, fontFamily: mono ? "'JetBrains Mono', monospace" : undefined, fontSize: mono ? 12 : 13, textAlign: 'right', whiteSpace: 'nowrap' }}>{v}</span>
    </div>
  );
}

function EmployeeProfile() {
  const Side = window.PnSidebar;
  return (
    <div className="pn" data-screen-label="Employee profile — record view">
      <Side active="Employees"></Side>
      <div className="pn-main">
        <div className="pn-top">
          <div className="pn-crumb"><span>HR &amp; people</span><span>/</span><span>Employees</span><span>/</span><span className="here">Sanne Bakker</span></div>
          <div className="pn-top-right">
            <span className="pn-btn ghost" style={{ fontSize: 12.5, padding: '7px 14px' }}>Edit</span>
            <span className="pn-btn" style={{ fontSize: 12.5, padding: '7px 14px' }}>Actions ▾</span>
          </div>
        </div>
        <div className="pn-body" style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
          <div className="pn-widget" style={{ padding: '20px 24px', display: 'flex', alignItems: 'center', gap: 18 }}>
            <span className="pn-ava" style={{ background: '#8B5CF6', width: 52, height: 52, fontSize: 20 }}>S</span>
            <div>
              <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                <h1 style={{ fontSize: 21 }}>Sanne Bakker</h1>
                <span className="pn-pill ok"><span className="dot"></span>Active</span>
              </div>
              <p style={{ marginTop: 3, fontSize: 13, color: 'var(--ink-faint)' }}>Warehouse lead · Operations · since March 2021</p>
            </div>
            <div style={{ marginLeft: 'auto', display: 'flex', gap: 24, textAlign: 'right' }}>
              {[['Leave balance', '11 days'], ['Hours', '38 h/week'], ['Next review', 'Sep 2026']].map(([k, v]) => (
                <div key={k}>
                  <div className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 15, fontWeight: 700 }}>{v}</div>
                  <div style={{ fontSize: 11, color: 'var(--ink-faint)', marginTop: 2 }}>{k}</div>
                </div>
              ))}
            </div>
          </div>

          <div className="pn-tabs" style={{ background: 'transparent' }}>
            {['Overview', 'Contract', 'Leave', 'Documents', 'Payroll', 'Activity'].map((t, i) => (
              <span key={t} className={'pn-tab' + (i === 0 ? ' on' : '')}>{t}</span>
            ))}
          </div>

          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 16, alignItems: 'start' }}>
            <div className="pn-widget">
              <div className="pn-widget-head"><h3>Personal</h3></div>
              <EpInfoRow k="Email" v="sanne@veldkamp.eu"></EpInfoRow>
              <EpInfoRow k="Phone" v="+31 6 2345 8821" mono></EpInfoRow>
              <EpInfoRow k="City" v="Utrecht"></EpInfoRow>
              <EpInfoRow k="Emergency contact" v="J. Bakker · partner"></EpInfoRow>
              <EpInfoRow k="Birthday" v="14 Feb 1991" mono></EpInfoRow>
            </div>
            <div className="pn-widget">
              <div className="pn-widget-head"><h3>Contract</h3><span className="all">History →</span></div>
              <EpInfoRow k="Type" v="Permanent · full-time"></EpInfoRow>
              <EpInfoRow k="Hours" v="38 h/week" mono></EpInfoRow>
              <EpInfoRow k="Salary" v="€3.450 /month" mono></EpInfoRow>
              <EpInfoRow k="Manager" v="Tom de Vries"></EpInfoRow>
              <EpInfoRow k="Started" v="01-03-2021" mono></EpInfoRow>
            </div>
            <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
              <div className="pn-widget" style={{ padding: '16px 20px' }}>
                <h3 style={{ fontSize: 13.5 }}>One record, four modules</h3>
                <p style={{ fontSize: 12, color: 'var(--ink-faint)', marginTop: 4, lineHeight: 1.5 }}>Changes here flow everywhere this person appears.</p>
                <div style={{ marginTop: 12, display: 'flex', flexWrap: 'wrap', gap: 7 }}>
                  {[['Leave', '#8B5CF6'], ['Payroll', '#10B981'], ['Scheduling', '#FB923C'], ['Learning', '#22C55E']].map(([m, c]) => (
                    <span key={m} className="pn-dept"><span className="sq" style={{ background: c }}></span>{m}</span>
                  ))}
                </div>
              </div>
              <div className="pn-widget">
                <div className="pn-widget-head"><h3>Recent</h3><span className="all">All →</span></div>
                {[
                  ['Today', 'Leave approved · 24–28 Jun', '#8B5CF6'],
                  ['Mon', 'Forklift certification renewed', '#22C55E'],
                  ['03 Jun', 'May payslip issued', '#10B981'],
                ].map(([t, what, c]) => (
                  <div key={what} style={{ display: 'flex', gap: 10, alignItems: 'baseline', padding: '9px 20px', borderBottom: '1px solid var(--line)', fontSize: 12.5 }}>
                    <span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 10.5, color: 'var(--ink-faint)', width: 42, flex: 'none' }}>{t}</span>
                    <span style={{ width: 7, height: 7, borderRadius: 2, background: c, flex: 'none', position: 'relative', top: -1 }}></span>
                    <span style={{ color: 'var(--ink-soft)' }}>{what}</span>
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

/* ── ⌘K command palette ──────────────────────────────────────── */
function CommandPalette() {
  const rows = [
    { group: 'Jump to', items: [
      ['Sanne Bakker', 'Employee · HR', '#8B5CF6', false],
      ['Veldkamp Logistics — invoice #2026-114', 'Invoice · Finance', '#10B981', true],
      ['Atelier Noor', 'Account · CRM', '#F43F5E', false],
    ]},
    { group: 'Actions', items: [
      ['Approve leave requests', '3 waiting', '#8B5CF6', false],
      ['Create invoice', 'Finance', '#10B981', false],
      ['Switch on a module…', 'Billing', '#4F46E5', false],
    ]},
  ];
  return (
    <div className="pn" data-screen-label="Command palette ⌘K" style={{ gridTemplateColumns: '1fr', position: 'relative', height: 900 }}>
      {/* dimmed app behind */}
      <div style={{ position: 'absolute', inset: 0, padding: 28, filter: 'blur(2px)', opacity: 0.5 }}>
        <div className="pn-kpis">
          {[1, 2, 3, 4].map((i) => <div key={i} className="pn-kpi" style={{ height: 90 }}></div>)}
        </div>
        <div style={{ display: 'grid', gridTemplateColumns: '1.25fr 1fr', gap: 18, marginTop: 18 }}>
          <div className="pn-widget" style={{ height: 380 }}></div>
          <div className="pn-widget" style={{ height: 380 }}></div>
        </div>
      </div>
      <div style={{ position: 'absolute', inset: 0, background: 'rgba(17,24,39,0.4)', backdropFilter: 'blur(1px)' }}></div>
      <div style={{ position: 'absolute', left: '50%', top: 120, transform: 'translateX(-50%)', width: 640, background: '#fff', borderRadius: 16, boxShadow: '0 40px 90px -20px rgba(17,24,39,0.5)', overflow: 'hidden', border: '1px solid var(--line-strong)' }}>
        <div style={{ display: 'flex', alignItems: 'center', gap: 12, padding: '16px 22px', borderBottom: '1px solid var(--line)' }}>
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#4F46E5" strokeWidth="1.8" strokeLinecap="round"><circle cx="7" cy="7" r="4.5"></circle><path d="M10.5 10.5L14 14"></path></svg>
          <span style={{ fontSize: 15.5 }}>veld<span style={{ borderRight: '1.5px solid var(--ink)', paddingRight: 1 }}></span><span style={{ color: 'var(--ink-faint)' }}>kamp…</span></span>
          <span style={{ marginLeft: 'auto', fontFamily: "'JetBrains Mono', monospace", fontSize: 10, border: '1px solid var(--line)', borderRadius: 4, padding: '2px 6px', color: 'var(--ink-faint)' }}>ESC</span>
        </div>
        {rows.map((g) => (
          <div key={g.group}>
            <div style={{ padding: '10px 22px 4px', fontFamily: "'JetBrains Mono', monospace", fontSize: 9.5, letterSpacing: '0.18em', color: 'var(--ink-faint)' }}>{g.group.toUpperCase()}</div>
            {g.items.map(([t, sub, c, sel]) => (
              <div key={t} style={{ display: 'flex', alignItems: 'center', gap: 12, padding: '11px 22px', background: sel ? 'var(--violet-soft, #EEF2FF)' : 'transparent', borderLeft: '2px solid ' + (sel ? '#4F46E5' : 'transparent') }}>
                <span style={{ width: 9, height: 9, borderRadius: 3, background: c, flex: 'none' }}></span>
                <span style={{ fontSize: 14, fontWeight: 600, whiteSpace: 'nowrap' }}>{t}</span>
                <span style={{ marginLeft: 'auto', fontSize: 11.5, color: 'var(--ink-faint)', whiteSpace: 'nowrap' }}>{sub}</span>
                {sel && <span style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 10, color: 'var(--ink-faint)' }}>↵</span>}
              </div>
            ))}
          </div>
        ))}
        <div style={{ display: 'flex', gap: 18, padding: '12px 22px', borderTop: '1px solid var(--line)', background: '#FBFAF8', fontFamily: "'JetBrains Mono', monospace", fontSize: 10, color: 'var(--ink-faint)' }}>
          <span>↑↓ navigate</span><span>↵ open</span><span>tab actions</span>
          <span style={{ marginLeft: 'auto' }}>searches every module you can see</span>
        </div>
      </div>
    </div>
  );
}

/* ── Empty state — module just switched on (Support panel) ───── */
function EmptyStateTickets() {
  return (
    <div className="pn" data-screen-label="Empty state — Tickets just on" style={{ '--violet': '#F97316', '--violet-soft': '#FFF1E4' }}>
      <aside className="pn-side">
        <div className="pn-side-brand"><FFLogo light size={21}></FFLogo></div>
        <div className="pn-side-panel">SUPPORT · /SUPPORT</div>
        <nav className="pn-nav" style={{ marginTop: 8 }}>
          {['Tickets', 'Inbox rules', 'SLAs', 'Reports'].map((l, i) => (
            <span key={l} className={'pn-nav-item' + (i === 0 ? ' on' : '')}>
              <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round"><rect x="3" y="4" width="14" height="12" rx="2"></rect><path d="M3 8l7 4 7-4"></path></svg>
              {l}
            </span>
          ))}
        </nav>
        <div style={{ marginTop: 'auto' }}>
          <div className="pn-side-foot">
            <span className="ava" style={{ background: '#F97316' }}>E</span>
            <span>
              <span className="nm" style={{ display: 'block' }}>Eva Mulder</span>
              <span className="co">Veldkamp Logistics</span>
            </span>
          </div>
        </div>
      </aside>
      <div className="pn-main">
        <div className="pn-top">
          <div className="pn-crumb"><span>Support</span><span>/</span><span className="here">Tickets</span></div>
          <div className="pn-top-right"><span className="pn-ava" style={{ background: '#F97316' }}>E</span></div>
        </div>
        <div className="pn-body" style={{ display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
          <div style={{ width: 560, textAlign: 'center' }}>
            <div style={{ display: 'inline-flex', alignItems: 'center', gap: 12, border: '1px solid var(--line-strong)', background: '#fff', borderRadius: 999, padding: '10px 20px' }}>
              <span style={{ fontWeight: 600, fontSize: 13.5 }}>Tickets</span>
              <span className="ff-sw on sm" style={{ background: '#F97316' }}></span>
              <span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 9.5, letterSpacing: '0.14em', background: 'var(--violet-soft)', color: '#C2570B', borderRadius: 5, padding: '3px 8px', whiteSpace: 'nowrap' }}>JUST ON</span>
            </div>
            <h1 style={{ fontSize: 26, marginTop: 22 }}>Your support inbox, ready in two steps.</h1>
            <p style={{ marginTop: 10, fontSize: 14.5, lineHeight: 1.65, color: 'var(--ink-soft)' }}>Tickets is live and already linked to your CRM accounts — every conversation will show on the customer record automatically. It just needs a mailbox to listen to.</p>
            <div style={{ marginTop: 26, textAlign: 'left', border: '1px solid var(--line-strong)', borderRadius: 14, background: '#fff', overflow: 'hidden' }}>
              {[
                ['1', 'Connect your support address', 'support@veldkamp.eu — forwarding or IMAP', false],
                ['2', 'Set your first SLA', 'A reply-time promise per priority. One rule is enough to start.', false],
                ['✓', 'Linked to CRM accounts', 'Done automatically — both modules share the database', true],
              ].map(([n, t, sub, done]) => (
                <div key={t} style={{ display: 'flex', gap: 14, alignItems: 'center', padding: '14px 20px', borderBottom: '1px solid var(--line)', opacity: done ? 0.65 : 1 }}>
                  <span style={{ width: 26, height: 26, borderRadius: '50%', flex: 'none', display: 'flex', alignItems: 'center', justifyContent: 'center', fontFamily: "'JetBrains Mono', monospace", fontSize: 11, fontWeight: 700, background: done ? '#E5F5EE' : 'var(--violet-soft)', color: done ? '#0E8C61' : '#C2570B' }}>{n}</span>
                  <span style={{ textAlign: 'left' }}>
                    <span style={{ fontSize: 13.5, fontWeight: 600, display: 'block' }}>{t}</span>
                    <span style={{ fontSize: 12, color: 'var(--ink-faint)' }}>{sub}</span>
                  </span>
                  {!done && <span style={{ marginLeft: 'auto', color: '#C2570B', fontWeight: 600, fontSize: 12.5, whiteSpace: 'nowrap' }}>Start →</span>}
                </div>
              ))}
            </div>
            <div style={{ marginTop: 18, display: 'flex', gap: 12, justifyContent: 'center' }}>
              <span className="pn-btn" style={{ background: '#F97316' }}>Connect mailbox</span>
              <span className="pn-btn ghost">Import from Freshdesk</span>
            </div>
            <p className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 11, color: 'var(--ink-faint)', marginTop: 16 }}>€1,50/user/month from 1 July · switch off any time, data stays</p>
          </div>
        </div>
      </div>
    </div>
  );
}

Object.assign(window, { EmailTemplates, EmployeeProfile, CommandPalette, EmptyStateTickets });
