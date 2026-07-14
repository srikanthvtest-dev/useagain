'use client';

import React, { useEffect, useRef } from 'react';
import Icon from '@/components/ui/AppIcon';

const steps = [
  {
    number: '01',
    icon: 'MagnifyingGlassIcon',
    title: 'Browse & Discover',
    description:
      'Explore hundreds of second-hand products across 6 categories. Filter by condition, location, and type to find exactly what your family needs.',
    color: 'bg-primary/10',
    iconColor: 'text-primary',
    accent: 'text-primary',
  },
  {
    number: '02',
    icon: 'ChatBubbleLeftRightIcon',
    title: 'Enquire on WhatsApp',
    description:
      'Found something you like? Tap the WhatsApp button on any product and connect directly with the seller. Fast, simple, no registration needed.',
    color: 'bg-secondary/10',
    iconColor: 'text-secondary',
    accent: 'text-secondary',
  },
  {
    number: '03',
    icon: 'GiftIcon',
    title: 'Buy, Sell or Donate',
    description:
      'Complete your purchase at a fair price — or donate your unused items free of cost. Suitable donations are routed to orphanages across Hyderabad.',
    color: 'bg-accent/10',
    iconColor: 'text-accent',
    accent: 'text-accent',
  },
];

export default function HowItWorksSection() {
  const sectionRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.querySelectorAll('.scroll-reveal').forEach((el, i) => {
              setTimeout(() => el.classList.remove('hidden-pre'), i * 150);
            });
          }
        });
      },
      { threshold: 0.1 }
    );
    if (sectionRef.current) observer.observe(sectionRef.current);
    return () => observer.disconnect();
  }, []);

  return (
    <section ref={sectionRef} id="how-it-works" className="py-20 bg-background">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Header */}
        <div className="text-center mb-16">
          <div className="scroll-reveal hidden-pre">
            <p className="text-primary text-xs font-semibold uppercase tracking-widest mb-4">Simple Process</p>
            <h2 className="font-display text-section-title text-foreground max-w-xl mx-auto">
              How{' '}
              <span className="text-primary italic">UseAgain</span>{' '}
              works.
            </h2>
          </div>
        </div>

        {/* Steps — 3 cards in a row */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 relative">
          {/* Connector line (desktop only) */}
          <div className="hidden md:block absolute top-12 left-[calc(16.67%+2rem)] right-[calc(16.67%+2rem)] h-px bg-border z-0" />

          {steps.map((step, i) => (
            <div
              key={step.number}
              className="scroll-reveal hidden-pre relative z-10 bg-card border border-border rounded-3xl p-8 hover-lift shadow-card"
              style={{ transitionDelay: `${i * 150}ms` }}
            >
              {/* Step number + icon */}
              <div className="flex items-center justify-between mb-6">
                <div className={`w-14 h-14 ${step.color} rounded-2xl flex items-center justify-center`}>
                  <Icon name={step.icon as any} size={26} className={step.iconColor} />
                </div>
                <span className={`text-4xl font-display font-normal ${step.accent} opacity-20`}>
                  {step.number}
                </span>
              </div>

              <h3 className="font-display text-card-foreground text-xl mb-3">{step.title}</h3>
              <p className="text-muted-foreground text-sm leading-relaxed">{step.description}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
