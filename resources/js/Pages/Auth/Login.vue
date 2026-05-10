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
        <!-- Panneau gauche : Vidéo immersive -->
        <div class="login-panel-left" aria-hidden="true">
            <video autoplay muted loop playsinline class="panel-video">
                <source src="/videos/login-bg.mp4" type="video/mp4">
            </video>

            <!-- Overlay ajusté pour plus de visibilité vidéo -->
            <div class="video-overlay"></div>

            <div class="panel-content">
                <div class="brand-mark">
                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Cercle de fond avec dégradé orangé -->
                        <rect width="48" height="48" rx="14" fill="url(#paint0_linear_logo_login)" />
                        
                        <!-- Icône Headset (Casque Call Center) -->
                        <path d="M24 11C18.4772 11 14 15.4772 14 21V28C14 29.6569 15.3431 31 17 31H19V21H16C16 16.5817 19.5817 13 24 13C28.4183 13 32 16.5817 32 21H29V31H31C32.6569 31 34 29.6569 34 28V21C34 15.4772 29.5228 11 24 11Z" fill="white"/>
                        <path d="M31 31H34V34C34 35.6569 32.6569 37 31 37H28V35C28 32.7909 29.3431 31 31 31Z" fill="white" fill-opacity="0.8"/>
                        <path d="M24 33V37" stroke="white" stroke-width="2" stroke-linecap="round"/>
                        
                        <defs>
                            <linearGradient id="paint0_linear_logo_login" x1="0" y1="0" x2="48" y2="48" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#FB923C"/> <!-- orange-400 -->
                                <stop offset="1" stop-color="#F97316"/> <!-- orange-500 -->
                            </linearGradient>
                        </defs>
                    </svg>
                    <span class="brand-name">WipLite</span>
                </div>

                <div class="panel-tagline">
                    <h1>Gestion RH<br /><em>Réinventée.</em></h1>
                    <p>Une expérience fluide, moderne et performante pour piloter vos équipes au quotidien.</p>
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
                        <span class="stat-label">Cloud</span>
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
                                    <rect x="2" y="4" width="20" height="16" rx="3" />
                                    <path d="m2 7 10 7 10-7" />
                                </svg>
                            </span>
                            <input id="email" type="email" v-model="form.email" required autofocus autocomplete="username" placeholder="vous@example.com" class="field-input" />
                        </div>
                        <InputError :message="form.errors.email" class="field-error-msg" />
                    </div>

                    <div class="field-group">
                        <label for="password" class="field-label">Mot de passe</label>
                        <div class="field-wrapper" :class="{ 'has-error': form.errors.password }">
                            <span class="field-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <rect x="3" y="11" width="18" height="11" rx="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                            </span>
                            <input id="password" type="password" v-model="form.password" required autocomplete="current-password" placeholder="••••••••" class="field-input" />
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

                    <button type="submit" class="submit-btn" :disabled="form.processing">
                        <span v-if="!form.processing">Se connecter</span>
                        <span v-else class="btn-loader">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="spin">
                                <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                            </svg>
                            Connexion…
                        </span>
                    </button>
                </form>

                <p class="form-footer">
                    Accès réservé au personnel autorisé.<br />
                    Contactez votre administrateur pour obtenir un accès.
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap');

*, *::before, *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

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
    overflow: hidden;
    background: #000; /* Fond noir pour le contraste vidéo */
}

.panel-video {
    position: absolute;
    top: 50%;
    left: 50%;
    min-width: 100%;
    min-height: 100%;
    width: auto;
    height: auto;
    transform: translate(-50%, -50%);
    object-fit: cover;
    z-index: 1;
}

.video-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    /* Opacité réduite (0.35 et 0.5) pour voir davantage la vidéo */
    background: linear-gradient(135deg, 
        rgba(37, 99, 235, 0.35) 0%, 
        rgba(13, 17, 23, 0.5) 100%);
    backdrop-filter: blur(2px); /* Flou réduit pour plus de clarté */
    z-index: 2;
}

.panel-content {
    position: relative;
    z-index: 10;
    height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 4rem;
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
    color: white;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3); /* Ajout d'ombre pour lisibilité */
}

.panel-tagline h1 {
    font-family: 'Sora', sans-serif;
    font-size: 3.5rem;
    font-weight: 800;
    line-height: 1.1;
    color: white;
    letter-spacing: -0.04em;
    margin-bottom: 1.5rem;
    text-shadow: 0 4px 12px rgba(0,0,0,0.4);
}

.panel-tagline h1 em {
    font-style: normal;
    color: #60a5fa;
}

.panel-tagline p {
    font-size: 1.15rem;
    color: rgba(255, 255, 255, 0.9);
    line-height: 1.6;
    max-width: 440px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.panel-stats {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    backdrop-filter: blur(12px);
}

.stat-num {
    font-family: 'Sora', sans-serif;
    font-size: 1.4rem;
    font-weight: 700;
    color: white;
}

.stat-label {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.7);
    text-transform: uppercase;
}

.stat-divider {
    width: 1px;
    height: 36px;
    background: rgba(255, 255, 255, 0.3);
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
.form-eyebrow { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: #2563eb; margin-bottom: 0.5rem; }
.form-title { font-family: 'Sora', sans-serif; font-size: 2rem; font-weight: 700; color: #0d1117; }
.form-subtitle { font-size: 0.95rem; color: #64748b; }

.login-form { display: flex; flex-direction: column; gap: 1.25rem; }
.field-group { display: flex; flex-direction: column; gap: 6px; }
.field-label { font-size: 0.85rem; font-weight: 500; color: #1e2533; }
.field-wrapper { display: flex; align-items: center; border: 1.5px solid #e2e8f0; border-radius: 10px; background: #f8fafc; transition: 0.2s; }
.field-wrapper:focus-within { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15); background: #ffffff; }
.field-input { flex: 1; border: none; background: transparent; padding: 0.75rem 0.75rem 0.75rem 0; outline: none; }

.submit-btn {
    margin-top: 0.5rem;
    width: 100%;
    padding: 0.875rem;
    background: #2563eb;
    color: white;
    font-weight: 600;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
}

.spin { animation: spin 0.9s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

@media (max-width: 1023px) {
    .login-panel-left { display: none; }
    .login-panel-right { background: #f8fafc; }
}
</style>