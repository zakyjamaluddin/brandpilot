# BrandPilot AI — Official Design System & Reusable Style Guide

Sistem desain ini diekstrak langsung dari arsitektur visual **brandpilot.my.id**, mengusung tema **"Executive Luxury Tech"** dengan perpaduan warna **Burgundy (Deep Wine)**, **Rose Gold / Antique Copper**, dan aksen **Metallic Gold**.

---

## 1. Core Typography

| Role | Font Family | Google Fonts Link / CSS Font Spec | Weights & Usage |
|---|---|---|---|
| **Primary Sans** | `Plus Jakarta Sans` | `'Plus Jakarta Sans', sans-serif` | `400, 500, 600, 700, 800` (UI, Tombol, Teks isi, Tabel) |
| **Editorial Accent** | `Playfair Display` | `'Playfair Display', serif` | `italic 400, italic 700` (Penekanan kata mewah pada Headline) |

### HTML Import
```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,400;1,600;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
```

---

## 2. Color Palette & Tokens

### A. Primary Burgundy (Deep Wine)
- `burgundy-50`: `#FBF2F4`
- `burgundy-100`: `#F6E4E8`
- `burgundy-200`: `#ECCDD4`
- `burgundy-300`: `#DDA6B4`
- `burgundy-400`: `#CA768D`
- `burgundy-500`: `#B24C68`
- `burgundy-600`: `#963650`
- `burgundy-700`: `#7C2A41`
- `burgundy-800`: **`#5B0E2D`** *(Brand Primary)*
- `burgundy-900`: `#420B22`
- `burgundy-950`: `#2B0414` *(Text heading / ultra-contrast)*

### B. Accent Rose Gold & Copper
- `rosegold-50`: `#FAF6F3`
- `rosegold-100`: `#F4ECE5`
- `rosegold-200`: `#E7D6C8` *(Selection background)*
- `rosegold-300`: `#D5B9A4`
- `rosegold-400`: `#C0987E` *(Subtle border)*
- `rosegold-500`: **`#B76E79`** *(Brand Secondary Accent)*
- `rosegold-600`: `#A06E54`
- `rosegold-700`: `#855844`
- `rosegold-800`: `#6D4839`
- `rosegold-900`: `#593C31`

### C. Metallic Gold (Shimmer & Highlights)
- `gold-500`: `#D4AF37`
- `gold-600`: `#AA820A`

### D. Neutral & Surface
- **Light Theme**:
  - Background Canvas: `#FCFCFC` / `#FCF9FA`
  - Cards: `rgba(255, 255, 255, 0.90)`
  - Border: `rgba(183, 110, 121, 0.18)` atau `#E2E8F0` (`slate-200`)
- **Dark Theme (Obsidian Wine)**:
  - Background Canvas: `#0C0307`
  - Cards: `rgba(22, 8, 15, 0.72)`
  - Border: `rgba(183, 110, 121, 0.28)`

---

## 3. Konfigurasi Tailwind CSS

### Untuk Tailwind CSS v4 (`resources/css/app.css`)
```css
@import "tailwindcss";

@theme {
  --font-sans: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
  --font-serif: 'Playfair Display', Georgia, serif;

  --color-burgundy-50: #fbf2f4;
  --color-burgundy-100: #f6e4e8;
  --color-burgundy-200: #eccdd4;
  --color-burgundy-300: #dda6b4;
  --color-burgundy-400: #ca768d;
  --color-burgundy-500: #b24c68;
  --color-burgundy-600: #963650;
  --color-burgundy-700: #7c2a41;
  --color-burgundy-800: #5b0e2d;
  --color-burgundy-900: #420b22;
  --color-burgundy-950: #2b0414;

  --color-rosegold-50: #faf6f3;
  --color-rosegold-100: #f4ece5;
  --color-rosegold-200: #e7d6c8;
  --color-rosegold-300: #d5b9a4;
  --color-rosegold-400: #c0987e;
  --color-rosegold-500: #b76e79;
  --color-rosegold-600: #a06e54;
  --color-rosegold-700: #855844;
  --color-rosegold-800: #6d4839;
  --color-rosegold-900: #593c31;

  --color-gold-500: #d4af37;
  --color-gold-600: #aa820a;
}
```

### Untuk Tailwind CSS v3 (`tailwind.config.js`)
```javascript
export default {
  theme: {
    extend: {
      fontFamily: {
        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
        serif: ['"Playfair Display"', 'Georgia', 'serif'],
      },
      colors: {
        burgundy: {
          50: '#FBF2F4',
          100: '#F6E4E8',
          200: '#ECCDD4',
          300: '#DDA6B4',
          400: '#CA768D',
          500: '#B24C68',
          600: '#963650',
          700: '#7C2A41',
          800: '#5B0E2D',
          900: '#420B22',
          950: '#2B0414',
        },
        rosegold: {
          50: '#FAF6F3',
          100: '#F4ECE5',
          200: '#E7D6C8',
          300: '#D5B9A4',
          400: '#C0987E',
          500: '#B76E79',
          600: '#A06E54',
          700: '#855844',
          800: '#6D4839',
          900: '#593C31',
        },
        gold: {
          500: '#D4AF37',
          600: '#AA820A',
        }
      }
    }
  }
}
```

---

## 4. Reusable Styles & Components

```css
/* Teks Gradient Brand (Burgundy ke Rose Gold ke Gold) */
.gradient-text {
  background: linear-gradient(135deg, #5B0E2D 0%, #B76E79 50%, #D4AF37 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* Tombol CTA Mewah */
.btn-burgundy {
  background: linear-gradient(135deg, #5B0E2D 0%, #7C2A41 100%);
  box-shadow: 0 10px 25px -5px rgba(91, 14, 45, 0.35);
  border: 1px solid rgba(192, 152, 126, 0.4);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.btn-burgundy:hover {
  transform: translateY(-2px);
  box-shadow: 0 15px 30px -5px rgba(91, 14, 45, 0.50);
}
```


---

## 5. Short-Form Video Safe Zone & Motion Typography Rules (1080x1920)

Aturan baku penataan tipografi kinetik, kartu glassmorphism, dan animasi pada video vertikal (TikTok, Reels, Shorts):

```
+--------------------------------------------------------+ y = 0
|  TOP HEADER / SEARCH / TABS (Danger Zone: 0 - 240px)   |
+--------------------------------------------------------+ y = 240px
|                                                        |
|  SPEAKER FACE PROTECTION ZONE (y = 280px - 750px)      |
|  [DILARANG KERAS MENUTUPI WAJAH DENGAN TEKS / KARTU]   |
|                                                        |
+--------------------------------------------------------+ y = 750px
|                                                        |
|  ⭐⭐ GOLDEN CENTER SAFE BAND (y = 860px - 1420px) ⭐⭐   |
|  - Teks Kinetik Penekanan                              |
|  - Angka Berjalan (Rolling Numbers)                    |
|  - Kartu Glassmorphism & Animasi Garis Putus-Putus     |
|                                                        |
+--------------------------------------------------------+ y = 1480px
|  ⚠️ DANGER ZONE DASAR VIDEO (y = 1480px - 1920px) ⚠️   |
|  [DILARANG MENARUH TEKS / SUBTITLE / KARTU DI SINI]    |
|  Tertutup oleh: Caption Medsos, Username, Sound Name,  |
|  Progress Bar, dan Action Buttons (Like/Comment).      |
+--------------------------------------------------------+ y = 1920px
```

### Prinsip Utama Penataan Animasi:
1. **Aturan Dasar Bebas Elemen (*No-Bottom-Zone Rule*):**
   - Area dasar video setinggi `440px - 450px` (`bottom < 450px`) **tidak boleh** diisi subtitle, judul, maupun kartu animasi karena pasti akan tertutup oleh interface UI platform medsos.
2. **Aturan Area Wajah (*Face Protection Rule*):**
   - Area wajah pembicara (`y = 280px - 750px`) harus senantiasa bersih dan tidak tertutup kotak solid atau teks penekanan.
3. **Penempatan di Pita Tengah (*Golden Center Safe Band*):**
   - Seluruh teks kinetik, highlight marker, dan angka berjalan diletakkan di koordinat `y = 860px - 1420px` (area dada / torso bawah).
4. **Transisi ke B-Roll Sinematik:**
   - Untuk visualisasi kompleks seperti mockup smartphone dan diagram sindikasi multi-platform, video A-roll bertransisi ke rekaman **B-Roll sinematik** sehingga elemen grafis dapat tampil leluasa tanpa khawatir menutupi subjek pembicara.
5. **Estetika Glassmorphism & Garis Putus-Putus (*Marching Dashed Lines*):**
   - Gunakan panel kaca berembun (`backdrop-filter: blur(24px)`, latar transparan wine `rgba(25, 6, 17, 0.75)`, dan border rose-gold tipis `1px solid rgba(232, 180, 160, 0.35)`).
   - Gunakan garis putus-putus berpendar yang bergerak dinamis (`strokeDasharray="10 8"`, `strokeDashoffset={-(frame * 2.5)}`) sebagai aksen bingkai atau jalur laser koneksi data.
