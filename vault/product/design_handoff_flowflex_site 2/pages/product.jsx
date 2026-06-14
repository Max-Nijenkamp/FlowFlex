/* Product overview + Domain detail (HR). Exports ProductDesktop, DomainDesktop. */
const PDD = window.FF_DATA;

const PD_DOMAINS = [
  {
    key: 'hr', name: 'HR & people', tagline: 'Recruiting to payroll, one record.',
    desc: 'Every employee is one profile shared by leave, payroll, onboarding and reviews. Approve a holiday and scheduling already knows.',
    mods: [['Employee profiles', 'included'], ['Leave & absence', '€1,50'], ['Payroll', '€2,50'], ['Recruiting', '€1,50'], ['Onboarding', '€1,00'], ['Time tracking', '€1,00']],
    flows: ['Offer accepted → salary lands in the next payroll run', 'Leave approved → shifts unassign, coverage flagged'],
  },
  {
    key: 'finance', name: 'Finance & accounting', tagline: 'Ledger-first books.',
    desc: 'Invoices, expenses and reporting on the same ledger your CRM and projects write to. Nothing is imported, so nothing is stale.',
    mods: [['Invoicing', '€2,00'], ['Expenses', '€1,00'], ['AP / AR', '€1,50'], ['Reporting', '€1,00']],
    flows: ['Deal won → draft invoice with the deal value on it', 'Invoice paid → account lifetime value updates'],
  },
  {
    key: 'crm', name: 'CRM & sales', tagline: 'Pipeline to contract.',
    desc: 'Contacts, deals and pipeline that see support tickets, invoices and projects — because they live in the same database.',
    mods: [['Contacts', 'included'], ['Pipeline', '€1,50'], ['Deals & quotes', '€1,50']],
    flows: ['Ticket spike → account health drops before renewal', 'Quote signed → project kickoff scaffolded'],
  },
  {
    key: 'projects', name: 'Projects & work', tagline: 'Boards, sprints, time.',
    desc: 'Kanban, sprints and time tracking with real awareness of who is on leave, what was invoiced, and which deal this work came from.',
    mods: [['Projects & boards', '€1,50'], ['Sprints', '€1,00'], ['Time tracking', '€1,00']],
    flows: ['Hours logged → invoice lines drafted for billable work', 'Leave approved → assignments flagged for handover'],
  },
];

function PdDomainSection({ d, i }) {
  const color = PDD.domainColors[d.key];
  return (
    <section className="ff-section" style={{ background: i % 2 ? 'var(--card)' : 'transparent' }}>
      <div className="wrap">
        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 64, alignItems: 'start' }}>
          <div>
            <p className="ff-tag"><b>{String(i + 1).padStart(2, '0')}</b> / DOMAIN</p>
            <h2 style={{ display: 'flex', alignItems: 'center', gap: 14 }}>
              <span style={{ width: 14, height: 14, borderRadius: 4, background: color, flex: 'none' }}></span>
              {d.name}
            </h2>
            <p className="ff-lede">{d.desc}</p>
            <span className="ff-arrlink" style={{ marginTop: 22, display: 'inline-flex' }}>Explore {d.name} <span className="arr">→</span></span>
            <div style={{ marginTop: 28 }}>
              <p className="ff-tag" style={{ letterSpacing: '0.16em' }}>FLOWS AUTOMATICALLY</p>
              <div style={{ marginTop: 12, display: 'flex', flexDirection: 'column', gap: 10 }}>
                {d.flows.map((f) => (
                  <p key={f} style={{ display: 'flex', gap: 10, fontSize: 14.5, color: 'var(--ink-soft)', alignItems: 'baseline' }}>
                    <span style={{ width: 7, height: 7, borderRadius: 2, background: color, flex: 'none', position: 'relative', top: -1 }}></span>{f}
                  </p>
                ))}
              </div>
            </div>
          </div>
          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
            {d.mods.map(([name, price]) => (
              <div key={name} style={{ border: '1px solid var(--line-strong)', borderRadius: 12, background: i % 2 ? 'var(--paper)' : 'var(--card)', padding: '16px 18px', display: 'flex', flexDirection: 'column', gap: 6 }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                  <span style={{ fontWeight: 600, fontSize: 14 }}>{name}</span>
                  <FFSw on={price === 'included'} sm></FFSw>
                </div>
                <span className="mono" style={{ fontSize: 11.5, color: 'var(--ink-faint)' }}>{price === 'included' ? 'included' : price + '/user'}</span>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}

function ProductDesktop() {
  return (
    <div className="ff" data-screen-label="Product overview desktop">
      <FFNav active="Product"></FFNav>
      <section className="ff-hero ff-grid-bg">
        <div className="wrap">
          <span className="ff-kicker"><span className="sq"></span>Product</span>
          <h1 style={{ maxWidth: 720 }}>Four departments today.<br></br>The rest is <span className="u">already wired</span>.</h1>
          <p className="ff-lede">Every module below ships today. Each one is a switch on your billing page — not a sales call, not an implementation project.</p>
        </div>
      </section>
      {PD_DOMAINS.map((d, i) => <PdDomainSection key={d.key} d={d} i={i}></PdDomainSection>)}
      <section className="ff-section ff-grid-bg">
        <div className="wrap">
          <p className="ff-tag"><b>05</b> / NEXT IN LINE</p>
          <h2>Waiting on the switchboard.</h2>
          <p className="ff-lede">Twelve more departments share the same database and the same pricing model, rolling out domain by domain.</p>
          <div style={{ marginTop: 40, display: 'flex', flexWrap: 'wrap', gap: 10 }}>
            {PDD.domains.slice(4, 14).map((d) => (
              <span key={d.key} className="ff-dompill" style={{ borderStyle: 'dashed', background: 'transparent' }}>
                <span className="chip" style={{ background: PDD.domainColors[d.key] }}></span>
                {d.name}
                <span className="mono" style={{ fontSize: 10, color: 'var(--ink-faint)' }}>soon</span>
              </span>
            ))}
          </div>
        </div>
      </section>
      <FFBand title="Only pay for the rows you need." sub="Start with one domain. The rest will be one switch away."></FFBand>
      <FFFooter></FFFooter>
    </div>
  );
}

/* ── Domain detail: HR & people ──────────────────────────────── */
const HR_MODULES = [
  { name: 'Employee profiles', price: 'included', on: true, desc: 'One record per person — contracts, documents, history.' },
  { name: 'Leave & absence', price: '€1,50', on: true, desc: 'Requests, balances, approval chains, team calendar.' },
  { name: 'Payroll', price: '€2,50', on: false, desc: 'Salary runs that read contracts and approved leave.' },
  { name: 'Recruiting', price: '€1,50', on: false, desc: 'Vacancies, candidate pipeline, structured scoring.' },
  { name: 'Onboarding', price: '€1,00', on: false, desc: 'Checklists that provision IT, LMS and payroll in one go.' },
  { name: 'Time tracking', price: '€1,00', on: false, desc: 'Hours that flow into payroll and project billing.' },
];

const HR_FLOWS = [
  { from: 'HR', to: 'Payroll', event: 'Offer accepted', effect: 'The salary lands in the next payroll run' },
  { from: 'HR', to: 'Scheduling', event: 'Leave approved', effect: 'Shifts unassign and coverage gaps get flagged' },
  { from: 'HR', to: 'IT', event: 'Onboarding started', effect: 'Accounts and hardware provisioning kick off' },
  { from: 'LMS', to: 'HR', event: 'Course completed', effect: 'The certification shows on the employee profile' },
];

function DomainDesktop() {
  const violet = PDD.domainColors.hr;
  return (
    <div className="ff" data-screen-label="Domain detail — HR desktop">
      <FFNav active="Product"></FFNav>
      <section className="ff-hero ff-grid-bg" style={{ paddingBottom: 76 }}>
        <div className="wrap">
          <div className="ff-crumb"><span>Product</span><span>/</span><span className="here">HR &amp; people</span></div>
          <h1 style={{ display: 'flex', alignItems: 'center', gap: 18, marginTop: 22 }}>
            <span style={{ width: 18, height: 18, borderRadius: 5, background: violet, flex: 'none' }}></span>
            HR &amp; people
          </h1>
          <p className="ff-lede">Recruiting to payroll on one employee record. Six modules, each its own switch — most teams start with two.</p>
          <div className="ff-hero-ctas">
            <span className="ff-btn primary lg">Price these modules</span>
            <span className="ff-arrlink">See all departments <span className="arr">→</span></span>
          </div>
        </div>
      </section>
      <section className="ff-section" style={{ background: 'var(--card)' }}>
        <div className="wrap">
          <p className="ff-tag"><b>01</b> / MODULES</p>
          <h2>What's in HR &amp; people.</h2>
          <div style={{ marginTop: 48, display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 14 }}>
            {HR_MODULES.map((m) => (
              <div key={m.name} className={'ff-tile' + (m.on ? '' : ' off')} style={{ padding: 22 }}>
                <div className="top">
                  <span className="chip" style={{ background: violet }}><span></span></span>
                  <span className={'ff-state ' + (m.on ? 'on' : 'off')}>{m.on ? 'ON' : 'OFF'}</span>
                </div>
                <div className="nm" style={{ fontSize: 15.5 }}>{m.name}</div>
                <p style={{ fontSize: 13.5, lineHeight: 1.55, color: 'var(--ink-soft)', marginTop: 6 }}>{m.desc}</p>
                <div className="pr" style={{ marginTop: 12 }}>{m.price === 'included' ? 'included' : m.price + '/user/month'}</div>
              </div>
            ))}
          </div>
        </div>
      </section>
      <FFFlowBand tag="02" title="HR tells the rest of the company itself." lede="No exports, no Zapier. These happen because everything shares one database." flows={HR_FLOWS}></FFFlowBand>
      <section className="ff-section">
        <div className="wrap">
          <p className="ff-tag"><b>03</b> / PLAYS WELL WITH</p>
          <h2>Strongest alongside.</h2>
          <div style={{ marginTop: 36, display: 'flex', flexWrap: 'wrap', gap: 10 }}>
            {[['finance', 'Finance — payroll & expense flows'], ['projects', 'Projects — capacity & time'], ['lms', 'Learning — certifications'], ['it', 'IT — provisioning']].map(([k, label]) => (
              <span key={k} className="ff-dompill">
                <span className="chip" style={{ background: PDD.domainColors[k] }}></span>{label}
              </span>
            ))}
          </div>
        </div>
      </section>
      <FFBand title="Start with HR. Grow from there." sub="Employee profiles are free — add leave for €1,50 per person and you're running."></FFBand>
      <FFFooter></FFFooter>
    </div>
  );
}

Object.assign(window, { ProductDesktop, DomainDesktop });
