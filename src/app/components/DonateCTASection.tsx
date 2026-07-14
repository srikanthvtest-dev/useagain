'use client';

import React, { useEffect, useRef } from 'react';
import Icon from '@/components/ui/AppIcon';

const WHATSAPP_URL = 'https://wa.me/919492060241';

export default function DonateCTASection() {
  const sectionRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.querySelectorAll('.scroll-reveal').forEach((el, i) => {
              setTimeout(() => el.classList.remove('hidden-pre'), i * 120);
            });
          }
        });
      },
      { threshold: 0.2 }
    );
    if (sectionRef?.current) observer?.observe(sectionRef?.current);
    return () => observer?.disconnect();
  }, []);

  return (
    <section ref={sectionRef} className="py-20 bg-background">
      <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="scroll-reveal hidden-pre gradient-green-blue rounded-4xl p-10 md:p-16 text-center relative overflow-hidden">
          {/* Background blob */}
          <div className="absolute top-0 right-0 w-64 h-64 blob-primary opacity-30 pointer-events-none" />
          <div className="absolute bottom-0 left-0 w-48 h-48 blob-accent opacity-20 pointer-events-none" />

          <div className="relative z-10">
            {/* Icon */}
            <div className="w-16 h-16 bg-white/15 rounded-2xl flex items-center justify-center mx-auto mb-6 animate-float">
              <Icon name="GiftIcon" size={32} className="text-white" variant="solid" />
            </div>

            <h2 className="font-display text-section-title text-white mb-5">
              Have items to donate or sell?
            </h2>
            <p className="text-white/70 text-lg leading-relaxed mb-8 max-w-xl mx-auto">
              WhatsApp us your item details and photos. We'll list it for free — and route suitable donations directly to orphanages in Hyderabad.
            </p>

            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <a
                href={WHATSAPP_URL}
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center justify-center gap-3 bg-white text-primary px-8 py-4 rounded-full text-base font-semibold hover:bg-white/90 transition-all duration-300 hover:shadow-hero min-h-[52px]"
              >
                <Icon name="ChatBubbleLeftRightIcon" size={20} className="text-primary" />
                Donate or Sell on WhatsApp
              </a>
              <a
                href="mailto:srikanth.v@useagain.in"
                className="inline-flex items-center justify-center gap-3 bg-white/15 border border-white/30 text-white px-8 py-4 rounded-full text-base font-semibold hover:bg-white/25 transition-all duration-300 min-h-[52px]"
              >
                <Icon name="EnvelopeIcon" size={20} />
                Email Us
              </a>
            </div>

            <p className="text-white/40 text-sm mt-6">
              WhatsApp: 9492060241 · srikanth.v@useagain.in · Hyderabad, Telangana
            </p>
          </div>
        </div>
      </div>
    </section>
  );
}
