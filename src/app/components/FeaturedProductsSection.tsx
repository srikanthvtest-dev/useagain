'use client';

import React, { useEffect, useRef } from 'react';
import Link from 'next/link';
import ProductCard from '@/components/ProductCard';

interface Product {
  id: string;
  title: string;
  category: string;
  condition: string;
  description: string;
  images: string[];
  seller: string;
  location: string;
  city: string;
  listedDate: string;
  featured: boolean;
  forDonation: boolean;
}

interface FeaturedProductsSectionProps {
  products: Product[];
}

export default function FeaturedProductsSection({ products }: FeaturedProductsSectionProps) {
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
    if (sectionRef.current) observer.observe(sectionRef.current);
    return () => observer.disconnect();
  }, []);

  return (
    <section ref={sectionRef} className="py-20 bg-muted">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Header */}
        <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-12">
          <div className="scroll-reveal hidden-pre">
            <p className="text-primary text-xs font-semibold uppercase tracking-widest mb-3">Hand-Picked</p>
            <h2 className="font-display text-section-title text-foreground">
              Featured{' '}
              <span className="text-primary italic">products.</span>
            </h2>
          </div>
          <div className="scroll-reveal hidden-pre">
            <Link
              href="/products"
              className="inline-flex items-center gap-2 bg-primary text-primary-foreground px-6 py-3 rounded-full text-sm font-semibold hover:bg-primary/90 transition-all hover:shadow-card"
            >
              View All Products →
            </Link>
          </div>
        </div>

        {/* Products grid — 4 cards, 4 cols desktop */}
        {/* BENTO AUDIT: 4 cards in 1 row of 4 cols = 4/4 ✓ */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          {products.map((product, i) => (
            <div
              key={product.id}
              className="scroll-reveal hidden-pre"
              style={{ transitionDelay: `${i * 100}ms` }}
            >
              <ProductCard product={product} />
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
