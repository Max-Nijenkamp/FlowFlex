/* Filament panel screens — HR dashboard + Employees CRUD index (brand skin).
   Exports PanelDashboard, PanelCrud. 1440×960 frames. */

const PN_ICONS = {
  dash: <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round"><rect x="3" y="3" width="6" height="6" rx="1.5"></rect><rect x="11" y="3" width="6" height="6" rx="1.5"></rect><rect x="3" y="11" width="6" height="6" rx="1.5"></rect><rect x="11" y="11" width="6" height="6" rx="1.5"></rect></svg>,
  people: <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round"><circle cx="7.5" cy="7" r="3"></circle><path d="M2.5 17c.6-3 2.6-4.5 5-4.5s4.4 1.5 5 4.5M13.5 4.5a3 3 0 010 5M15 12.7c1.6.6 2.6 1.9 3 4.3"></path></svg>,
  cal: <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round"><rect x="3" y="4.5" width="14" height="12.5" rx="2"></rect><path d="M3 8.5h14M7 3v3M13 3v3"></path></svg>,
  pay: <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round"><rect x="2.5" y="5" width="15" height="10" rx="2"></rect><circle cx="10" cy="10" r="2.4"></circle></svg>,
  onb: <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round"><path d="M4 10.5l4 4 8-9"></path></svg>,
  doc: <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round"><path d="M5 3h7l3 3v11H5z"></path><path d="M12 3v3h3M8 10h4M8 13h4"></path></svg>,
};

function PnSidebar({ active }) {
  const nav = [
    { group: 'Overview', items: [['Dashboard', 'dash']] },
    { group: 'People', items: [['Employees', 'people'], ['Leave & absence', 'cal'], ['Onboarding', 'onb']] },
    { group: 'Compensation', items: [['Payroll', 'pay'], ['Contracts', 'doc']] },
  ];
  return (
    <aside className="pn-side">
      <div className="pn-side-brand">
        <FFLogo light size={21}></FFLogo>
      </div>
      <div className="pn-side-panel">HR &amp; PEOPLE · /HR</div>
      {nav.map((g) => (
        <React.Fragment key={g.group}>
          <div className="pn-nav-group">{g.group}</div>
          <nav className="pn-nav">
            {g.items.map(([label, ic]) => (
              <span key={label} className={'pn-nav-item' + (label === active ? ' on' : '')}>
                {PN_ICONS[ic]}{label}
              </span>
            ))}
          </nav>
        </React.Fragment>
      ))}
      <div style={{ marginTop: 'auto' }}>
        <div className="pn-nav-group">Your panels</div>
        <div className="pn-panels">
          {[['HR', true], ['FIN'], ['CRM'], ['PRJ'], ['APP']].map(([p, on]) => (
            <span key={p} className={on ? 'on' : ''}>{p}</span>
          ))}
        </div>
        <div className="pn-side-foot">
          <span className="ava">M</span>
          <span>
            <span className="nm" style={{ display: 'block' }}>Marieke Jansen</span>
            <span className="co">Veldkamp Logistics</span>
          </span>
        </div>
      </div>
    </aside>
  );
}

function PnTop({ crumb }) {
  return (
    <div className="pn-top">
      <div className="pn-crumb">
        <span>HR &amp; people</span><span>/</span>
        {crumb.map((c, i) => <span key={c} className={i === crumb.length - 1 ? 'here' : ''}>{c}</span>)}
      </div>
      <div className="pn-search">
        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round"><circle cx="7" cy="7" r="4.5"></circle><path d="M10.5 10.5L14 14"></path></svg>
        Search employees, requests…
        <span className="kbd">⌘K</span>
      </div>
      <div className="pn-top-right">
        <span className="pn-iconbtn">
          <svg width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round"><path d="M10 3a5 5 0 015 5c0 4 1.5 5 1.5 5h-13S5 12 5 8a5 5 0 015-5zM8.5 16.5a1.8 1.8 0 003 0"></path></svg>
          <span className="ping"></span>
        </span>
        <span className="pn-ava" style={{ background: '#8B5CF6' }}>M</span>
      </div>
    </div>
  );
}

const PN_PEOPLE = [
  { nm: 'Sanne Bakker', sub: 'Warehouse lead', color: '#8B5CF6', when: '24–28 Jun · 5 days', type: 'Holiday' },
  { nm: 'Daan Visser', sub: 'Account manager', color: '#F43F5E', when: '1–2 Jul · 2 days', type: 'Personal' },
  { nm: 'Lotte Smit', sub: 'Finance', color: '#10B981', when: '8 Jul · 1 day', type: 'Holiday' },
];

function PanelDashboard() {
  return (
    <div className="pn" data-screen-label="Filament — HR dashboard">
      <PnSidebar active="Dashboard"></PnSidebar>
      <div className="pn-main">
        <PnTop crumb={['Dashboard']}></PnTop>
        <div className="pn-body" style={{ display: 'flex', flexDirection: 'column', gap: 18 }}>
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-end' }}>
            <div>
              <h1 style={{ fontSize: 23 }}>Good morning, Marieke</h1>
              <p style={{ marginTop: 4, fontSize: 13.5, color: 'var(--ink-faint)' }}>Thursday 12 June · 3 approvals waiting for you</p>
            </div>
            <span className="pn-btn">+ New employee</span>
          </div>

          <div className="pn-kpis">
            {[
              { k: 'Headcount', v: '127', d: '+4 this month', up: true },
              { k: 'On leave today', v: '6', d: '2 return tomorrow', dim: true },
              { k: 'Open positions', v: '5', d: '23 candidates in pipeline', dim: true },
              { k: 'Onboarding', v: '3', d: 'next start · Mon 16 Jun', dim: true },
            ].map((s) => (
              <div className="pn-kpi" key={s.k}>
                <span className="corner"></span>
                <div className="k">{s.k}</div>
                <div className="v">{s.v}</div>
                <div className={'d ' + (s.up ? 'up' : 'dim')}>{s.d}</div>
              </div>
            ))}
          </div>

          <div style={{ display: 'grid', gridTemplateColumns: '1.25fr 1fr', gap: 18, alignItems: 'start' }}>
            <div style={{ display: 'flex', flexDirection: 'column', gap: 18 }}>
            <div className="pn-widget">
              <div className="pn-widget-head">
                <h3>Leave requests · awaiting approval</h3>
                <span className="all">View all →</span>
              </div>
              {PN_PEOPLE.map((p) => (
                <div className="pn-leave-row" key={p.nm}>
                  <span className="pn-ava" style={{ background: p.color }}>{p.nm[0]}</span>
                  <span className="who">
                    <span className="nm" style={{ display: 'block' }}>{p.nm}</span>
                    <span className="sub">{p.sub} · {p.type}</span>
                  </span>
                  <span className="when">{p.when}</span>
                  <span style={{ display: 'flex', gap: 6 }}>
                    <span className="pn-btn xs ok">Approve</span>
                    <span className="pn-btn xs ghost">Deny</span>
                  </span>
                </div>
              ))}
              <div style={{ padding: '10px 20px', background: '#FAF9F5', fontSize: 12, color: 'var(--ink-faint)', display: 'flex', gap: 6, alignItems: 'center' }}>
                <span style={{ width: 7, height: 7, borderRadius: 2, background: '#38BDF8' }}></span>
                Approvals flow to scheduling automatically — coverage gaps get flagged.
              </div>
            </div>

            <div className="pn-widget">
              <div className="pn-widget-head"><h3>Recent activity</h3><span className="all">Audit log →</span></div>
              {[
                ['09:42', 'Femke de Boer', 'approved leave for Sanne Bakker (24–28 Jun)', '#8B5CF6'],
                ['09:15', 'System', 'payroll draft for June created · 127 employees', '#38BDF8'],
                ['08:51', 'Tom de Vries', 'moved Yusuf Demir to onboarding step 4 of 5', '#6366F1'],
                ['08:30', 'Eva Mulder', 'accepted workspace invitation', '#EC4899'],
              ].map(([t, who, what, c]) => (
                <div key={t} style={{ display: 'flex', gap: 12, alignItems: 'baseline', padding: '10px 20px', borderBottom: '1px solid var(--line)', fontSize: 13 }}>
                  <span className="mono" style={{ fontSize: 11, color: 'var(--ink-faint)', flex: 'none' }}>{t}</span>
                  <span style={{ width: 7, height: 7, borderRadius: 2, background: c, flex: 'none', position: 'relative', top: -1 }}></span>
                  <span style={{ color: 'var(--ink-soft)' }}><b style={{ color: 'var(--ink)', fontWeight: 600 }}>{who}</b> {what}</span>
                </div>
              ))}
            </div>
            </div>

            <div style={{ display: 'flex', flexDirection: 'column', gap: 18 }}>
              <div className="pn-widget">
                <div className="pn-widget-head"><h3>Out this week</h3><span className="all">Calendar →</span></div>
                <div className="pn-week">
                  {[
                    { dt: 'MON 16', out: [['Sanne B.', '#F3EEFE', '#6D28D9']] },
                    { dt: 'TUE 17', out: [['Sanne B.', '#F3EEFE', '#6D28D9'], ['Tom V.', '#FDF1DC', '#B45309']] },
                    { dt: 'WED 18', out: [['Sanne B.', '#F3EEFE', '#6D28D9']] },
                    { dt: 'THU 19', out: [] },
                    { dt: 'FRI 20', out: [['Lotte S.', '#E5F5EE', '#0E8C61']] },
                  ].map((d) => (
                    <div className="pn-day" key={d.dt}>
                      <span className="dt">{d.dt}</span>
                      {d.out.map(([nm, bg, fg]) => (
                        <span className="chip" key={nm} style={{ background: bg, color: fg }}>{nm}</span>
                      ))}
                    </div>
                  ))}
                </div>
              </div>
              <div className="pn-widget">
                <div className="pn-widget-head"><h3>Onboarding in progress</h3><span className="all">All →</span></div>
                {[
                  { nm: 'Yusuf Demir', role: 'Driver · starts Mon 16 Jun', pct: 80, color: '#06B6D4' },
                  { nm: 'Eva Mulder', role: 'Support · starts 1 Jul', pct: 45, color: '#EC4899' },
                ].map((o) => (
                  <div key={o.nm} style={{ padding: '12px 20px', borderBottom: '1px solid var(--line)' }}>
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                      <span style={{ display: 'flex', gap: 10, alignItems: 'center' }}>
                        <span className="pn-ava" style={{ background: o.color, width: 26, height: 26, fontSize: 11 }}>{o.nm[0]}</span>
                        <span>
                          <span style={{ fontSize: 13, fontWeight: 600, display: 'block' }}>{o.nm}</span>
                          <span style={{ fontSize: 11.5, color: 'var(--ink-faint)' }}>{o.role}</span>
                        </span>
                      </span>
                      <span className="mono" style={{ fontSize: 11, color: 'var(--ink-soft)' }}>{o.pct}%</span>
                    </div>
                    <div style={{ marginTop: 8, height: 5, borderRadius: 3, background: 'var(--line)' }}>
                      <div style={{ width: o.pct + '%', height: 5, borderRadius: 3, background: 'var(--violet)' }}></div>
                    </div>
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

const PN_ROWS = [
  { nm: 'Sanne Bakker', em: 'sanne@veldkamp.eu', role: 'Warehouse lead', dept: ['Operations', '#FB923C'], st: ['ok', 'Active'], date: '03-2021', color: '#8B5CF6', sel: true },
  { nm: 'Daan Visser', em: 'daan@veldkamp.eu', role: 'Account manager', dept: ['Sales', '#F43F5E'], st: ['ok', 'Active'], date: '11-2019', color: '#F43F5E' },
  { nm: 'Lotte Smit', em: 'lotte@veldkamp.eu', role: 'Controller', dept: ['Finance', '#10B981'], st: ['leave', 'On leave'], date: '06-2022', color: '#10B981' },
  { nm: 'Yusuf Demir', em: 'yusuf@veldkamp.eu', role: 'Driver', dept: ['Operations', '#FB923C'], st: ['onb', 'Onboarding'], date: '06-2026', color: '#06B6D4' },
  { nm: 'Eva Mulder', em: 'eva@veldkamp.eu', role: 'Support agent', dept: ['Support', '#F97316'], st: ['onb', 'Onboarding'], date: '07-2026', color: '#EC4899' },
  { nm: 'Tom de Vries', em: 'tom@veldkamp.eu', role: 'Operations director', dept: ['Operations', '#FB923C'], st: ['ok', 'Active'], date: '01-2018', color: '#6366F1' },
  { nm: 'Femke de Boer', em: 'femke@veldkamp.eu', role: 'HR officer', dept: ['HR', '#8B5CF6'], st: ['ok', 'Active'], date: '09-2023', color: '#8B5CF6' },
  { nm: 'Ruben Kok', em: 'ruben@veldkamp.eu', role: 'Planner', dept: ['Operations', '#FB923C'], st: ['off', 'Offboarding'], date: '05-2020', color: '#64748B' },
];

function PanelCrud() {
  return (
    <div className="pn" data-screen-label="Filament — Employees CRUD">
      <PnSidebar active="Employees"></PnSidebar>
      <div className="pn-main">
        <PnTop crumb={['Employees']}></PnTop>
        <div className="pn-body" style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-end' }}>
            <div>
              <h1 style={{ fontSize: 23 }}>Employees</h1>
              <p style={{ marginTop: 4, fontSize: 13.5, color: 'var(--ink-faint)' }}>127 people · one record each, shared by every module</p>
            </div>
            <div style={{ display: 'flex', gap: 10 }}>
              <span className="pn-btn ghost">Export</span>
              <span className="pn-btn">+ New employee</span>
            </div>
          </div>

          <div className="pn-widget">
            <div className="pn-tabs" style={{ padding: '0 12px' }}>
              {[['All', 127, true], ['Active', 116], ['On leave', 6], ['Onboarding', 3], ['Offboarding', 2]].map(([t, ct, on]) => (
                <span key={t} className={'pn-tab' + (on ? ' on' : '')}>{t}<span className="ct">{ct}</span></span>
              ))}
              <span style={{ marginLeft: 'auto', display: 'flex', alignItems: 'center', gap: 8, padding: '8px 4px' }}>
                <span className="pn-btn xs ghost">
                  <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round"><path d="M2 4h12M4.5 8h7M7 12h2"></path></svg>
                  Filters
                </span>
                <span className="pn-btn xs ghost">Columns</span>
              </span>
            </div>
            <div style={{ padding: '8px 16px', borderBottom: '1px solid var(--line)', display: 'flex', alignItems: 'center', gap: 10, background: 'var(--violet-soft)' }}>
              <span className="pn-check on"></span>
              <span style={{ fontSize: 12.5, fontWeight: 600, color: '#6D28D9' }}>1 selected</span>
              <span style={{ fontSize: 12.5, color: 'var(--ink-soft)' }}>Assign to department · Export · Deactivate</span>
            </div>
            <table className="pn-table">
              <thead>
                <tr>
                  <th style={{ width: 36 }}></th>
                  <th>Employee</th><th>Role</th><th>Department</th><th>Status</th><th>Started</th><th style={{ width: 40 }}></th>
                </tr>
              </thead>
              <tbody>
                {PN_ROWS.map((r) => (
                  <tr key={r.nm} className={r.sel ? 'sel' : ''}>
                    <td><span className={'pn-check' + (r.sel ? ' on' : '')}></span></td>
                    <td>
                      <span style={{ display: 'flex', gap: 10, alignItems: 'center' }}>
                        <span className="pn-ava" style={{ background: r.color, width: 28, height: 28, fontSize: 11 }}>{r.nm[0]}</span>
                        <span>
                          <span className="nm" style={{ display: 'block' }}>{r.nm}</span>
                          <span className="sub">{r.em}</span>
                        </span>
                      </span>
                    </td>
                    <td>{r.role}</td>
                    <td><span className="pn-dept"><span className="sq" style={{ background: r.dept[1] }}></span>{r.dept[0]}</span></td>
                    <td><span className={'pn-pill ' + r.st[0]}><span className="dot"></span>{r.st[1]}</span></td>
                    <td className="mono" style={{ fontSize: 12, color: 'var(--ink-soft)' }}>{r.date}</td>
                    <td style={{ color: 'var(--ink-faint)' }}>⋯</td>
                  </tr>
                ))}
              </tbody>
            </table>
            <div className="pn-foot">
              <span>Showing 1–8 of 127</span>
              <div className="pn-pages">
                <span className="pn-page">←</span>
                <span className="pn-page on">1</span>
                <span className="pn-page">2</span>
                <span className="pn-page">3</span>
                <span className="pn-page">…</span>
                <span className="pn-page">16</span>
                <span className="pn-page">→</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

Object.assign(window, { PanelDashboard, PanelCrud, PnSidebar, PnTop });
