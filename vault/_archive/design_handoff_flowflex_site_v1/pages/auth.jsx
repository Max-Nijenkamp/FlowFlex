/* Auth screens — customer login, admin login, forgot password, invite register.
   All 1440×900 fixed frames. Exports LoginCustomer, LoginAdmin, ForgotPassword, InviteRegister. */

function AuField({ label, value, ph, right, type }) {
  return (
    <label className="ff-field">
      <span className="lbl"><span>{label}</span>{right}</span>
      <span className="ff-input">{value ? <span>{type === 'pw' ? '••••••••••••' : value}</span> : <span className="ph">{ph}</span>}</span>
    </label>
  );
}

/* Dark brand panel with animated flow pulses */
function AuBrandPanel({ admin }) {
  const paths = [
    'M-20,120 C 180,120 240,250 460,250',
    'M-20,420 C 200,420 260,330 480,330',
    'M-20,640 C 220,640 280,480 500,480',
  ];
  return (
    <div style={{ position: 'relative', background: '#0E1320', color: '#fff', overflow: 'hidden', display: 'flex', flexDirection: 'column', justifyContent: 'space-between', padding: '52px 56px' }}>
      <div style={{ position: 'absolute', left: '50%', top: -260, transform: 'translateX(-50%)', width: 900, height: 560, background: 'radial-gradient(ellipse 50% 50% at 50% 50%, rgba(79,70,229,0.28), rgba(56,189,248,0.05) 55%, transparent 75%)', pointerEvents: 'none' }}></div>
      <svg className="ff-paths" style={{ position: 'absolute', inset: 0, width: '100%', height: '100%' }} viewBox="0 0 620 900" preserveAspectRatio="none">
        {paths.map((d, i) => (
          <g key={i}>
            <path d={d} fill="none" stroke="rgba(255,255,255,0.07)" strokeWidth="1.5"></path>
            <path d={d} fill="none" className="pulse" stroke={i % 2 ? 'rgba(56,189,248,0.8)' : 'rgba(139,137,255,0.8)'} strokeWidth="1.5" style={{ animationDelay: i * 1.4 + 's' }}></path>
          </g>
        ))}
      </svg>
      <div style={{ position: 'relative' }}>
        <FFLogo light size={26}></FFLogo>
      </div>
      <div style={{ position: 'relative' }}>
        {admin ? (
          <div>
            <p className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 11, letterSpacing: '0.24em', color: 'rgba(255,255,255,0.4)' }}>FLOWFLEX STAFF · /ADMIN</p>
            <h2 style={{ fontFamily: "'Archivo', sans-serif", fontSize: 38, letterSpacing: '-0.025em', marginTop: 14, lineHeight: 1.1 }}>Platform<br></br>operations.</h2>
            <p style={{ marginTop: 16, fontSize: 15, lineHeight: 1.65, color: 'rgba(255,255,255,0.55)', maxWidth: 320 }}>Module catalogue, company workspaces, billing and platform health — staff access only, fully audited.</p>
          </div>
        ) : (
          <div>
            <h2 style={{ fontFamily: "'Archivo', sans-serif", fontSize: 42, letterSpacing: '-0.025em', lineHeight: 1.08 }}>Everything<br></br>flows.</h2>
            <p style={{ marginTop: 16, fontSize: 15, lineHeight: 1.65, color: 'rgba(255,255,255,0.55)', maxWidth: 320 }}>One login for HR, finance, CRM and every other module your team switched on.</p>
          </div>
        )}
        <div style={{ marginTop: 28, display: 'flex', gap: 24, fontFamily: "'JetBrains Mono', monospace", fontSize: 11, color: 'rgba(255,255,255,0.38)' }}>
          <span>EU-hosted</span><span>·</span><span>GDPR-first</span><span>·</span><span>{admin ? 'full audit trail' : '2FA available'}</span>
        </div>
      </div>
    </div>
  );
}

function AuShell({ admin, children }) {
  return (
    <div className="ff" style={{ width: 1440, height: 900, display: 'grid', gridTemplateColumns: '620px 1fr' }}>
      <AuBrandPanel admin={admin}></AuBrandPanel>
      <div className="ff-grid-bg" style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', background: 'var(--paper)' }}>
        {children}
      </div>
    </div>
  );
}

function AuCard({ children, width = 420 }) {
  return (
    <div style={{ width, background: 'var(--card)', border: '1px solid var(--line-strong)', borderRadius: 20, padding: 40, boxShadow: '0 1px 2px rgba(17,24,39,0.04), 0 28px 56px -32px rgba(17,24,39,0.16)', display: 'flex', flexDirection: 'column', gap: 20 }}>
      {children}
    </div>
  );
}

function LoginCustomer() {
  return (
    <div data-screen-label="Login — customer">
      <AuShell>
        <AuCard>
          <div>
            <h1 style={{ fontFamily: "'Archivo', sans-serif", fontSize: 26, letterSpacing: '-0.02em', margin: 0 }}>Sign in to FlowFlex</h1>
            <p style={{ marginTop: 6, fontSize: 14.5, color: 'var(--ink-soft)' }}>Welcome back.</p>
          </div>
          <AuField label="Work email" value="marieke@veldkamp.eu"></AuField>
          <AuField label="Password" value="x" type="pw" right={<span style={{ fontSize: 12.5, fontWeight: 600, color: 'var(--indigo)' }}>Forgot it?</span>}></AuField>
          <span className="ff-check"><span className="box on"></span>Keep me signed in</span>
          <span className="ff-btn primary lg" style={{ width: '100%', boxSizing: 'border-box' }}>Sign in</span>
          <p style={{ textAlign: 'center', fontSize: 13, color: 'var(--ink-faint)', lineHeight: 1.6 }}>
            New here? FlowFlex workspaces are invite-only —<br></br><span style={{ color: 'var(--indigo)', fontWeight: 600 }}>talk to us</span> to get set up.
          </p>
        </AuCard>
      </AuShell>
    </div>
  );
}

function LoginAdmin() {
  return (
    <div data-screen-label="Login — admin (staff)">
      <AuShell admin>
        <AuCard>
          <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: 12 }}>
            <h1 style={{ fontFamily: "'Archivo', sans-serif", fontSize: 24, letterSpacing: '-0.02em', margin: 0, whiteSpace: 'nowrap' }}>Staff sign in</h1>
            <span className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 10, letterSpacing: '0.16em', background: '#111827', color: '#fff', borderRadius: 6, padding: '5px 10px' }}>/ADMIN</span>
          </div>
          <p style={{ fontSize: 14, color: 'var(--ink-soft)', margin: '-6px 0 0' }}>FlowFlex employees only. All sessions are audited.</p>
          <AuField label="Staff email" ph="you@flowflex.eu"></AuField>
          <AuField label="Password" ph="••••••••••••"></AuField>
          <div>
            <span className="lbl" style={{ display: 'flex', justifyContent: 'space-between', fontSize: 13.5, fontWeight: 600, marginBottom: 7 }}>Two-factor code</span>
            <div style={{ display: 'flex', gap: 8 }}>
              {['4', '7', '', '', '', ''].map((d, i) => (
                <span key={i} className="ff-input" style={{ width: 48, height: 52, display: 'flex', alignItems: 'center', justifyContent: 'center', fontFamily: "'JetBrains Mono', monospace", fontSize: 19, fontWeight: 700, padding: 0, borderColor: i === 2 ? 'var(--indigo)' : undefined, boxShadow: i === 2 ? '0 0 0 3px rgba(79,70,229,0.15)' : undefined }}>{d}</span>
              ))}
            </div>
          </div>
          <span className="ff-btn lg" style={{ width: '100%', boxSizing: 'border-box' }}>Sign in to admin</span>
          <p style={{ textAlign: 'center', fontSize: 12.5, color: 'var(--ink-faint)' }}>Lost your authenticator? Contact platform security.</p>
        </AuCard>
      </AuShell>
    </div>
  );
}

function ForgotPassword() {
  return (
    <div className="ff ff-grid-bg" data-screen-label="Forgot password" style={{ width: 1440, height: 900, display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', gap: 28 }}>
      <FFLogo size={28}></FFLogo>
      <AuCard width={440}>
        <div>
          <h1 style={{ fontFamily: "'Archivo', sans-serif", fontSize: 24, letterSpacing: '-0.02em', margin: 0 }}>Reset your password</h1>
          <p style={{ marginTop: 8, fontSize: 14.5, lineHeight: 1.6, color: 'var(--ink-soft)' }}>Type the work email you sign in with. If it has a FlowFlex account, a reset link is on its way.</p>
        </div>
        <AuField label="Work email" ph="you@company.com"></AuField>
        <span className="ff-btn primary lg" style={{ width: '100%', boxSizing: 'border-box' }}>Send reset link</span>
        <p style={{ textAlign: 'center', fontSize: 13, color: 'var(--ink-faint)' }}>Remembered it? <span style={{ color: 'var(--indigo)', fontWeight: 600 }}>Back to sign in</span></p>
      </AuCard>
      <p className="mono" style={{ fontFamily: "'JetBrains Mono', monospace", fontSize: 11, color: 'var(--ink-faint)' }}>EU-hosted · GDPR-first</p>
    </div>
  );
}

function InviteRegister() {
  return (
    <div data-screen-label="Invite registration">
      <AuShell>
        <AuCard>
          <div>
            <span className="ff-kicker" style={{ marginBottom: 16 }}><span className="sq"></span>You're invited</span>
            <h1 style={{ fontFamily: "'Archivo', sans-serif", fontSize: 24, letterSpacing: '-0.02em', margin: '14px 0 0' }}>Join Veldkamp Logistics</h1>
            <p style={{ marginTop: 6, fontSize: 14.5, lineHeight: 1.6, color: 'var(--ink-soft)' }}>Tom de Vries invited you to the Veldkamp workspace. Set a name and password and you're in.</p>
          </div>
          <label className="ff-field">
            <span className="lbl"><span>Work email</span></span>
            <span className="ff-input" style={{ background: 'var(--paper-deep)', color: 'var(--ink-soft)', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
              marieke@veldkamp.eu
              <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="#98A0AB" strokeWidth="1.6"><rect x="3" y="7" width="10" height="7" rx="1.5"></rect><path d="M5.5 7V5.5a2.5 2.5 0 015 0V7"></path></svg>
            </span>
          </label>
          <AuField label="Your name" value="Marieke Jansen"></AuField>
          <AuField label="Choose a password" value="x" type="pw"></AuField>
          <div style={{ display: 'flex', gap: 5, marginTop: -10 }}>
            {[1, 1, 1, 0].map((on, i) => <span key={i} style={{ flex: 1, height: 4, borderRadius: 2, background: on ? '#10B981' : 'var(--line)' }}></span>)}
            <span style={{ fontSize: 11.5, color: '#10B981', fontWeight: 600, marginLeft: 6 }}>strong</span>
          </div>
          <span className="ff-btn primary lg" style={{ width: '100%', boxSizing: 'border-box' }}>Create account &amp; join</span>
          <p style={{ textAlign: 'center', fontSize: 12.5, color: 'var(--ink-faint)' }}>By joining you accept the workspace's terms and our privacy policy.</p>
        </AuCard>
      </AuShell>
    </div>
  );
}

Object.assign(window, { LoginCustomer, LoginAdmin, ForgotPassword, InviteRegister });
