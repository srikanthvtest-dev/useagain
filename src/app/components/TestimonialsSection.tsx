'use client';

import React, { useEffect, useRef } from 'react';
import AppImage from '@/components/ui/AppImage';
import Icon from '@/components/ui/AppIcon';

const testimonials = [
{
  name: 'Kavitha Reddy',
  location: 'Banjara Hills',
  role: 'Mother of two',
  quote:
  'I found a barely-used baby walker for my daughter at a fraction of the retail price. The seller was just 3 km away in Jubilee Hills. UseAgain saved me a lot!',
  image: "https://img.rocket.new/generatedImages/rocket_gen_img_1d1a6cf71-1763301532880.png",
  imageAlt: 'Smiling Indian woman in her thirties with dark hair, warm natural background lighting',
  rating: 5,
  highlight: true
},
{
  name: 'Ravi Shankar',
  location: 'Gachibowli',
  role: 'Software Engineer',
  quote:
  'Donated my old bicycle and 30 books to an orphanage through UseAgain. The team handled everything. I didn\'t have to do anything except message on WhatsApp.',
  image: "https://img.rocket.new/generatedImages/rocket_gen_img_12ea075c2-1763300507102.png",
  imageAlt: 'Professional Indian man in his forties, confident expression, office background',
  rating: 5,
  highlight: false
},
{
  name: 'Sunita Rao', location: 'Kondapur', role: 'Primary School Teacher', quote: 'My students needed NCERT books. A parent listed a full set on UseAgain. We got them for almost nothing. This platform is doing incredible work for education.', image: "https://img.rocket.new/generatedImages/rocket_gen_img_178471507-1772439819553.png", imageAlt: 'Cheerful Indian teacher woman, bright expression, natural daylight background',
  rating: 5,
  highlight: false
},
{
  name: 'Arjun Menon', location: 'Hitech City', role: 'Startup Founder', quote: 'Listed our office furniture when we downsized. Got enquiries within hours. UseAgain made the whole process effortless — and one desk went to an NGO for free.', image: "https://img.rocket.new/generatedImages/rocket_gen_img_110ce9b39-1763295413245.png", imageAlt: 'Young Indian entrepreneur man, casual confident look, modern workspace background',
  rating: 5,
  highlight: false
}];


export default function TestimonialsSection() {
  const sectionRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.querySelectorAll('.scroll-reveal').forEach((el, i) => {
              setTimeout(() => el.classList.remove('hidden-pre'), i * 100);
            });
          }
        });
      },
      { threshold: 0.1 }
    );
    if (sectionRef?.current) observer?.observe(sectionRef?.current);
    return () => observer?.disconnect();
  }, []);

  return (
    <section ref={sectionRef} className="py-20 bg-muted">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Header */}
        <div className="text-center mb-14">
          <div className="scroll-reveal hidden-pre">
            <p className="text-primary text-xs font-semibold uppercase tracking-widest mb-4">From Our Community</p>
            <h2 className="font-display text-section-title text-foreground">
              Real families.{' '}
              <span className="text-primary italic">Real stories.</span>
            </h2>
          </div>
        </div>

        {/* Masonry-style testimonial grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
          {testimonials?.map((t, i) =>
          <div
            key={t?.name}
            className={`scroll-reveal hidden-pre rounded-3xl p-6 border hover-lift ${
            t?.highlight ?
            'bg-foreground text-white border-foreground/20' :
            'bg-card text-card-foreground border-border shadow-card'} ${
            i === 0 ? 'lg:row-span-1' : ''}`}
            style={{ transitionDelay: `${i * 100}ms` }}>
            
              {/* Stars */}
              <div className="flex gap-1 mb-4">
                {Array.from({ length: t?.rating })?.map((_, s) =>
              <Icon
                key={s}
                name="StarIcon"
                size={14}
                variant="solid"
                className={t?.highlight ? 'text-secondary' : 'text-primary'} />

              )}
              </div>

              {/* Quote */}
              <p
              className={`text-sm leading-relaxed mb-6 ${
              t?.highlight ? 'text-white/80' : 'text-muted-foreground'}`
              }>
              
                "{t?.quote}"
              </p>

              {/* Author */}
              <div className="flex items-center gap-3 pt-4 border-t border-border/30">
                <div className="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                  <AppImage
                  src={t?.image}
                  alt={t?.imageAlt}
                  width={40}
                  height={40}
                  className="w-full h-full object-cover" />
                
                </div>
                <div>
                  <div className={`text-sm font-semibold ${t?.highlight ? 'text-white' : 'text-card-foreground'}`}>
                    {t?.name}
                  </div>
                  <div className={`text-xs ${t?.highlight ? 'text-white/50' : 'text-muted-foreground'}`}>
                    {t?.role} · {t?.location}
                  </div>
                </div>
              </div>
            </div>
          )}
        </div>
      </div>
    </section>);


}
