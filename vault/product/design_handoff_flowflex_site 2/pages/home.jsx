/* Home page — Switchboard+ final direction. Exports HomeDesktop, HomeMobile. */
const HMD = window.FF_DATA;

function HomeBoard({ compact }) {
  const rows = [
    { name: 'Employee profiles', domain: 'hr', price: 'included', on: true },
    { name: 'Leave & absence', domain: 'hr', price: '€1,50', on: true },
    { name: 'Payroll', domain: 'hr', price: '€2,50', on: false },
    { name: 'Invoicing', domain: 'finance', price: '€2,00', on: true },
    { name: 'Expenses', domain: 'finance', price: '€1,00', on: false },
    { name: 'Pipeline', domain: 'crm', price: '€1,50', on: true },
    { name: 'Projects & boards', domain: 'projects', price: '€1,50', on: false },
  ].slice(0, compact ? 5 : 7);
  return (
    <div className="ff-board">
      <div className="ff-board-head">
        <span className="t">Your modules</span>
        <span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace" }}>80 users</span>
      </div>
      <div>
        {rows.map((r) => (
          <div className={'ff-board-row' + (r.on ? '' : ' off')} key={r.name}>
            <span className="nm"><span className="chip" style={{ background: HMD.domainColors[r.domain] }}></span>{r.name}</span>
            <span className="pr">{r.price}{r.price !== 'included' && '/user'}</span>
            <FFSw on={r.on}></FFSw>
          </div>
        ))}
      </div>
      <div className="ff-board-total">
        <span className="f">€5,00/user × 80 users</span>
        <span className="v">€400<em>/month</em></span>
      </div>
    </div>
  );
}

function HomeProblem() {
  return (
    <section className="ff-section" style={{ background: 'var(--card)' }}>
      <div className="wrap">
        <p className="ff-tag"><b>01</b> / THE PATCHWORK TAX</p>
        <h2>Twelve tools, one company, and nothing talks to anything.</h2>
        <p className="ff-lede">Somewhere between 40 and 80 people, the cost of switching, syncing and re-typing quietly outgrows the cost of the tools themselves.</p>
        <div className="ff-cells">
          <div className="ff-cell">
            <span className="corner"></span>
            <div className="big">5–15</div>
            <h3>Separate tools at 100 people</h3>
            <p>Each with its own login, its own invoice, its own idea of who your employees are.</p>
          </div>
          <div className="ff-cell">
            <span className="corner"></span>
            <div className="big">×5</div>
            <h3>Forms per new hire</h3>
            <p>HR, payroll, IT, the LMS, the project tool. One person, five data entries, five chances to typo.</p>
          </div>
          <div className="ff-cell">
            <span className="corner"></span>
            <div className="big">0</div>
            <h3>Integrations to maintain</h3>
            <p>One database. There is nothing to glue together, so nothing breaks at 2am.</p>
          </div>
        </div>
      </div>
    </section>
  );
}

function HomeMarketplace({ mobile }) {
  const tiles = [
    { name: 'Employee profiles', domain: 'hr', price: 'included', on: true },
    { name: 'Leave & absence', domain: 'hr', price: '€1,50/user', on: true },
    { name: 'Invoicing', domain: 'finance', price: '€2,00/user', on: true },
    { name: 'Pipeline', domain: 'crm', price: '€1,50/user', on: true },
    { name: 'Payroll', domain: 'hr', price: '€2,50/user', on: false },
    { name: 'Expenses', domain: 'finance', price: '€1,00/user', on: false },
    { name: 'Tickets', domain: 'support', price: '€1,50/user', on: false },
  ].slice(0, mobile ? 5 : 7);
  return (
    <section className="ff-section ff-grid-bg">
      <div className="wrap">
        <p className="ff-tag"><b>02</b> / FLEX</p>
        <h2>Modules are switches, not sales calls.</h2>
        <p className="ff-lede">Flip one on and it's live immediately. Flip it off and billing stops at month-end — your data stays exactly where it was.</p>
        <div className="ff-tiles">
          {tiles.map((t) => (
            <div className={'ff-tile' + (t.on ? '' : ' off')} key={t.name}>
              <div className="top">
                <span className="chip" style={{ background: HMD.domainColors[t.domain] }}><span></span></span>
                <span className={'ff-state ' + (t.on ? 'on' : 'off')}>{t.on ? 'ON' : 'OFF'}</span>
              </div>
              <div className="nm">{t.name}</div>
              <div className="pr">{t.price}</div>
            </div>
          ))}
          <div className="ff-tile ghost">+ 65 more modules</div>
        </div>
      </div>
    </section>
  );
}

function HomeCoverage({ mobile }) {
  const doms = mobile ? HMD.domains.slice(0, 8) : HMD.domains.slice(0, 12);
  return (
    <section className="ff-section" style={{ background: 'var(--card)' }}>
      <div className="wrap">
        <p className="ff-tag"><b>04</b> / COVERAGE</p>
        <h2>Every department, already inside.</h2>
        <div className="ff-table">
          {doms.map((d) => (
            <div className="ff-trow" key={d.key}>
              <span className="chip" style={{ background: HMD.domainColors[d.key] }}></span>
              <span className="nm">{d.name}</span>
              <span className="ct">{d.modules} modules</span>
              {!mobile && <span className="go">explore →</span>}
            </div>
          ))}
        </div>
        <p style={{ marginTop: 16, fontFamily: "'JetBrains Mono', monospace", fontSize: 12, color: 'var(--ink-faint)' }}>+ {mobile ? 8 : 4} more departments · all on the same database</p>
      </div>
    </section>
  );
}

function HomePricingTeaser({ mobile }) {
  return (
    <section className="ff-section ff-grid-bg">
      <div className="wrap">
        <div style={{ display: 'grid', gridTemplateColumns: mobile ? '1fr' : '1fr 400px', gap: mobile ? 44 : 80, alignItems: 'center' }}>
          <div>
            <p className="ff-tag"><b>05</b> / PRICING</p>
            <h2>Your invoice is a list, not a tier.</h2>
            <p className="ff-lede">The sum of the modules you switched on, times the people on your team. The per-module price is identical at 50 users or 500 — you pay for more seats, never a higher tier.</p>
            <div className="ff-hero-ctas">
              <span className="ff-btn primary lg">Build your price</span>
            </div>
          </div>
          <div className="ff-receipt" style={{ transform: 'rotate(0.6deg)' }}>
            <div className="rt">FLOWFLEX · MONTHLY</div>
            <div style={{ height: 14 }}></div>
            <div className="rl head"><span>module</span><span>/user</span></div>
            <div className="rl"><span>Employee profiles</span><span>€0,00</span></div>
            <div className="rl"><span>Leave &amp; absence</span><span>€1,50</span></div>
            <div className="rl"><span>Invoicing</span><span>€2,00</span></div>
            <div className="rl"><span>Pipeline</span><span>€1,50</span></div>
            <div className="rl total"><span>€5,00 × 80 users</span><span>€400</span></div>
            <div style={{ height: 8 }}></div>
            <div className="rl" style={{ justifyContent: 'center', fontSize: 11, color: 'var(--ink-faint)', whiteSpace: 'normal', textAlign: 'center' }}>change modules any month · no contracts</div>
          </div>
        </div>
      </div>
    </section>
  );
}

function HomeDesktop() {
  return (
    <div className="ff" data-screen-label="Home desktop">
      <FFNav active="Product"></FFNav>
      <section className="ff-hero ff-grid-bg">
        <div className="wrap">
          <div style={{ display: 'grid', gridTemplateColumns: '1.05fr 1fr', gap: 64, alignItems: 'center' }}>
            <div>
              <span className="ff-kicker"><span className="sq"></span>Per user · per module</span>
              <h1>Run everything.<br></br>Pay for what's <span className="u">switched on</span>.</h1>
              <p className="ff-lede">HR, finance, CRM and 70 more modules on one database. Each one is a switch on your billing page — flip it on when you need it, off when you don't.</p>
              <div className="ff-hero-ctas">
                <span className="ff-btn primary lg">Build your price</span>
                <span className="ff-btn outline lg">See the modules</span>
              </div>
              <p className="ff-hero-meta">teams of 50–500 · no tiers · no lock-in · data portable</p>
            </div>
            <HomeBoard></HomeBoard>
          </div>
        </div>
      </section>
      <FFStrip></FFStrip>
      <HomeProblem></HomeProblem>
      <HomeMarketplace></HomeMarketplace>
      <FFFlowBand></FFFlowBand>
      <HomeCoverage></HomeCoverage>
      <HomePricingTeaser></HomePricingTeaser>
      <FFBand title="Switch on what you need. Nothing else." sub="See what your stack would cost on one platform — it takes about a minute."></FFBand>
      <FFFooter></FFFooter>
    </div>
  );
}

function HomeMobile() {
  return (
    <div className="ff ff-m" data-screen-label="Home mobile">
      <FFNav mobile></FFNav>
      <section className="ff-hero ff-grid-bg" style={{ padding: '54px 0 64px' }}>
        <div className="wrap">
          <span className="ff-kicker"><span className="sq"></span>Per user · per module</span>
          <h1>Run everything. Pay for what's <span className="u">switched on</span>.</h1>
          <p className="ff-lede" style={{ fontSize: 16 }}>HR, finance, CRM and 70 more modules on one database — each one a switch on your billing page.</p>
          <div className="ff-hero-ctas" style={{ flexDirection: 'column', alignItems: 'stretch' }}>
            <span className="ff-btn primary lg">Build your price</span>
            <span className="ff-btn outline lg">See the modules</span>
          </div>
          <div style={{ marginTop: 44 }}>
            <HomeBoard compact></HomeBoard>
          </div>
        </div>
      </section>
      <HomeProblem></HomeProblem>
      <HomeMarketplace mobile></HomeMarketplace>
      <FFFlowBand mobile></FFFlowBand>
      <HomeCoverage mobile></HomeCoverage>
      <FFBand title="Switch on what you need." sub="See what your stack would cost on one platform."></FFBand>
      <FFFooter mobile></FFFooter>
    </div>
  );
}

Object.assign(window, { HomeDesktop, HomeMobile });
