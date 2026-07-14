'use client';

import React, { useEffect, useRef } from 'react';
import Icon from '@/components/ui/AppIcon';

const stats = [
  {
    icon: 'ArrowPathIcon',
    value: '500+',
    label: 'Items Rehomed',
    description: 'Products found new homes instead of landfills',
  },
  {
    icon: 'HeartIcon',
    value: '12',
    label: 'Orphanages Served',
    description: 'Across Greater Hyderabad receive free donations',
  },
  {
    icon: 'UserGroupIcon',
    value: '1,200+',
    label: 'Happy Families',
    description: 'Saved money on quality second-hand essentials',
  },
  {
    icon: 'LeafIcon',
    value: '2.4 T',
    label: 'Waste Prevented',
    description: 'Estimated carbon footprint reduction in kg CO₂',
  },
];

export default function MissionSection() {
  const sectionRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.querySelectorAll('.scroll-reveal').forEach((el, i) => {
              setTimeout(() => {
                el.classList.remove('hidden-pre');
              }, i * 120);
            });
          }
        });
      },
      { threshold: 0.15 }
    );
    if (sectionRef.current) observer.observe(sectionRef.current);
    return () => observer.disconnect();
  }, []);

  return (
    <section ref={sectionRef} className="py-20 bg-foreground text-white relative overflow-hidden">
      {/* Decorative blob */}
      <div className="absolute right-0 top-1/2 -translate-y-1/2 w-80 h-80 blob-primary opacity-20 pointer-events-none" />

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
          {/* Left: Mission text */}
          <div className="scroll-reveal hidden-pre">
            <div className="inline-flex items-center gap-2 bg-secondary/20 border border-secondary/30 px-4 py-2 rounded-full text-secondary text-xs font-semibold uppercase tracking-widest mb-6">
              <Icon name="SparklesIcon" size={13} variant="solid" />
              Our Mission
            </div>
            <h2 className="font-display text-section-title text-white mb-6">
              Waste less.{' '}
              <span className="text-secondary italic">Give more.</span>
            </h2>
            <p className="text-white/65 text-lg leading-relaxed mb-6">
              Every year, millions of perfectly usable toys, books, and baby products end up discarded. UseAgain connects families who want to declutter with families who need affordable essentials — and routes suitable donations directly to orphanages.
            </p>
            <p className="text-white/55 text-base leading-relaxed">
              No middlemen. No commissions on donations. Just a simple WhatsApp conversation that changes lives.
            </p>
          </div>

          {/* Right: Stats grid */}
          <div className="grid grid-cols-2 gap-4">
            {stats.map((stat, i) => (
              <div
                key={stat.label}
                className="scroll-reveal hidden-pre bg-white/5 border border-white/10 rounded-2xl p-6 hover:bg-white/8 transition-colors"
                style={{ transitionDelay: `${(i + 1) * 100}ms` }}
              >
                <div className="w-10 h-10 bg-secondary/20 rounded-xl flex items-center justify-center mb-4">
                  <Icon name={stat.icon as any} size={20} className="text-secondary" />
                </div>
                <div className="text-3xl font-display text-white mb-1">{stat.value}</div>
                <div className="text-sm font-semibold text-white/80 mb-1">{stat.label}</div>
                <div className="text-xs text-white/40 leading-relaxed">{stat.description}</div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}
