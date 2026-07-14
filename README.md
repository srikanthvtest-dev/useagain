# UseAgain — Second-Hand Marketplace for Hyderabad Families

UseAgain is a sustainability-first marketplace where families in Hyderabad can buy affordable second-hand products and donate usable items to orphanages — free of cost.

---

## 🚀 Getting Started

### Prerequisites
- Node.js 18+
- npm or yarn

### Installation

npm install
npm run dev

Open [http://localhost:3000](http://localhost:3000) in your browser.

---

## 📁 Project Structure

useagain/
├── data/
│   └── products.json              ← All product data (edit this to add products)
├── public/
│   └── assets/
│       └── images/
│           └── products/
│               ├── toys/          ← Toy product images
│               ├── bicycles/      ← Bicycle images
│               ├── books/         ← Book images
│               ├── baby-products/ ← Baby product images
│               ├── sports/        ← Sports equipment images
│               └── furniture/     ← Furniture images
├── src/
│   ├── app/
│   │   ├── page.tsx               ← Homepage
│   │   ├── layout.tsx             ← Root layout
│   │   ├── sitemap.ts
│   │   ├── robots.ts
│   │   ├── components/            ← Homepage section components
│   │   ├── products/
│   │   │   └── page.tsx           ← Products listing page
│   │   ├── product-details/
│   │   │   └── page.tsx           ← Product detail page
│   │   ├── about/
│   │   │   └── page.tsx
│   │   ├── contact/
│   │   │   └── page.tsx
│   │   └── faq/
│   │       └── page.tsx
│   ├── components/
│   │   ├── Header.tsx
│   │   ├── Footer.tsx
│   │   └── ProductCard.tsx
│   └── styles/
│       └── tailwind.css
├── tailwind.config.js
└── README.md

---

## ➕ How to Add New Products (Non-Technical Guide)

### Step 1: Add your product image

Copy your product image into the correct category folder:

| Category | Folder |
|---|---|
| Toys | `public/assets/images/products/toys/` |
| Bicycles | `public/assets/images/products/bicycles/` |
| Books | `public/assets/images/products/books/` |
| Baby Products | `public/assets/images/products/baby-products/` |
| Sports | `public/assets/images/products/sports/` |
| Furniture | `public/assets/images/products/furniture/` |

**Example:** If you're adding a toy, copy `my-toy-photo.jpg` into `public/assets/images/products/toys/my-toy-photo.jpg`

**Image tips:**
- Use JPG or PNG format
- Recommended size: 800×600 pixels or larger
- Keep file size under 500KB for fast loading
- Use descriptive filenames (e.g., `red-bicycle-hero-26inch.jpg`)

---

### Step 2: Add the product entry to products.json

Open `data/products.json` and add a new entry at the end of the array (before the closing `]`):

{
  "id": "21",
  "title": "Your Product Title Here",
  "category": "toys",
  "condition": "Good",
  "description": "Detailed description of the product. Include key features, any defects, and why it's useful.",
  "images": ["/assets/images/products/toys/my-toy-photo.jpg"],
  "seller": "Seller Name",
  "location": "Banjara Hills",
  "city": "Hyderabad",
  "listedDate": "2026-07-13",
  "featured": false,
  "forDonation": false
}

**Field Reference:**

| Field | Description | Options |
|---|---|---|
| `id` | Unique ID (increment from last) | Any unique number as string |
| `title` | Product name | Any text |
| `category` | Product category | `toys`, `bicycles`, `books`, `baby-products`, `sports`, `furniture` |
| `condition` | Item condition | `Like New`, `Very Good`, `Good`, `Fair` |
| `description` | Full description | Any text (2-4 sentences recommended) |
| `images` | Array of image paths | Paths starting with `/assets/images/products/...` |
| `seller` | Seller's name | Any name |
| `location` | Area in Hyderabad | e.g., `Banjara Hills`, `Gachibowli` |
| `city` | City | `Hyderabad` |
| `listedDate` | Date listed | Format: `YYYY-MM-DD` |
| `featured` | Show on homepage | `true` or `false` |
| `forDonation` | Free donation item | `true` or `false` |

---

### Step 3: Save and verify

Save `products.json`. If the development server is running (`npm run dev`), refresh the browser — your product will appear automatically.

To make a product appear on the homepage Featured section, set `"featured": true`.

---

## 🎨 Design System

| Token | Value | Usage |
|---|---|---|
| `primary` | #2D6A4F | Forest green — CTAs, links, badges |
| `secondary` | #52B788 | Medium green — accents, highlights |
| `accent` | #1B4F72 | Trust blue — secondary CTAs |
| `background` | #F8FAF9 | Page background |
| `foreground` | #1A2E22 | Dark text, dark sections |
| `muted` | #E8F5EE | Light green tint — card backgrounds |

---

## 📞 Contact

- **WhatsApp:** [9492060241](https://wa.me/919492060241)
- **Email:** srikanth.v@useagain.in
- **Location:** Hyderabad, Telangana

---

## 📋 CHANGELOG

### v1.0.0 — 2026-07-13

**Initial Release**

**New Files:**
- `data/products.json` — 20 sample products from Hyderabad sellers
- `src/app/page.tsx` — Homepage with Hero, Mission, Categories, Featured Products, How It Works, Testimonials, CTA
- `src/app/layout.tsx` — Root layout with DM Serif Display + Plus Jakarta Sans fonts
- `src/app/components/HeroSection.tsx` — Cinematic full-bleed hero with line-reveal animations
- `src/app/components/MissionSection.tsx` — Impact stats + mission narrative
- `src/app/components/CategoriesSection.tsx` — Asymmetric category bento grid
- `src/app/components/FeaturedProductsSection.tsx` — Featured product cards
- `src/app/components/HowItWorksSection.tsx` — 3-step process cards
- `src/app/components/TestimonialsSection.tsx` — Community testimonials
- `src/app/components/DonateCTASection.tsx` — WhatsApp donation CTA banner
- `src/app/products/page.tsx` — Products listing with search, filter, sort, pagination
- `src/app/product-details/page.tsx` — Product detail with gallery, info, WhatsApp enquiry
- `src/app/about/page.tsx` — About page
- `src/app/contact/page.tsx` — Contact page with form
- `src/app/faq/page.tsx` — FAQ accordion
- `src/app/sitemap.ts` — XML sitemap
- `src/app/robots.ts` — Robots.txt
- `src/components/Header.tsx` — Responsive header with mobile hamburger
- `src/components/Footer.tsx` — Minimal footer (Pattern 2)
- `src/components/ProductCard.tsx` — Reusable product card component
- `src/styles/tailwind.css` — Design tokens + utility classes
- `tailwind.config.js` — Tailwind configuration with custom tokens

**Design Decisions:**
- Green/blue/white theme reflecting sustainability and trust
- DM Serif Display for headings (premium, distinctive)
- Plus Jakarta Sans for body (clean, modern)
- WhatsApp-first contact flow (no backend required)
- "Available for Sale" shown instead of price on all products
- Scroll-reveal animations using IntersectionObserver (no GSAP dependency)
- Mobile hamburger menu with backdrop blur overlay

---

## 🌱 Contributing

To add new products, follow the "How to Add New Products" guide above.

For code contributions, please test at 320px, 375px, 768px, and 1280px breakpoints before submitting.
