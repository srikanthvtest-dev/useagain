'use client';

import React, { useState, useEffect, Suspense } from 'react';
import { useSearchParams } from 'next/navigation';
import Link from 'next/link';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import ProductCard from '@/components/ProductCard';
import AppImage from '@/components/ui/AppImage';
import Icon from '@/components/ui/AppIcon';
import productsData from '../../../data/products.json';

const WHATSAPP_BASE = 'https://wa.me/919492060241';

const conditionBadgeClass: Record<string, string> = {
  'Like New': 'badge-condition-like-new',
  'Very Good': 'badge-condition-very-good',
  'Good': 'badge-condition-good',
  'Fair': 'badge-condition-fair',
};

const categoryLabels: Record<string, string> = {
  toys: 'Toys',
  bicycles: 'Bicycles',
  books: 'Books',
  'baby-products': 'Baby Products',
  sports: 'Sports',
  furniture: 'Furniture',
};

function ProductDetailsContent() {
  const searchParams = useSearchParams();
  const id = searchParams.get('id');
  const [activeImage, setActiveImage] = useState(0);

  const product = productsData.find((p) => p.id === id);
  const related = productsData
    .filter((p) => p.category === product?.category && p.id !== product?.id)
    .slice(0, 4);

  useEffect(() => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }, [id]);

  if (!product) {
    return (
      <main className="min-h-screen bg-background">
        <Header />
        <div className="flex flex-col items-center justify-center min-h-[60vh] gap-4">
          <Icon name="ExclamationTriangleIcon" size={48} className="text-muted-foreground" />
          <h1 className="font-display text-2xl text-foreground">Product not found</h1>
          <Link href="/products" className="text-primary font-semibold hover:underline">
            ← Back to Products
          </Link>
        </div>
        <Footer />
      </main>
    );
  }

  const whatsappMsg = encodeURIComponent(
    `Hi! I'm interested in "${product.title}" listed on UseAgain (${product.location}, Hyderabad). Is it still available?`
  );
  const whatsappUrl = `${WHATSAPP_BASE}?text=${whatsappMsg}`;

  const badgeClass = conditionBadgeClass[product.condition] || 'badge-condition-good';
  const categoryLabel = categoryLabels[product.category] || product.category;

  const allImages = product.images.length > 1 ? product.images : [
    product.images[0],
    product.images[0],
    product.images[0],
  ];

  return (
    <main className="min-h-screen bg-background">
      <Header />

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-16">
        {/* Breadcrumb */}
        <nav className="flex items-center gap-2 text-sm text-muted-foreground mb-8" aria-label="Breadcrumb">
          <Link href="/" className="hover:text-primary transition-colors">Home</Link>
          <Icon name="ChevronRightIcon" size={14} />
          <Link href="/products" className="hover:text-primary transition-colors">Products</Link>
          <Icon name="ChevronRightIcon" size={14} />
          <Link href={`/products?category=${product.category}`} className="hover:text-primary transition-colors capitalize">
            {categoryLabel}
          </Link>
          <Icon name="ChevronRightIcon" size={14} />
          <span className="text-foreground font-medium truncate max-w-[200px]">{product.title}</span>
        </nav>

        {/* Main Content */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 mb-16">
          {/* Left: Image Gallery */}
          <div className="flex flex-col gap-4">
            <div className="relative aspect-[4/3] rounded-3xl overflow-hidden bg-muted border border-border">
              <AppImage
                src={allImages[activeImage]}
                alt={`${product.title} — ${product.condition} condition product photo, ${product.location} Hyderabad`}
                fill
                priority
                sizes="(max-width: 1024px) 100vw, 50vw"
                className="object-cover"
              />
              {product.forDonation && (
                <div className="absolute top-4 left-4 flex items-center gap-1.5 bg-primary text-primary-foreground px-3 py-1.5 rounded-full text-sm font-semibold">
                  <Icon name="GiftIcon" size={14} variant="solid" />
                  Free Donation
                </div>
              )}
            </div>

            <div className="flex gap-3">
              {allImages.map((img, i) => (
                <button
                  key={i}
                  onClick={() => setActiveImage(i)}
                  className={`relative w-20 h-16 rounded-xl overflow-hidden border-2 transition-all flex-shrink-0 ${
                    activeImage === i ? 'border-primary shadow-card' : 'border-border hover:border-primary/50'
                  }`}
                >
                  <AppImage
                    src={img}
                    alt={`${product.title} thumbnail ${i + 1}`}
                    fill
                    sizes="80px"
                    className="object-cover"
                  />
                </button>
              ))}
            </div>
          </div>

          {/* Right: Product Info */}
          <div className="flex flex-col">
            <div className="flex items-center gap-3 mb-4">
              <span className="text-xs font-semibold text-primary uppercase tracking-widest bg-primary/10 px-3 py-1 rounded-full">
                {categoryLabel}
              </span>
              <span className={`text-xs font-semibold px-3 py-1 rounded-full ${badgeClass}`}>
                {product.condition}
              </span>
            </div>

            <h1 className="font-display text-3xl md:text-4xl text-foreground mb-4 leading-tight">
              {product.title}
            </h1>

            <div className="inline-flex items-center gap-2 bg-secondary/15 border border-secondary/30 px-4 py-2.5 rounded-xl mb-6 self-start">
              <div className="w-2.5 h-2.5 rounded-full bg-secondary animate-pulse" />
              <span className="text-secondary font-semibold text-sm">Available for Sale</span>
            </div>

            <div className="bg-muted rounded-2xl p-5 mb-6">
              <h3 className="text-xs font-semibold text-muted-foreground uppercase tracking-widest mb-3">Description</h3>
              <p className="text-foreground text-base leading-relaxed">{product.description}</p>
            </div>

            <div className="grid grid-cols-2 gap-3 mb-8">
              <div className="bg-card border border-border rounded-xl p-4">
                <div className="flex items-center gap-2 text-muted-foreground mb-1">
                  <Icon name="MapPinIcon" size={14} />
                  <span className="text-xs font-medium uppercase tracking-wide">Location</span>
                </div>
                <p className="text-foreground font-semibold text-sm">{product.location}, Hyderabad</p>
              </div>
              <div className="bg-card border border-border rounded-xl p-4">
                <div className="flex items-center gap-2 text-muted-foreground mb-1">
                  <Icon name="UserIcon" size={14} />
                  <span className="text-xs font-medium uppercase tracking-wide">Seller</span>
                </div>
                <p className="text-foreground font-semibold text-sm">{product.seller}</p>
              </div>
              <div className="bg-card border border-border rounded-xl p-4">
                <div className="flex items-center gap-2 text-muted-foreground mb-1">
                  <Icon name="CalendarIcon" size={14} />
                  <span className="text-xs font-medium uppercase tracking-wide">Listed</span>
                </div>
                <p className="text-foreground font-semibold text-sm">
                  {product.listedDate}
                </p>
              </div>
              <div className="bg-card border border-border rounded-xl p-4">
                <div className="flex items-center gap-2 text-muted-foreground mb-1">
                  <Icon name="TagIcon" size={14} />
                  <span className="text-xs font-medium uppercase tracking-wide">Category</span>
                </div>
                <p className="text-foreground font-semibold text-sm capitalize">{categoryLabel}</p>
              </div>
            </div>

            <a
              href={whatsappUrl}
              target="_blank"
              rel="noopener noreferrer"
              className="flex items-center justify-center gap-3 w-full bg-primary text-primary-foreground px-8 py-4 rounded-2xl text-base font-semibold hover:bg-primary/90 transition-all duration-300 hover:shadow-card-hover mb-3 min-h-[56px]"
            >
              <Icon name="ChatBubbleLeftRightIcon" size={20} />
              Enquire on WhatsApp
            </a>

            <p className="text-center text-muted-foreground text-xs">
              You'll be connected directly with {product.seller} via WhatsApp
            </p>
          </div>
        </div>

        {/* Related Products */}
        {related.length > 0 && (
          <div>
            <h2 className="font-display text-2xl text-foreground mb-6">
              More in{' '}
              <span className="text-primary italic">{categoryLabel}</span>
            </h2>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
              {related.map((p) => (
                <ProductCard key={p.id} product={p} />
              ))}
            </div>
          </div>
        )}
      </div>

      <Footer />
    </main>
  );
}

export default function ProductDetailsPage() {
  return (
    <Suspense fallback={
      <main className="min-h-screen bg-background">
        <Header />
        <div className="flex items-center justify-center min-h-[60vh]">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary" />
        </div>
        <Footer />
      </main>
    }>
      <ProductDetailsContent />
    </Suspense>
  );
}
