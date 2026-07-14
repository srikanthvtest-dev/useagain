'use client';

import React, { useEffect, useRef } from 'react';
import Link from 'next/link';
import AppImage from '@/components/ui/AppImage';
import Icon from '@/components/ui/AppIcon';

const WHATSAPP_URL = 'https://wa.me/919492060241';

export default function HeroSection() {
  const line1Ref = useRef<HTMLSpanElement>(null);
  const line2Ref = useRef<HTMLSpanElement>(null);
  const line3Ref = useRef<HTMLSpanElement>(null);
  const subtitleRef = useRef<HTMLParagraphElement>(null);
  const ctaRef = useRef<HTMLDivElement>(null);
  const statsRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const delays = [
    { ref: line1Ref, delay: 200 },
    { ref: line2Ref, delay: 380 },
    { ref: line3Ref, delay: 530 },
    { ref: subtitleRef, delay: 680 },
    { ref: ctaRef, delay: 820 },
    { ref: statsRef, delay: 960 }];


    delays?.forEach(({ ref, delay }) => {
      const el = ref?.current;
      if (!el) return;
      const timer = setTimeout(() => {
        el?.classList?.add('revealed');
      }, delay);
      return () => clearTimeout(timer);
    });
  }, []);

  return (
    <section className="relative min-h-screen flex flex-col overflow-hidden">
      {/* Background image */}
      <div className="absolute inset-0 z-0">
        <AppImage
          src="https://images.unsplash.com/photo-1657208190289-331f8fd47a4d"
          alt="Family browsing second-hand goods outdoors in warm afternoon light, lush greenery in background"
          fill
          priority
          sizes="100vw"
          className="object-cover" />
        
        {/* Scrim overlay — dark on left for white text legibility */}
        <div className="absolute inset-0 hero-scrim" />
        {/* Additional bottom gradient */}
        <div className="absolute bottom-0 left-0 right-0 h-40 bg-gradient-to-t from-foreground/60 to-transparent" />
      </div>
      {/* Decorative blobs */}
      <div className="absolute top-1/4 right-10 w-96 h-96 blob-accent opacity-30 pointer-events-none" />
      {/* Content */}
      <div className="relative z-10 flex-1 flex flex-col justify-center max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-32 pb-24">
        {/* Eyebrow badge */}
        <div className="line-reveal mb-6">
          <span
            ref={line1Ref}
            className="inline-flex items-center gap-2 bg-white/15 backdrop-blur-md border border-white/25 px-4 py-2 rounded-full text-white text-xs font-semibold uppercase tracking-widest">
            
            <Icon name="ArrowPathIcon" size={14} className="text-secondary" />
            Hyderabad's Sustainability Marketplace
          </span>
        </div>

        {/* Headline */}
        <h1 className="font-display text-hero text-white mb-6 max-w-3xl">
          <span className="line-reveal block">
            <span ref={line2Ref} className="block">Give it a</span>
          </span>
          <span className="line-reveal block">
            <span ref={line3Ref} className="block text-secondary italic">second life.</span>
          </span>
        </h1>

        {/* Subheadline */}
        <p
          ref={subtitleRef}
          className="line-reveal text-white/80 text-lg md:text-xl font-normal leading-relaxed max-w-xl mb-10">
          
          Buy affordable second-hand toys, books, bicycles &amp; more — or donate your unused items to orphanages across Hyderabad. Free of cost.
        </p>

        {/* CTA Buttons */}
        <div ref={ctaRef} className="line-reveal flex flex-col sm:flex-row gap-4 mb-16">
          <Link
            href="/products"
            className="inline-flex items-center justify-center gap-2.5 bg-primary text-primary-foreground px-8 py-4 rounded-full text-base font-semibold hover:bg-primary/90 transition-all duration-300 hover:shadow-hero min-h-[52px]">
            
            <Icon name="MagnifyingGlassIcon" size={18} />
            Browse Products
          </Link>
          <a
            href={WHATSAPP_URL}
            target="_blank"
            rel="noopener noreferrer"
            className="inline-flex items-center justify-center gap-2.5 bg-white/15 backdrop-blur-md border border-white/30 text-white px-8 py-4 rounded-full text-base font-semibold hover:bg-white/25 transition-all duration-300 min-h-[52px]">
            
            <Icon name="GiftIcon" size={18} />
            Donate or Sell
          </a>
        </div>

        {/* Stats Row */}
        <div
          ref={statsRef}
          className="line-reveal flex flex-col sm:flex-row gap-6 sm:gap-0 sm:divide-x sm:divide-white/20">
          
          {[
          { value: '500+', label: 'Products Listed' },
          { value: '1,200+', label: 'Families Helped' },
          { value: '300+', label: 'Donations to Orphanages' }]?.
          map((stat) =>
          <div key={stat?.label} className="sm:px-8 first:pl-0 last:pr-0">
              <div className="text-3xl font-display text-white mb-1">{stat?.value}</div>
              <div className="text-white/55 text-sm font-medium">{stat?.label}</div>
            </div>
          )}
        </div>
      </div>
      {/* Scroll indicator */}
      <div className="relative z-10 flex justify-center pb-8">
        <div className="flex flex-col items-center gap-2 text-white/40 animate-float">
          <span className="text-xs font-medium uppercase tracking-widest">Scroll</span>
          <Icon name="ChevronDownIcon" size={20} />
        </div>
      </div>
    </section>);

}
