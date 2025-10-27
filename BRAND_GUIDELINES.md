# 🎨 POS System - Brand Guidelines

**Version:** 2.0
**Last Updated:** 2025-10-27
**Status:** Complete Visual Identity System

---

## 📋 Table of Contents

1. [Brand Overview](#brand-overview)
2. [Logo Usage](#logo-usage)
3. [Color Palette](#color-palette)
4. [Typography](#typography)
5. [Icon System](#icon-system)
6. [UI Components](#ui-components)
7. [Photography & Imagery](#photography--imagery)
8. [Voice & Tone](#voice--tone)
9. [Usage Guidelines](#usage-guidelines)

---

## 🎯 Brand Overview

### Mission Statement
**POS System** provides modern, efficient point-of-sale solutions that empower retailers to manage their businesses with confidence and ease.

### Brand Values
- **Reliability** - Always dependable, never lets you down
- **Simplicity** - Easy to use, intuitive interface
- **Professionalism** - Enterprise-grade quality
- **Innovation** - Modern technology, future-ready
- **Trust** - Secure, accurate, transparent

### Brand Personality
- Professional yet approachable
- Modern but not trendy
- Confident but not arrogant
- Efficient but not cold
- Innovative but not complicated

---

## 🎨 Logo Usage

### Primary Logo

**File:** `resources/img/logo.svg`

The primary logo consists of a shopping cart icon combined with the "POS System" wordmark.

**Specifications:**
- Width: 120px
- Height: 40px
- Format: SVG (scalable)
- Primary Color: #1e40af (Blue)

**Usage:**
```html
<!-- Standard usage -->
<img src="/img/logo.svg" alt="POS System" height="40">
```

### Logo Variations

#### 1. **Primary Logo** (Light backgrounds)
- **File:** `resources/img/logo.svg`
- **Use on:** White, light gray, light colored backgrounds
- **Colors:** Blue cart icon + black text

#### 2. **Dark Mode Logo** (Dark backgrounds)
- **File:** `resources/img/logo-dark.svg`
- **Use on:** Dark backgrounds, dark mode interface
- **Colors:** Light blue cart icon + white text

#### 3. **Small/Icon Logo** (Collapsed sidebar, mobile)
- **File:** `resources/img/logo-small.svg`
- **Use on:** Mobile, favicon, small spaces
- **Size:** 40x40px
- **Format:** Icon only, no text

#### 4. **Favicon**
- **File:** `resources/img/favicon.svg`
- **Size:** 32x32px
- **Use:** Browser tabs, bookmarks, app icons

### Logo Clear Space

Maintain a minimum clear space around the logo equal to the height of the shopping cart icon (approximately 24px).

```
┌────────────────────────────────┐
│         Clear Space            │
│    ┌──────────────────┐       │
│    │   POS System     │       │
│    └──────────────────┘       │
│         Clear Space            │
└────────────────────────────────┘
```

### Logo Don'ts

❌ **Don't:**
- Rotate the logo
- Change logo colors
- Stretch or distort
- Add effects (shadows, gradients not in original)
- Place on busy backgrounds
- Use low-resolution versions
- Recreate the logo

✅ **Do:**
- Use official logo files
- Maintain aspect ratio
- Use appropriate version for background
- Ensure sufficient contrast
- Keep adequate clear space

---

## 🎨 Color Palette

### Primary Colors

#### Brand Blue (Primary)
```css
/* Main brand color */
--brand-primary: #1e40af;        /* Blue-800 */
--brand-primary-hover: #1e3a8a;   /* Blue-900 */
--brand-primary-light: #3b82f6;   /* Blue-500 */
```

**Usage:**
- Primary buttons
- Links
- Key UI elements
- Focus states
- Active states

**Accessibility:** AAA rating on white backgrounds

#### Secondary Gray
```css
--brand-secondary: #64748b;  /* Slate-500 */
```

**Usage:**
- Secondary text
- Icons
- Borders
- Disabled states

### UI Colors (Light Mode)

#### Backgrounds
```css
--bg-primary: #ffffff;      /* Pure white */
--bg-secondary: #f8fafc;    /* Slate-50 */
--bg-tertiary: #f1f5f9;     /* Slate-100 */
--bg-hover: #f8fafc;        /* Hover state */
```

#### Text
```css
--text-primary: #1e293b;    /* Slate-800 - Main text */
--text-secondary: #64748b;  /* Slate-500 - Secondary text */
--text-tertiary: #94a3b8;   /* Slate-400 - Muted text */
```

#### Borders
```css
--border-color: #e2e8f0;    /* Slate-200 */
--border-hover: #cbd5e1;    /* Slate-300 */
```

### Status Colors

#### Success (Green)
```css
--success: #059669;          /* Emerald-600 */
--success-bg: #d1fae5;       /* Emerald-100 */
```
**Usage:** Success messages, completed states, positive indicators

#### Error (Red)
```css
--error: #dc2626;            /* Red-600 */
--error-bg: #fee2e2;         /* Red-100 */
```
**Usage:** Error messages, destructive actions, alerts

#### Warning (Orange)
```css
--warning: #d97706;          /* Amber-600 */
--warning-bg: #fed7aa;       /* Amber-200 */
```
**Usage:** Warnings, caution messages, pending states

#### Info (Cyan)
```css
--info: #0284c7;             /* Sky-600 */
--info-bg: #e0f2fe;          /* Sky-100 */
```
**Usage:** Informational messages, tips, help text

### Dark Mode Colors

#### Backgrounds (Dark)
```css
--bg-primary: #0f172a;       /* Slate-900 */
--bg-secondary: #1e293b;     /* Slate-800 */
--bg-tertiary: #334155;      /* Slate-700 */
```

#### Text (Dark)
```css
--text-primary: #f8fafc;     /* Slate-50 */
--text-secondary: #cbd5e1;   /* Slate-300 */
--text-tertiary: #94a3b8;    /* Slate-400 */
```

### Color Usage Matrix

| Element | Light Mode | Dark Mode |
|---------|------------|-----------|
| Primary Button | #1e40af (Blue) | #3b82f6 (Light Blue) |
| Text | #1e293b (Dark Gray) | #f8fafc (White) |
| Background | #ffffff (White) | #0f172a (Dark) |
| Border | #e2e8f0 (Light Gray) | #334155 (Medium Gray) |
| Success | #059669 (Green) | #10b981 (Light Green) |
| Error | #dc2626 (Red) | #ef4444 (Light Red) |

---

## ✍️ Typography

### Font Family

**Primary Font:** Inter

```css
font-family: 'Inter', system-ui, -apple-system, sans-serif;
```

**Source:** Google Fonts
**License:** Open Font License
**Link:** https://fonts.google.com/specimen/Inter

**Why Inter:**
- Excellent readability at all sizes
- Professional appearance
- Wide language support
- Optimized for screens
- Open source and free

### Font Weights

```css
--font-light: 300;       /* Use sparingly */
--font-regular: 400;     /* Body text */
--font-medium: 500;      /* Subheadings, labels */
--font-semibold: 600;    /* Buttons, emphasis */
--font-bold: 700;        /* Headings */
--font-extrabold: 800;   /* Hero text, metrics */
--font-black: 900;       /* Display only */
```

### Type Scale

```css
/* Headings */
h1: 2.5rem (40px) - font-weight: 800
h2: 2rem (32px) - font-weight: 700
h3: 1.5rem (24px) - font-weight: 700
h4: 1.25rem (20px) - font-weight: 600
h5: 1.125rem (18px) - font-weight: 600
h6: 1rem (16px) - font-weight: 600

/* Body */
body: 1rem (16px) - font-weight: 400
small: 0.875rem (14px) - font-weight: 400
tiny: 0.75rem (12px) - font-weight: 500
```

### Line Height

```css
--line-tight: 1.2;       /* Headings */
--line-normal: 1.5;      /* Body text */
--line-relaxed: 1.6;     /* Long-form content */
--line-loose: 2;         /* Captions, metadata */
```

### Letter Spacing

```css
--tracking-tight: -0.02em;   /* Large headings */
--tracking-normal: 0;        /* Body text */
--tracking-wide: 0.05em;     /* Uppercase labels */
```

### Usage Examples

```html
<!-- Page Title -->
<h1 style="font-size: 2.5rem; font-weight: 800; color: #1e293b;">
    Dashboard
</h1>

<!-- Section Heading -->
<h2 style="font-size: 1.5rem; font-weight: 700; color: #1e40af;">
    Recent Sales
</h2>

<!-- Body Text -->
<p style="font-size: 1rem; font-weight: 400; color: #64748b; line-height: 1.6;">
    Your sales performance has improved by 15% this month.
</p>

<!-- Button Text -->
<button style="font-size: 1rem; font-weight: 600;">
    Create Sale
</button>

<!-- Label/Caption -->
<span style="font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8;">
    Total Sales
</span>
```

---

## 🔷 Icon System

### Icon Style

**Style:** Outline/Line icons
**Weight:** 2px stroke
**Rounding:** Rounded caps and joins
**Size:** 16px, 20px, 24px

**Recommended Icon Library:**
- Heroicons (https://heroicons.com/) - Matches our style perfectly
- Feather Icons (https://feathericons.com/) - Alternative option

### Icon Usage

```html
<!-- Small (16px) - Use in tight spaces, labels -->
<svg class="w-4 h-4" stroke-width="2">...</svg>

<!-- Medium (20px) - Default size, buttons, nav -->
<svg class="w-5 h-5" stroke-width="2">...</svg>

<!-- Large (24px) - Headings, empty states -->
<svg class="w-6 h-6" stroke-width="2">...</svg>
```

### Icon Colors

- **Primary:** #1e40af (Brand blue)
- **Secondary:** #64748b (Gray)
- **Success:** #059669 (Green)
- **Error:** #dc2626 (Red)
- **Warning:** #d97706 (Orange)

### Shopping Cart Icon (Brand Icon)

Our primary brand icon is a shopping cart, representing retail and commerce.

**Specifications:**
- Style: Outlined, 2px stroke
- Colors: Brand blue (#1e40af)
- Rounding: Rounded corners
- Features: Two wheels, rectangular body

**Usage:**
- Logo
- Loading states
- Empty states
- POS interface
- Marketing materials

---

## 🧩 UI Components

### Buttons

#### Primary Button
```css
background: #1e40af;
color: white;
padding: 0.75rem 2rem;
border-radius: 0.5rem;
font-weight: 600;
transition: all 0.15s ease;
```

**States:**
- Hover: `background: #1e3a8a` + `transform: translateY(-1px)`
- Active: `background: #1e40af` + `transform: translateY(0)`
- Disabled: `opacity: 0.5` + `cursor: not-allowed`

#### Secondary Button
```css
background: #f1f5f9;
color: #1e293b;
border: 1px solid #e2e8f0;
```

### Cards

```css
background: white;
border: 1px solid #e2e8f0;
border-radius: 0.75rem;
box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
padding: 1.5rem;
transition: all 0.25s ease;
```

**Hover:**
```css
transform: translateY(-2px);
box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
```

### Badges

```css
display: inline-flex;
padding: 0.25rem 0.75rem;
border-radius: 9999px;
font-size: 0.75rem;
font-weight: 600;
text-transform: uppercase;
letter-spacing: 0.05em;
```

### Modals

```css
background: white;
border-radius: 1rem;
box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
max-width: 600px;
```

---

## 📸 Photography & Imagery

### Image Style

**Preferred:**
- Professional product photography
- Clean, minimal backgrounds
- Good lighting
- High contrast
- Sharp focus

**Avoid:**
- Busy backgrounds
- Low resolution
- Poor lighting
- Cluttered compositions

### Illustrations

Use the custom illustrations provided:
- **Empty State:** `resources/img/empty-state.svg`
- **404 Error:** `resources/img/error-404.svg`
- **403 Error:** `resources/img/error-403.svg`
- **Loading:** `resources/img/loading-spinner.svg`

**Style Characteristics:**
- Minimalist
- Line-based
- Brand colors
- Rounded corners
- Friendly but professional

---

## 💬 Voice & Tone

### Brand Voice

**Professional:** Clear, accurate, reliable
**Friendly:** Approachable, helpful, human
**Confident:** Assured, knowledgeable, trustworthy
**Concise:** Direct, efficient, no fluff

### Writing Guidelines

#### Do:
✅ Use clear, simple language
✅ Be direct and concise
✅ Use active voice
✅ Be helpful and supportive
✅ Acknowledge user actions

#### Don't:
❌ Use jargon or technical terms (unless necessary)
❌ Be overly casual or use slang
❌ Use passive voice
❌ Be vague or ambiguous
❌ Blame the user for errors

### Example Messages

**Success:**
```
✓ Sale completed successfully!
✓ Product added to inventory
✓ Customer profile updated
```

**Error:**
```
✗ Unable to process payment. Please check the card details and try again.
✗ This product is out of stock. Would you like to add more inventory?
```

**Info:**
```
ℹ This action cannot be undone.
ℹ Tip: Use keyboard shortcut Cmd+S to save quickly
```

---

## 📐 Usage Guidelines

### Spacing System

```css
--spacing-xs: 0.25rem;   /* 4px */
--spacing-sm: 0.5rem;    /* 8px */
--spacing-md: 1rem;      /* 16px */
--spacing-lg: 1.5rem;    /* 24px */
--spacing-xl: 2rem;      /* 32px */
--spacing-2xl: 3rem;     /* 48px */
```

### Border Radius

```css
--radius-sm: 0.375rem;   /* 6px - Small elements */
--radius-md: 0.5rem;     /* 8px - Default */
--radius-lg: 0.75rem;    /* 12px - Cards */
--radius-xl: 1rem;       /* 16px - Modals */
--radius-full: 9999px;   /* Pills, badges */
```

### Shadows

```css
--shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
--shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
--shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
--shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
```

### Transitions

```css
--transition-fast: 150ms ease;
--transition-normal: 250ms ease;
--transition-slow: 350ms ease;
```

### Responsive Breakpoints

```css
/* Mobile */
@media (max-width: 639px) { }

/* Tablet */
@media (min-width: 640px) { }

/* Desktop */
@media (min-width: 1024px) { }

/* Large Desktop */
@media (min-width: 1280px) { }
```

---

## ✅ Brand Checklist

Before releasing any branded material, ensure:

- [ ] Logo is used correctly (right version for background)
- [ ] Colors match brand palette exactly
- [ ] Typography uses Inter font
- [ ] Spacing is consistent with system
- [ ] Icons match style guidelines
- [ ] Tone of voice is appropriate
- [ ] All text is proofread
- [ ] Images are high quality
- [ ] Responsive design works on all devices
- [ ] Accessibility standards met (WCAG AA minimum)

---

## 📞 Questions & Support

For questions about brand usage or to request custom assets:

**Email:** brand@possystem.com
**Documentation:** This file
**Design Files:** `resources/img/` directory

---

**Brand Guidelines Version:** 2.0
**Last Updated:** 2025-10-27
**Maintained By:** Design Team
**Status:** ✅ Active & Complete
