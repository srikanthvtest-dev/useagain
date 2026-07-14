import React from 'react';
import type { Metadata, Viewport } from 'next';
import { DM_Serif_Display, Plus_Jakarta_Sans } from 'next/font/google';
import '../styles/tailwind.css';

const dmSerifDisplay = DM_Serif_Display({
  subsets: ['latin'],
  weight: ['400'],
  variable: '--font-dm-serif-display',
  display: 'swap',
});

const plusJakartaSans = Plus_Jakarta_Sans({
  subsets: ['latin'],
  weight: ['400', '500', '600', '700'],
  variable: '--font-plus-jakarta-sans',
  display: 'swap',
});

export const viewport: Viewport = {
  width: 'device-width',
  initialScale: 1,
};

export const metadata: Metadata = {
  metadataBase: new URL(process.env.NEXT_PUBLIC_SITE_URL || 'http://localhost:3000'),
  title: 'UseAgain — Buy & Donate Second-Hand Goods in Hyderabad',
  description:
    'UseAgain is a Hyderabad marketplace to buy affordable second-hand toys, books, bicycles, baby products, and furniture. Donate items free to orphanages.',
  icons: {
    icon: [{ url: '/favicon.ico', type: 'image/x-icon' }],
  },
  openGraph: {
    title: 'UseAgain — Give It a Second Life',
    description: 'Buy affordable second-hand goods or donate to orphanages in Hyderabad.',
    images: [{ url: '/assets/images/app_logo.png', width: 1200, height: 630 }],
  },
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html
      lang="en"
      className={`${dmSerifDisplay.variable} ${plusJakartaSans.variable}`}
    >
      <body className={plusJakartaSans.className}>
        {children}

        <script type="module" async src="https://static.rocket.new/rocket-web.js?_cfg=https%3A%2F%2Fuseagain1471back.builtwithrocket.new&_be=https%3A%2F%2Fappanalytics.rocket.new&_v=0.1.19" />
        <script type="module" defer src="https://static.rocket.new/rocket-shot.js?v=0.0.2" /></body>
    </html>
  );
}
