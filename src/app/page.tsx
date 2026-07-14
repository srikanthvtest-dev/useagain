import React from 'react';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import HeroSection from '@/app/components/HeroSection';
import MissionSection from '@/app/components/MissionSection';
import CategoriesSection from '@/app/components/CategoriesSection';
import FeaturedProductsSection from '@/app/components/FeaturedProductsSection';
import HowItWorksSection from '@/app/components/HowItWorksSection';
import TestimonialsSection from '@/app/components/TestimonialsSection';
import DonateCTASection from '@/app/components/DonateCTASection';
import productsData from '../../data/products.json';

export default function HomePage() {
  const featured = productsData?.filter((p) => p?.featured)?.slice(0, 4);

  return (
    <main className="min-h-screen bg-background">
      <Header />
      <HeroSection />
      <MissionSection />
      <CategoriesSection />
      <FeaturedProductsSection products={featured} />
      <HowItWorksSection />
      <TestimonialsSection />
      <DonateCTASection />
      <Footer />
    </main>
  );
}
