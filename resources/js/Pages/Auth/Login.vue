<script setup>
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Connexion — WipLite RH" />

    <div class="login-root">
        <!-- Panneau gauche décoratif -->
        <div class="login-panel-left" aria-hidden="true">
            <div class="panel-grid"></div>
            <div class="panel-orb orb-1"></div>
            <div class="panel-orb orb-2"></div>
            <div class="panel-content">
                <div class="brand-mark">
                    <svg width="44" height="44" viewBox="0 0 44 44" fill="none">
                        <rect width="44" height="44" rx="12" fill="white" fill-opacity="0.12"/>
                        <path d="M12 14h8v8h-8zM24 14h8v8h-8zM12 24h8v8h-8zM24 24h8v4h-4v4h-4v-8z" fill="white"/>
                    </svg>
                    <span class="brand-name">WipLite</span>
                </div>
                <div class="panel-tagline">
                    <h1>Gestion RH<br/><em>intelligente.</em></h1>
                    <p>Centraliser, affecter, planifier.<br/>Tout votre personnel en un seul endroit.</p>
                </div>
                <div class="panel-stats">
                    <div class="stat">
                        <span class="stat-num">4</span>
                        <span class="stat-label">Rôles</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat">
                        <span class="stat-num">∞</span>
                        <span class="stat-label">Campagnes</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat">
                        <span class="stat-num">100%</span>
                        <span class="stat-label">Sécurisé</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panneau droit : formulaire -->
        <div class="login-panel-right">
            <div class="form-container">
                <div class="form-header">
                    <p class="form-eyebrow">Portail RH</p>
                    <h2 class="form-title">Connexion</h2>
                    <p class="form-subtitle">Accédez à votre espace de travail</p>
                </div>

                <div v-if="status" class="status-banner">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="login-form">
                    <div class="field-group">
                        <label for="email" class="field-label">Adresse email</label>
                        <div class="field-wrapper" :class="{ 'has-error': form.errors.email }">
                            <span class="field-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <rect x="2" y="4" width="20" height="16" rx="3"/>
                                    <path d="m2 7 10 7 10-7"/>
                                </svg>
                            </span>
                            <input
                                id="email"
                                type="email"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="vous@example.com"
                                class="field-input"
                            />
                        </div>
                        <InputError :message="form.errors.email" class="field-error-msg" />
                    </div>

                    <div class="field-group">
                        <label for="password" class="field-label">Mot de passe</label>
                        <div class="field-wrapper" :class="{ 'has-error': form.errors.password }">
                            <span class="field-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <rect x="3" y="11" width="18" height="11" rx="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </span>
                            <input
                                id="password"
                                type="password"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="field-input"
                            />
                        </div>
                        <InputError :message="form.errors.password" class="field-error-msg" />
                    </div>

                    <div class="form-options">
                        <label class="remember-label">
                            <input type="checkbox" v-model="form.remember" class="remember-check" />
                            <span class="remember-text">Se souvenir de moi</span>
                        </label>
                        <a v-if="canResetPassword" :href="route('password.request')" class="forgot-link">
                            Mot de passe oublié ?
                        </a>
                    </div>

                    <button
                        type="submit"
                        class="submit-btn"
                        :disabled="form.processing"
                    >
                        <span v-if="!form.processing">Se connecter</span>
                        <span v-else class="btn-loader">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="spin">
                                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                            </svg>
                            Connexion…
                        </span>
                    </button>
                </form>

                <p class="form-footer">
                    Accès réservé au personnel autorisé.<br/>
                    Contactez votre administrateur pour obtenir un accès.
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap');

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.login-root {
    display: flex;
    min-height: 100vh;
    font-family: 'DM Sans', sans-serif;
    background: #ffffff;
}

/* ── Panneau gauche ── */
.login-panel-left {
    position: relative;
    width: 45%;
    background: linear-gradient(145deg, #0f172a 0%, #1e3a5f 55%, #0c4a6e 100%);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.panel-grid {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
    background-size: 40px 40px;
}

.panel-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    pointer-events: none;
}
.orb-1 {
    width: 380px; height: 380px;
    background: radial-gradient(circle, rgba(59,130,246,0.35) 0%, transparent 70%);
    top: -80px; left: -80px;
}
.orb-2 {
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(14,165,233,0.25) 0%, transparent 70%);
    bottom: -60px; right: -40px;
}

.panel-content {
    position: relative;
    z-index: 1;
    padding: 3rem;
    display: flex;
    flex-direction: column;
    gap: 3rem;
    color: white;
}

.brand-mark {
    display: flex;
    align-items: center;
    gap: 12px;
}
.brand-name {
    font-family: 'Sora', sans-serif;
    font-size: 1.5rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    color: white;
}

.panel-tagline h1 {
    font-family: 'Sora', sans-serif;
    font-size: 2.6rem;
    font-weight: 700;
    line-height: 1.15;
    letter-spacing: -0.03em;
    color: white;
    margin-bottom: 1rem;
}
.panel-tagline h1 em {
    font-style: normal;
    color: #93c5fd;
}
.panel-tagline p {
    font-size: 1rem;
    color: rgba(255,255,255,0.6);
    line-height: 1.7;
    font-weight: 300;
}

.panel-stats {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 1.5rem;
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 16px;
    backdrop-filter: blur(10px);
}
.stat { display: flex; flex-direction: column; gap: 2px; }
.stat-num {
    font-family: 'Sora', sans-serif;
    font-size: 1.4rem;
    font-weight: 700;
    color: white;
}
.stat-label {
    font-size: 0.75rem;
    color: rgba(255,255,255,0.5);
    text-transform: uppercase;
    letter-spacing: 0.06em;
}
.stat-divider {
    width: 1px; height: 36px;
    background: rgba(255,255,255,0.15);
    flex-shrink: 0;
}

/* ── Panneau droit ── */
.login-panel-right {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    padding: 2rem;
}

.form-container {
    width: 100%;
    max-width: 420px;
}

.form-header { margin-bottom: 2.5rem; }
.form-eyebrow {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #2563eb;
    margin-bottom: 0.5rem;
}
.form-title {
    font-family: 'Sora', sans-serif;
    font-size: 2rem;
    font-weight: 700;
    color: #0d1117;
    letter-spacing: -0.03em;
    margin-bottom: 0.4rem;
}
.form-subtitle { font-size: 0.95rem; color: #64748b; }

.status-banner {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #16a34a;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.field-group { display: flex; flex-direction: column; gap: 6px; }
.field-label { font-size: 0.85rem; font-weight: 500; color: #1e2533; }

.field-wrapper {
    display: flex;
    align-items: center;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    background: #f8fafc;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.field-wrapper:focus-within {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
    background: #ffffff;
}
.field-wrapper.has-error { border-color: #dc2626; }

.field-icon {
    display: flex;
    align-items: center;
    padding: 0 12px;
    color: #94a3b8;
    flex-shrink: 0;
}
.field-wrapper:focus-within .field-icon { color: #2563eb; }

.field-input {
    flex: 1;
    border: none;
    background: transparent;
    padding: 0.75rem 0.75rem 0.75rem 0;
    font-size: 0.95rem;
    color: #0d1117;
    font-family: 'DM Sans', sans-serif;
    outline: none;
}
.field-input::placeholder { color: #94a3b8; }

.field-error-msg { font-size: 0.8rem; color: #dc2626; margin-top: 2px; }

.form-options {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: -0.25rem;
}
.remember-label {
    display: flex; align-items: center; gap: 8px; cursor: pointer;
}
.remember-check { width: 16px; height: 16px; accent-color: #2563eb; cursor: pointer; }
.remember-text { font-size: 0.875rem; color: #64748b; }
.forgot-link {
    font-size: 0.875rem;
    color: #2563eb;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.15s;
}
.forgot-link:hover { color: #3b82f6; text-decoration: underline; }

.submit-btn {
    margin-top: 0.5rem;
    width: 100%;
    padding: 0.875rem;
    background: #2563eb;
    color: white;
    font-family: 'Sora', sans-serif;
    font-size: 0.95rem;
    font-weight: 600;
    letter-spacing: 0.01em;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
    box-shadow: 0 4px 14px rgba(37,99,235,0.3);
}
.submit-btn:hover:not(:disabled) {
    background: #1d4ed8;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(37,99,235,0.4);
}
.submit-btn:active:not(:disabled) { transform: translateY(0); }
.submit-btn:disabled { opacity: 0.7; cursor: not-allowed; }

.btn-loader {
    display: flex; align-items: center;
    justify-content: center; gap: 8px;
}
.spin { animation: spin 0.9s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.form-footer {
    margin-top: 2rem;
    font-size: 0.8rem;
    color: #94a3b8;
    text-align: center;
    line-height: 1.6;
}

@media (max-width: 768px) {
    .login-panel-left { display: none; }
    .login-panel-right { background: #f8fafc; }
}
</style>
