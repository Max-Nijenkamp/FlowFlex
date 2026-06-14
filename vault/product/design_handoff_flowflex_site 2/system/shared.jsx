/* Shared FlowFlex site components — logo, nav, footer, flow band, strip.
   Exports to window: FFLogo, FFNav, FFFooter, FFStrip, FFFlowBand, FFBand, FFSw, ffEuro */
const FFD = window.FF_DATA;

function FFLogo({ light, size = 25, mono }) {
  const ink = light ? '#FFFFFF' : '#111827';
  return (
    <div style={{ display: 'flex', alignItems: 'center', gap: 9 }}>
      <svg width={size} height={size} viewBox="0 0 48 48" fill="none">
        <path d="M10 14 C10 9 14 6 19 6 L38 6 C41 6 42 8 42 10 C42 12 41 14 38 14 L20 14 C17 14 16 15 16 18 L16 30 C16 39 9 42 4 40" stroke={mono ? ink : '#4F46E5'} strokeWidth="6" strokeLinecap="round" fill="none"></path>
        <path d="M16 24 L31 24 C34 24 36 26 36 28.5 C36 31 34 33 31 33 L24 33 C20 33 18 35 18 38" stroke={mono ? ink : '#4F46E5'} strokeWidth="6" strokeLinecap="round" fill="none" opacity="0.45"></path>
      </svg>
      <span style={{ fontFamily: "'Archivo', sans-serif", fontWeight: 700, fontSize: size * 0.72, letterSpacing: '-0.02em', color: ink }}>FlowFlex</span>
    </div>
  );
}

function FFNav({ mobile, active }) {
  const links = ['Product', 'Pricing', 'About', 'Contact'];
  return (
    <div className="ff-nav-outer">
      <div className="wrap">
        <nav className="ff-nav">
          <FFLogo size={mobile ? 23 : 25}></FFLogo>
          {!mobile && (
            <div className="ff-nav-links">
              {links.map((l) => <span key={l} className={l === active ? 'on' : ''}>{l}</span>)}
            </div>
          )}
          <div style={{ display: 'flex', alignItems: 'center', gap: 18 }}>
            {!mobile && <span style={{ fontSize: 14.5, fontWeight: 500, color: 'var(--ink-soft)' }}>Sign in</span>}
            {mobile
              ? <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#111827" strokeWidth="1.8" strokeLinecap="round"><path d="M4 7h16M4 12h16M4 17h16"></path></svg>
              : <span className="ff-btn sm">Talk to us</span>}
          </div>
        </nav>
      </div>
    </div>
  );
}

function FFStrip() {
  const items = [...FFD.replaces, ...FFD.replaces];
  return (
    <div className="ff-strip">
      <span className="label">REPLACES</span>
      <div className="ff-strip-track">
        {items.map((r, i) => <span key={i}>{r}</span>)}
      </div>
    </div>
  );
}

/* Dark kinetic Flow band — heading + glowing event chain */
function FFFlowBand({ mobile, tag = '03', title = 'Data moves between departments on its own.', lede = "These aren't integrations you configure. They're how a single database behaves.", flows }) {
  const list = flows || FFD.flows;
  return (
    <section className="ff-flow">
      <div className="ff-flow-glow"></div>
      <div className="ff-section" style={{ borderBottom: 'none', position: 'relative' }}>
        <div className="wrap">
          <p className="ff-tag"><b>{tag}</b> / FLOW</p>
          <h2>{title}</h2>
          <p className="ff-lede">{lede}</p>
          <div className="ff-chain">
            {list.map((f, i) => (
              <div className={'ff-chain-row' + (i % 2 ? ' alt' : '')} key={f.event}>
                {!mobile && <span className="route">{f.from} → {f.to}</span>}
                <span className="node"><span className="ff-node-dot"></span></span>
                <span>
                  <span className="evt">{f.event}</span>
                  <span className="fx">{f.effect}</span>
                </span>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}

function FFBand({ title, sub, cta = 'Build your price' }) {
  return (
    <section className="ff-band">
      <div className="wrap">
        <h2>{title}</h2>
        <p>{sub}</p>
        <span className="ff-btn white lg">{cta}</span>
      </div>
    </section>
  );
}

function FFSw({ on, sm }) {
  return <span className={'ff-sw' + (on ? ' on' : '') + (sm ? ' sm' : '')}></span>;
}

function FFFooter({ mobile }) {
  return (
    <footer className="ff-footer">
      <div className="wrap">
        <div className="ff-footer-grid">
          <div>
            <FFLogo light size={22}></FFLogo>
            <p style={{ marginTop: 16, fontSize: 13.5, lineHeight: 1.6, color: 'rgba(255,255,255,0.5)', maxWidth: 240 }}>
              One platform. Every tool. Always flexible. Built for teams of 50 to 500.
            </p>
          </div>
          <div>
            <h4>Product</h4>
            <span className="lnk">All modules</span><span className="lnk">Pricing</span><span className="lnk">Sign in</span>
          </div>
          <div>
            <h4>Company</h4>
            <span className="lnk">About</span><span className="lnk">Contact</span>
          </div>
          <div>
            <h4>Legal</h4>
            <span className="lnk">Terms</span><span className="lnk">Privacy</span>
          </div>
        </div>
        <div className="ff-footer-base">
          <span>© 2026 FlowFlex — everything flows</span>
          <span>EU-hosted · GDPR-first · data portable</span>
        </div>
      </div>
    </footer>
  );
}

const ffEuro = (c) => '€' + (c / 100).toFixed(2).replace('.', ',');

Object.assign(window, { FFLogo, FFNav, FFFooter, FFStrip, FFFlowBand, FFBand, FFSw, ffEuro });
