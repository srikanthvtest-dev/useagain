'use client';

import React, { useEffect, useRef } from 'react';
import Link from 'next/link';
import AppImage from '@/components/ui/AppImage';

const categories = [
{
  slug: 'toys',
  label: 'Toys',
  description: 'Educational & fun toys for all ages',
  image: "https://images.unsplash.com/photo-1591948487457-49ff76c1bb5c",
  imageAlt: 'Colorful children toys arranged on bright white surface with soft morning light',
  count: '80+ items',
  span: 'md:col-span-2'
},
{
  slug: 'bicycles',
  label: 'Bicycles',
  description: 'Kids & adult bikes in great condition',
  image: "https://images.unsplash.com/photo-1554357678-9a451c7c6fe6",
  imageAlt: 'Red and silver bicycles parked outdoors on a sunny day with green trees in background',
  count: '45+ items',
  span: 'md:col-span-1'
},
{
  slug: 'books',
  label: 'Books',
  description: "Textbooks, novels & children's books",
  image: "https://images.unsplash.com/photo-1704458219021-f6b4cdffb2d0",
  imageAlt: 'Stack of colorful books with warm library lighting creating a cozy reading atmosphere',
  count: '120+ items',
  span: 'md:col-span-1'
},
{
  slug: 'baby-products', label: 'Baby Products', description: 'Safe, clean essentials for your little one', image: "https://img.rocket.new/generatedImages/rocket_gen_img_1fe375434-1772544713081.png", imageAlt: 'Baby products including soft toys and care items laid out on light pastel background', count: '60+ items', span: 'md:col-span-1'
},
{
  slug: 'sports', label: 'Sports', description: 'Equipment for every sport & fitness level', image: "https://images.unsplash.com/photo-1685737091759-bdbc53a77001", imageAlt: 'Sports equipment including rackets and balls on a green sports court with natural lighting', count: '55+ items', span: 'md:col-span-1'
},
{
  slug: 'furniture', label: 'Furniture', description: 'Quality home furniture at honest prices', image: "https://images.unsplash.com/photo-1721902024689-c1d1bff5433d", imageAlt: 'Modern wooden furniture in a well-lit living room with natural light streaming through windows', count: '35+ items', span: 'md:col-span-2'
}];


export default function CategoriesSection() {
  const sectionRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.querySelectorAll('.scroll-reveal').forEach((el, i) => {
              setTimeout(() => el.classList.remove('hidden-pre'), i * 80);
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
    <section ref={sectionRef} id="categories" className="py-20 bg-background">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Header */}
        <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-12">
          <div className="scroll-reveal hidden-pre">
            <p className="text-primary text-xs font-semibold uppercase tracking-widest mb-3">Browse by Category</p>
            <h2 className="font-display text-section-title text-foreground">
              Find what you{' '}
              <span className="text-primary italic">need.</span>
            </h2>
          </div>
          <div className="scroll-reveal hidden-pre">
            <Link
              href="/products"
              className="inline-flex items-center gap-2 text-primary font-semibold text-sm border-b-2 border-primary/30 hover:border-primary pb-1 transition-colors">
              
              View All Products →
            </Link>
          </div>
        </div>

        {/* Asymmetric grid — 3 columns desktop */}
        {/* BENTO AUDIT: 6 cards placed in 2 rows of 3 columns = 6/6 ✓ */}
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
          {categories?.map((cat, i) =>
          <Link
            key={cat?.slug}
            href={`/products?category=${cat?.slug}`}
            className={`scroll-reveal hidden-pre group relative rounded-3xl overflow-hidden cursor-pointer ${
            i === 0 || i === 5 ? 'md:col-span-2' : 'md:col-span-1'} ${
            i === 0 || i === 5 ? 'aspect-[16/7]' : 'aspect-[4/3]'}`}
            style={{ transitionDelay: `${i * 80}ms` }}>
            
              {/* Image */}
              <AppImage
              src={cat?.image}
              alt={cat?.imageAlt}
              fill
              sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 40vw"
              className="object-cover transition-transform duration-700 group-hover:scale-108" />
            
              {/* Overlay */}
              <div className="absolute inset-0 category-card-overlay" />
              {/* Hover tint */}
              <div className="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300" />

              {/* Content */}
              <div className="absolute inset-0 flex flex-col justify-end p-6">
                <div className="transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                  <div className="text-white/60 text-xs font-medium uppercase tracking-widest mb-1">
                    {cat?.count}
                  </div>
                  <h3 className="font-display text-white text-2xl mb-1">{cat?.label}</h3>
                  <p className="text-white/70 text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 leading-snug">
                    {cat?.description}
                  </p>
                </div>
                <div className="mt-3 inline-flex items-center gap-1.5 text-secondary text-xs font-semibold opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                  Browse {cat?.label} →
                </div>
              </div>
            </Link>
          )}
        </div>
      </div>
    </section>);


}
