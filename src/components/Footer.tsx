import React from 'react';
import Link from 'next/link';
import AppLogo from '@/components/ui/AppLogo';
import Icon from '@/components/ui/AppIcon';

const WHATSAPP_URL = 'https://wa.me/919492060241';

export default function Footer() {
  return (
    <footer className="bg-foreground text-white border-t border-white/10">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Main Row */}
        <div className="py-12 flex flex-col md:flex-row md:items-start justify-between gap-10">
          {/* Brand */}
          <div className="max-w-xs">
            <div className="flex items-center gap-2.5 mb-4">
              <AppLogo size={34} />
              <span className="font-display text-xl text-white">UseAgain</span>
            </div>
            <p className="text-white/55 text-sm leading-relaxed">
              Reducing waste. Helping families. Supporting orphanages — one product at a time.
            </p>
          </div>

          {/* Links Grid */}
          <div className="grid grid-cols-2 sm:grid-cols-3 gap-8">
            <div>
              <p className="text-white/40 text-xs font-semibold uppercase tracking-widest mb-4">Platform</p>
              <ul className="space-y-3">
                <li><Link href="/products" className="text-white/65 hover:text-white text-sm font-medium transition-colors">Browse Products</Link></li>
                <li><Link href="/" className="text-white/65 hover:text-white text-sm font-medium transition-colors">Home</Link></li>
                <li>
                  <a href={WHATSAPP_URL} target="_blank" rel="noopener noreferrer" className="text-white/65 hover:text-white text-sm font-medium transition-colors">
                    Donate or Sell
                  </a>
                </li>
              </ul>
            </div>
            <div>
              <p className="text-white/40 text-xs font-semibold uppercase tracking-widest mb-4">Company</p>
              <ul className="space-y-3">
                <li><Link href="/about" className="text-white/65 hover:text-white text-sm font-medium transition-colors">About Us</Link></li>
                <li><Link href="/contact" className="text-white/65 hover:text-white text-sm font-medium transition-colors">Contact</Link></li>
                <li><Link href="/faq" className="text-white/65 hover:text-white text-sm font-medium transition-colors">FAQ</Link></li>
              </ul>
            </div>
            <div>
              <p className="text-white/40 text-xs font-semibold uppercase tracking-widest mb-4">Contact</p>
              <ul className="space-y-3">
                <li>
                  <a href="mailto:srikanth.v@useagain.in" className="text-white/65 hover:text-white text-sm font-medium transition-colors">
                    srikanth.v@useagain.in
                  </a>
                </li>
                <li>
                  <a href={WHATSAPP_URL} target="_blank" rel="noopener noreferrer" className="text-white/65 hover:text-white text-sm font-medium transition-colors">
                    WhatsApp: 9492060241
                  </a>
                </li>
                <li className="text-white/40 text-sm">Hyderabad, Telangana</li>
              </ul>
            </div>
          </div>
        </div>

        {/* Bottom Bar */}
        <div className="border-t border-white/10 py-6 flex flex-col sm:flex-row items-center justify-between gap-4">
          <p className="text-white/35 text-sm">
            © 2026 UseAgain. All rights reserved. Made with ♻️ in Hyderabad.
          </p>
          <div className="flex items-center gap-5">
            <a href={WHATSAPP_URL} target="_blank" rel="noopener noreferrer" className="text-white/40 hover:text-white transition-colors" aria-label="WhatsApp">
              <Icon name="ChatBubbleLeftRightIcon" size={18} />
            </a>
            <a href="mailto:srikanth.v@useagain.in" className="text-white/40 hover:text-white transition-colors" aria-label="Email">
              <Icon name="EnvelopeIcon" size={18} />
            </a>
            <Link href="/products" className="text-white/35 hover:text-white text-sm transition-colors">Privacy</Link>
            <Link href="/faq" className="text-white/35 hover:text-white text-sm transition-colors">Terms</Link>
          </div>
        </div>
      </div>
    </footer>
  );
}
