'use client';

import React, { useState, useEffect } from 'react';
import Link from 'next/link';
import AppLogo from '@/components/ui/AppLogo';
import Icon from '@/components/ui/AppIcon';

const navLinks = [
  { label: 'Home', href: '/' },
  { label: 'Products', href: '/products' },
  { label: 'About', href: '/about' },
  { label: 'Contact', href: '/contact' },
  { label: 'FAQ', href: '/faq' },
];

const WHATSAPP_URL = 'https://wa.me/919492060241';

export default function Header() {
  const [isScrolled, setIsScrolled] = useState(false);
  const [mobileOpen, setMobileOpen] = useState(false);

  useEffect(() => {
    const handleScroll = () => setIsScrolled(window.scrollY > 40);
    window.addEventListener('scroll', handleScroll, { passive: true });
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  useEffect(() => {
    if (mobileOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
    return () => { document.body.style.overflow = ''; };
  }, [mobileOpen]);

  return (
    <>
      <header
        className={`fixed top-0 left-0 w-full z-50 transition-all duration-500 ${
          isScrolled
            ? 'bg-white/95 backdrop-blur-xl shadow-nav py-3 border-b border-border'
            : 'bg-transparent py-5'
        }`}
      >
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
          {/* Logo */}
          <Link href="/" className="flex items-center gap-2.5 group">
            <AppLogo
              size={36}
              className="transition-transform duration-300 group-hover:scale-105"
            />
            <span
              className={`font-display text-xl font-normal tracking-tight transition-colors duration-300 ${
                isScrolled ? 'text-foreground' : 'text-white'
              }`}
            >
              UseAgain
            </span>
          </Link>

          {/* Desktop Nav */}
          <nav className="hidden md:flex items-center gap-7">
            {navLinks?.map((link) => (
              <Link
                key={link?.href}
                href={link?.href}
                className={`text-sm font-medium transition-colors duration-200 hover:text-primary ${
                  isScrolled ? 'text-foreground/70' : 'text-white/85 hover:text-white'
                }`}
              >
                {link?.label}
              </Link>
            ))}
          </nav>

          {/* CTA */}
          <div className="hidden md:flex items-center gap-3">
            <a
              href={WHATSAPP_URL}
              target="_blank"
              rel="noopener noreferrer"
              className="flex items-center gap-2 bg-primary text-primary-foreground px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-primary/90 transition-all duration-200 hover:shadow-card"
            >
              <Icon name="PlusCircleIcon" size={16} variant="solid" />
              Donate or Sell
            </a>
          </div>

          {/* Mobile Hamburger */}
          <button
            onClick={() => setMobileOpen(true)}
            className={`md:hidden p-2 rounded-lg transition-colors ${
              isScrolled ? 'text-foreground hover:bg-muted' : 'text-white hover:bg-white/10'
            }`}
            aria-label="Open menu"
          >
            <Icon name="Bars3Icon" size={24} />
          </button>
        </div>
      </header>
      {/* Mobile Menu Overlay */}
      {mobileOpen && (
        <div className="fixed inset-0 z-[100] flex flex-col">
          <div
            className="absolute inset-0 bg-foreground/50 backdrop-blur-sm"
            onClick={() => setMobileOpen(false)}
          />
          <div className="relative ml-auto w-4/5 max-w-sm h-full bg-white flex flex-col shadow-2xl animate-scale-in">
            {/* Header */}
            <div className="flex items-center justify-between px-6 py-5 border-b border-border">
              <div className="flex items-center gap-2">
                <AppLogo size={32} />
                <span className="font-display text-lg text-foreground">UseAgain</span>
              </div>
              <button
                onClick={() => setMobileOpen(false)}
                className="p-2 rounded-lg text-foreground/60 hover:bg-muted transition-colors"
                aria-label="Close menu"
              >
                <Icon name="XMarkIcon" size={22} />
              </button>
            </div>

            {/* Links */}
            <nav className="flex flex-col px-6 py-8 gap-1 flex-1">
              {navLinks?.map((link) => (
                <Link
                  key={link?.href}
                  href={link?.href}
                  onClick={() => setMobileOpen(false)}
                  className="flex items-center gap-3 px-4 py-3.5 rounded-xl text-foreground/80 font-medium hover:bg-muted hover:text-primary transition-colors min-h-[44px]"
                >
                  {link?.label}
                </Link>
              ))}
            </nav>

            {/* Mobile CTA */}
            <div className="px-6 pb-8">
              <a
                href={WHATSAPP_URL}
                target="_blank"
                rel="noopener noreferrer"
                onClick={() => setMobileOpen(false)}
                className="flex items-center justify-center gap-2 w-full bg-primary text-primary-foreground px-5 py-4 rounded-2xl text-base font-semibold hover:bg-primary/90 transition-all min-h-[44px]"
              >
                <Icon name="PlusCircleIcon" size={18} variant="solid" />
                Donate or Sell on WhatsApp
              </a>
            </div>
          </div>
        </div>
      )}
    </>
  );
}
