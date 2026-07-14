import React from 'react';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import AppImage from '@/components/ui/AppImage';
import Icon from '@/components/ui/AppIcon';
import Link from 'next/link';

const WHATSAPP_URL = 'https://wa.me/919492060241';

export default function AboutPage() {
  return (
    <main className="min-h-screen bg-background">
      <Header />

      {/* Hero */}
      <section className="pt-28 pb-16 bg-foreground text-white relative overflow-hidden">
        <div className="absolute right-0 top-0 w-72 h-72 blob-primary opacity-20 pointer-events-none" />
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <p className="text-secondary text-xs font-semibold uppercase tracking-widest mb-4">About UseAgain</p>
          <h1 className="font-display text-5xl md:text-6xl text-white mb-6 max-w-2xl leading-tight">
            Built on a simple belief: nothing should go to waste.
          </h1>
          <p className="text-white/60 text-lg max-w-xl leading-relaxed">
            UseAgain was founded in Hyderabad with a mission to connect families who want to declutter with families who need affordable essentials — and to ensure suitable items reach children in orphanages.
          </p>
        </div>
      </section>

      {/* Story */}
      <section className="py-20 bg-background">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center mb-20">
            <div>
              <h2 className="font-display text-section-title text-foreground mb-6">
                Our{' '}
                <span className="text-primary italic">story.</span>
              </h2>
              <p className="text-muted-foreground text-base leading-relaxed mb-4">
                Every year, millions of usable products — toys outgrown by children, books already read, bicycles gathering dust — end up discarded. Meanwhile, thousands of families in Hyderabad struggle to afford even basic items for their children.
              </p>
              <p className="text-muted-foreground text-base leading-relaxed mb-4">
                UseAgain was born from this gap. We built a simple, WhatsApp-first marketplace where anyone can list items for sale or donate them to those who need them most — including orphanages across Greater Hyderabad.
              </p>
              <p className="text-muted-foreground text-base leading-relaxed">
                No complicated apps. No hidden fees. Just a community of people who believe that a product's life doesn't end when you're done with it.
              </p>
            </div>
            <div className="relative aspect-[4/3] rounded-3xl overflow-hidden">
              <AppImage
                src="https://img.rocket.new/generatedImages/rocket_gen_img_1eb98d960-1766525700639.png"
                alt="Volunteers sorting donated goods in a bright community center, warm afternoon light"
                fill
                sizes="(max-width: 1024px) 100vw, 50vw"
                className="object-cover" />
              
            </div>
          </div>

          {/* Values */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {[
            {
              icon: 'ArrowPathIcon',
              title: 'Sustainability First',
              desc: 'Every item rehomed is one less item in a landfill. We measure our success in kilograms of waste prevented.'
            },
            {
              icon: 'HeartIcon',
              title: 'Community at Heart',
              desc: 'We believe in the power of neighbours helping neighbours. Every transaction strengthens the Hyderabad community.'
            },
            {
              icon: 'ShieldCheckIcon',
              title: 'Trust & Transparency',
              desc: 'No hidden commissions on donations. What you give, goes directly to orphanages — verified and tracked.'
            }].
            map((v) =>
            <div key={v.title} className="bg-card border border-border rounded-2xl p-7 shadow-card">
                <div className="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-5">
                  <Icon name={v.icon as any} size={22} className="text-primary" />
                </div>
                <h3 className="font-display text-xl text-foreground mb-3">{v.title}</h3>
                <p className="text-muted-foreground text-sm leading-relaxed">{v.desc}</p>
              </div>
            )}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="py-16 bg-muted">
        <div className="max-w-3xl mx-auto px-4 text-center">
          <h2 className="font-display text-3xl text-foreground mb-4">Ready to join the movement?</h2>
          <p className="text-muted-foreground text-base mb-8">Browse products, list your items, or get in touch to donate.</p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link href="/products" className="inline-flex items-center justify-center gap-2 bg-primary text-primary-foreground px-8 py-4 rounded-full font-semibold hover:bg-primary/90 transition-all min-h-[52px]">
              Browse Products
            </Link>
            <a href={WHATSAPP_URL} target="_blank" rel="noopener noreferrer" className="inline-flex items-center justify-center gap-2 border border-primary text-primary px-8 py-4 rounded-full font-semibold hover:bg-primary/10 transition-all min-h-[52px]">
              <Icon name="ChatBubbleLeftRightIcon" size={18} />
              WhatsApp Us
            </a>
          </div>
        </div>
      </section>

      <Footer />
    </main>);

}
