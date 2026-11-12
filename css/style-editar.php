:root {
    /* Paleta da referência */
    --skin: #FFC6A8;        /* Skin Tone */
    --bridal: #741A2F;      /* Bridal */

    /* Derivadas */
    --bridal-dark: #571223;
    --skin-light: #FFE4D6;
    --skin-soft: #FFD6C0;
    --error: #9E1B32;

    --font-display: "Anton", "Impact", "Arial Narrow", sans-serif;
    --font-body: "DM Sans", system-ui, -apple-system, "Segoe UI", sans-serif;
}

*, *::before, *::after { box-sizing: border-box; }

html, body { height: 100%; }

body {
    margin: 0;
    font-family: var(--font-body);
    color: var(--bridal);
    background: var(--skin);
    -webkit-font-smoothing: antialiased;
}

/* ---------- Estrutura: dois blocos de cor, como na referência ---------- */
.page {
    min-height: 100vh;
    min-height: 100dvh;
    display: grid;
    grid-template-columns: 1fr;
}

.intro {
    background: var(--bridal);
    color: var(--skin);
    padding: 2.5rem 1.5rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.intro-inner { width: 100%; max-width: 30rem; margin: 0 auto; }

.intro h1 {
    margin: 0 0 1rem;
    font-family: var(--font-display);
    font-weight: 400;
    font-size: clamp(3rem, 12vw, 6.5rem);
    line-height: 0.95;
    letter-spacing: 0.01em;
    text-transform: uppercase;
}

.intro p {
    margin: 0;
    max-width: 26rem;
    font-size: 1.05rem;
    line-height: 1.6;
    color: var(--skin-soft);
}

.panel {
    background: var(--skin);
    padding: 2rem 1.5rem 3rem;
    display: flex;
    align-items: center;
}

.form-wrap { width: 100%; max-width: 30rem; margin: 0 auto; }

/* ---------- Campos ---------- */
.field { margin-bottom: 1.5rem; }

label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 700;
    font-size: 0.95rem;
}

.hint {
    display: block;
    margin-top: 0.4rem;
    font-size: 0.85rem;
    color: var(--bridal-dark);
    opacity: 0.8;
}

input {
    width: 100%;
    padding: 0.9rem 1.1rem;
    font: inherit;
    font-size: 1rem;
    color: var(--bridal-dark);
    background: var(--skin-light);
    border: 2px solid var(--bridal);
    border-radius: 999px;          /* pílula, igual aos campos Hex da referência */
    transition: background-color .15s ease, box-shadow .15s ease;
}

input::placeholder { color: var(--bridal); opacity: 0.5; }

input:hover { background: #FFEBDF; }

input:focus-visible {
    outline: none;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(116, 26, 47, 0.28);
}

input:user-invalid {
    border-color: var(--error);
    background: #FFF1EA;
}

/* ---------- Botões ---------- */
.actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 2rem;
}

.btn {
    flex: 1 1 10rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.95rem 1.5rem;
    font: inherit;
    font-size: 1rem;
    font-weight: 700;
    text-decoration: none;
    text-align: center;
    border: 2px solid var(--bridal);
    border-radius: 999px;
    cursor: pointer;
    transition: background-color .15s ease, color .15s ease, transform .1s ease;
}

.btn-primary { background: var(--bridal); color: var(--skin); }
.btn-primary:hover { background: var(--bridal-dark); border-color: var(--bridal-dark); }

.btn-secondary { background: transparent; color: var(--bridal); }
.btn-secondary:hover { background: var(--bridal); color: var(--skin); }

.btn:active { transform: translateY(1px); }

.btn:focus-visible {
    outline: 3px solid var(--bridal-dark);
    outline-offset: 3px;
}

/* ---------- Desktop: lado a lado ---------- */
@media (min-width: 860px) {
    .page { grid-template-columns: 1fr 1fr; }
    .intro, .panel { padding: 4rem 3rem; }
    .intro-inner { margin-right: 0; }
    .form-wrap { margin-left: 0; }
}

@media (prefers-reduced-motion: reduce) {
    * { transition: none !important; }
}