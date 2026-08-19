# Plan: Migrarea de la MaryUI la BlatUI

**Obiectiv**: Înlocuirea completă a pachetului `robsontenorio/mary` cu `anousss007/blatui` în proiectul auto_sender.

**Context**: Proiectul folosește stack-ul Laravel + Livewire + Flux UI + Tailwind CSS v4. MaryUI este instalat dar utilizarea este limitată la componentele Toast și câteva componente UI (button, input, select, etc.).

---

## Analiza Utilizării Curente MaryUI

### 1. Toast Notifications (9 fișiere PHP + 5 layout-uri)
Fișiere care folosesc `Mary\Traits\Toast`:
- `components/settings/⚡profile/profile.php`
- `components/settings/⚡security/security.php`
- `components/pipeline/⚡review-applications/review-applications.php`
- `components/ai-settings/⚡ai-settings/ai-settings.php`
- `components/applications/⚡applications/applications.php`
- `components/rules/⚡rules/rules.php`
- `components/cvs/⚡cvs/cvs.php`
- `components/skills/⚡skills/skills.php`
- `components/keywords/⚡keywords/keywords.php`

Layout-uri cu `<x-toast />`:
- `layouts/app/header.blade.php`
- `layouts/app/sidebar.blade.php`
- `layouts/auth/card.blade.php`
- `layouts/auth/simple.blade.php`
- `layouts/auth/split.blade.php`

### 2. Componente UI Utilizate în Blade
| Componentă MaryUI | Tag-uri folosite | Echivalent BlatUI |
|---|---|---|
| Button | `<x-button>` | `<x-ui.button>` |
| Alert | `<x-alert>` | `<x-ui.alert>` |
| Avatar | `<x-avatar>` | `<x-ui.avatar>` |
| Badge | `<x-badge>` | `<x-ui.badge>` |
| Checkbox | `<x-checkbox>` | `<x-ui.checkbox>` |
| Drawer | `<x-drawer>` | `<x-ui.drawer>` |
| Dropdown | `<x-dropdown>` | `<x-ui.dropdown-menu>` |
| Icon | `<x-icon>` | `<x-ui.icon>` (sau blade-lucide-icons) |
| Input | `<x-input>` | `<x-ui.input>` |
| Main | `<x-main>` | `<x-ui.container>` |
| Menu | `<x-menu>` | `<x-ui.menu>` |
| MenuItem | `<x-menu-item>` | `<x-ui.menu-item>` |
| MenuSeparator | `<x-menu-separator>` | `<x-ui.menu-separator>` |
| Modal | `<x-modal>` | `<x-ui.dialog>` |
| Nav | `<x-nav>` | `<x-ui.navigation-menu>` |
| Password | `<x-password>` | `<x-ui.input type="password">` |
| Pin | `<x-pin>` | `<x-ui.input-otp>` |
| Select | `<x-select>` | `<x-ui.select>` |
| Textarea | `<x-textarea>` | `<x-ui.textarea>` |
| ThemeToggle | `<x-theme-toggle>` | Custom (BlatUI nu are echivalent direct) |
| Toast | `<x-toast>` | `<x-ui.sonner>` |

---

## Milestones

### Milestone 1: Pregătirea Mediului
**Efort: Mic (30 min)**

- [ ] Verifică compatibilitatea Tailwind CSS v4 (BlatUI necesită v4)
- [ ] Verifică că Node.js 18+ este instalat
- [ ] Fă backup la fișierele curente importante

### Milestone 2: Instalarea BlatUI
**Efort: Mic (30 min)**

- [ ] Rulează `composer require anousss007/blatui`
- [ ] Instalează dependențele peer:
  ```bash
  composer require gehrisandro/tailwind-merge-laravel mallardduck/blade-lucide-icons
  npm install -D alpinejs @alpinejs/anchor @floating-ui/dom @alpinejs/collapse @alpinejs/focus
  ```
- [ ] Publică foundation-urile:
  ```bash
  php artisan vendor:publish --tag=blatui-foundations
  ```
- [ ] Configurează `resources/css/app.css`:
  ```css
  @import "./blatui.css";
  ```
- [ ] Configurează `resources/js/app.js` (pentru Livewire, folosește `registerBlatUI`)
- [ ] Rulează `php artisan blatui:init` pentru a verifica setup-ul

### Milestone 3: Instalarea Componentelor BlatUI
**Efort: Mic (20 min)**

- [ ] Instalează componentele necesare:
  ```bash
  php artisan blatui:add button alert badge checkbox drawer dropdown-menu input modal select textarea sonner avatar icon navigation-menu menu
  ```
- [ ] Verifică lista completă cu `php artisan blatui:list`

### Milestone 4: Migrarea Toast Notifications
**Efort: Mediu (1-2 ore)**

**4.1. Înlocuirea `<x-toast />` în layout-uri**
- [ ] `layouts/app/header.blade.php`: `<x-toast />` → `<x-ui.sonner />`
- [ ] `layouts/app/sidebar.blade.php`: `<x-toast />` → `<x-ui.sonner />`
- [ ] `layouts/auth/card.blade.php`: `<x-toast />` → `<x-ui.sonner />`
- [ ] `layouts/auth/simple.blade.php`: `<x-toast />` → `<x-ui.sonner />`
- [ ] `layouts/auth/split.blade.php`: `<x-toast />` → `<x-ui.sonner />`

**4.2. Înlocuirea `Mary\Traits\Toast` în PHP**
Pentru fiecare fișier PHP:
- Înlocuiește `use Mary\Traits\Toast;` cu `use Mary\Traits\Toast;` (BlatUI nu are trait Toast - vezi nota mai jos)
- Dacă folosești `toast()->success()` etc., trebuie migrat la `toast()` din `andusd/blatui-toast` sau implementare custom

**⚠️ Notă Importantă**: BlatUI nu are un Toast trait echivalent. Opțiuni:
1. Folosește `andusd/blatui-toast` (dacă există) sau `flynsarmy/laravel-toaster`
2. Implementează un toast custom folosind Alpine.js
3. Folosește sesiunile Laravel flash + Alpine.js pentru notificări

### Milestone 5: Migrarea Componentelor UI
**Efort: Mare (4-6 ore)**

**5.1. Button** - Toate `<x-button>` → `<x-ui.button>`
- [ ] `settings/⚡profile/profile.blade.php`
- [ ] `settings/⚡security/security.blade.php`
- [ ] `settings/⚡delete-user-form/delete-user-form.blade.php`
- [ ] `settings/two-factor/⚡recovery-codes/recovery-codes.blade.php`
- [ ] `pipeline/⚡review-applications/review-applications.blade.php`
- [ ] `pipeline/⚡analyze-jobs/analyze-jobs.blade.php`
- [ ] `pipeline/⚡send-applications/send-applications.blade.php`
- [ ] `pipeline/⚡generate-messages/generate-messages.blade.php`
- [ ] Alte fișiere care folosesc `<x-button>`

**5.2. Form Components**
- [ ] `<x-input>` → `<x-ui.input>` în toate formele
- [ ] `<x-select>` → `<x-ui.select>`
- [ ] `<x-checkbox>` → `<x-ui.checkbox>`
- [ ] `<x-textarea>` → `<x-ui.textarea>`
- [ ] `<x-password>` → `<x-ui.input type="password">`
- [ ] `<x-pin>` → `<x-ui.input-otp>`

**5.3. Layout & Navigation**
- [ ] `<x-modal>` → `<x-ui.dialog>`
- [ ] `<x-dropdown>` → `<x-ui.dropdown-menu>`
- [ ] `<x-drawer>` → `<x-ui.drawer>`
- [ ] `<x-menu>` → `<x-ui.menu>`
- [ ] `<x-menu-item>` → `<x-ui.menu-item>`
- [ ] `<x-menu-separator>` → `<x-ui.menu-separator>`
- [ ] `<x-nav>` → `<x-ui.navigation-menu>`

**5.4. Display Components**
- [ ] `<x-alert>` → `<x-ui.alert>`
- [ ] `<x-avatar>` → `<x-ui.avatar>`
- [ ] `<x-badge>` → `<x-ui.badge>`

**5.5. Custom Components**
- [ ] `<x-theme-toggle>` - Implementare customă (BlatUI nu are echivalent)
- [ ] `<x-icon>` → Verifică dacă blade-lucide-icons oferă aceeași interfață

### Milestone 6: Actualizarea CSS/Tailwind
**Efort: Mic (30 min)**

- [ ] Actualizează clasele Tailwind specifice MaryUI la cele BlatUI
- [ ] Verifică variabilele CSS (MaryUI vs BlatUI tokens)
- [ ] Testează dark mode

### Milestone 7: Testing
**Efort: Mediu (1-2 ore)**

- [ ] Rulează toate testele: `php artisan test --compact`
- [ ] Verifică manual fiecare pagină:
  - [ ] Login/Register/Forgot Password
  - [ ] Dashboard
  - [ ] Settings (Profile, Security, Appearance)
  - [ ] Pipeline (Fetch Jobs, Analyze, Generate, Review, Send)
  - [ ] Applications, Rules, Skills, Keywords, CVs, AI Settings
- [ ] Verifică toast notifications funcționează
- [ ] Verifică modals, dropdowns, drawers funcționează
- [ ] Verifică formele (submit, validation, error messages)
- [ ] Verifică dark mode pe toate paginile

### Milestone 8: Curățare Finală
**Efort: Mic (30 min)**

- [ ] Dezinstalează MaryUI: `composer remove robsontenorio/mary`
- [ ] Rulează `composer dump-autoload`
- [ ] Verifică că nu mai există referințe la MaryUI în cod
- [ ] Rulează `vendor/bin/pint --dirty --format agent`
- [ ] Rulează testele finale
- [ ] Actualizează documentația proiectului dacă este necesar

---

## Riscuri și Dependințe

### Risc 1: Toast Notifications
**Impact: Mare**
- BlatUI nu are un Toast trait echivalent cu MaryUI
- *Mitigare*: Folosește `flynsarmy/laravel-toaster` sau implementează un wrapper custom

### Risc 2: Compatibilitate API
**Impact: Mediu**
- API-ul componentelor este diferit (ex: `<x-button variant="primary">` vs `<x-ui.button class="...">`)
- *Mitigare*: Mapping atent al proprietăților, teste manuale extensive

### Risc 3: Dark Mode
**Impact: Mic**
- MaryUI și BlatUI folosesc mecanisme diferite pentru dark mode
- *Mitigare`: Verifică `registerBlatUI(Alpine, { darkMode: false })` dacă Flux UI gestionează deja dark mode

### Risc 4: Flux UI Conflict
**Impact: Mediu**
- Proiectul folosește și Flux UI - asigură-te că nu sunt conflicte
- *Mitigare*: Verifică compatibilitatea, testează isolated

---

## Timp Estimat Total
- **Optimist**: 8-10 ore
- **Realist**: 12-16 ore
- **Pesimist**: 20+ ore (dacă apar conflicte cu Flux UI)

---

## Ordine Recomandată de Implementare
1. Instalare BlatUI (fără a elimina MaryUI)
2. Migrarea Toast (cea mai simplă, independentă)
3. Migrarea componentelor de formular (input, select, checkbox, etc.)
4. Migrarea componentelor de layout (modal, drawer, dropdown)
5. Migrarea componentelor de display (alert, badge, avatar)
6. Testing complet
7. Eliminarea MaryUI

**⚠️ Important**: Nu elimina MaryUI până când toate componentele nu sunt migrate și testate!
