'use client';

import React, { useState } from 'react';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import Icon from '@/components/ui/AppIcon';
import Link from 'next/link';

const WHATSAPP_URL = 'https://wa.me/919492060241';

const faqs = [
  {
    question: 'How do I buy a product on UseAgain?',
    answer:
      'Browse our product listings, find something you like, and click the "Enquire on WhatsApp" button on the product page. You\'ll be connected directly with the seller via WhatsApp to arrange payment and pickup.',
  },
  {
    question: 'How do I list an item for sale?',
    answer:
      'Simply WhatsApp us at 9492060241 with photos of your item and a brief description. We\'ll create the listing for you within 24 hours. There\'s no registration or app needed.',
  },
  {
    question: 'How does the donation to orphanages work?',
    answer:
      'When you want to donate an item, WhatsApp us and mention it\'s for donation. Our team evaluates the item\'s condition. If suitable, we coordinate pickup and delivery to one of our partner orphanages in Hyderabad — completely free of charge.',
  },
  {
    question: 'Is there any fee for listing or selling?',
    answer:
      'Listing is completely free. For sales, there\'s no platform commission — you keep 100% of what you sell for. For donations, everything is free.',
  },
  {
    question: 'What categories of products can I sell or donate?',
    answer:
      'We accept Toys, Bicycles, Books, Baby Products, Sports Equipment, and Furniture. Items must be in usable condition (Good or better). We do not accept broken, heavily damaged, or unsafe items.',
  },
  {
    question: 'Are the products safe and verified?',
    answer:
      'All listings are reviewed by our team before going live. We check condition descriptions and flag any safety concerns. However, we recommend inspecting items in person before purchasing.',
  },
  {
    question: 'Which areas of Hyderabad do you serve?',
    answer:
      'We serve all areas of Greater Hyderabad including Banjara Hills, Jubilee Hills, Madhapur, Gachibowli, Kondapur, Hitech City, Kukatpally, Begumpet, Secunderabad, Ameerpet, Dilsukhnagar, LB Nagar, Miyapur, Nizampet, Uppal, Nacharam, Mehdipatnam, Abids, Nampally, Charminar, and surrounding areas.',
  },
  {
    question: 'How do I add new product images to a category folder?',
    answer:
      'Add your product images to the appropriate folder under /public/assets/images/products/{category-name}/ (e.g., /public/assets/images/products/toys/). Then update /data/products.json with the new product entry including the image path. See the README for detailed instructions.',
  },
];

export default function FAQPage() {
  const [openIndex, setOpenIndex] = useState<number | null>(0);

  return (
    <main className="min-h-screen bg-background">
      <Header />
      <section className="pt-28 pb-16 bg-foreground text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <p className="text-secondary text-xs font-semibold uppercase tracking-widest mb-4">Help Center</p>
          <h1 className="font-display text-5xl md:text-6xl text-white mb-4">
            Frequently Asked Questions
          </h1>
          <p className="text-white/55 text-lg max-w-xl">
            Everything you need to know about buying, selling, and donating on UseAgain.
          </p>
        </div>
      </section>
      <section className="py-20">
        <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="space-y-3">
            {faqs?.map((faq, i) => (
              <div
                key={i}
                className={`bg-card border rounded-2xl overflow-hidden transition-all duration-300 ${
                  openIndex === i ? 'border-primary/30 shadow-card' : 'border-border'
                }`}
              >
                <button
                  onClick={() => setOpenIndex(openIndex === i ? null : i)}
                  className="w-full flex items-center justify-between gap-4 px-6 py-5 text-left min-h-[60px]"
                >
                  <span className="font-semibold text-foreground text-base leading-snug">{faq?.question}</span>
                  <div className={`flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-300 ${
                    openIndex === i ? 'bg-primary text-primary-foreground rotate-45' : 'bg-muted text-muted-foreground'
                  }`}>
                    <Icon name="PlusIcon" size={16} />
                  </div>
                </button>

                {openIndex === i && (
                  <div className="px-6 pb-5 border-t border-border/50">
                    <p className="text-muted-foreground text-sm leading-relaxed pt-4">{faq?.answer}</p>
                  </div>
                )}
              </div>
            ))}
          </div>

          {/* Still have questions */}
          <div className="mt-12 text-center bg-muted rounded-3xl p-10">
            <div className="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mx-auto mb-4">
              <Icon name="ChatBubbleLeftRightIcon" size={22} className="text-primary" />
            </div>
            <h3 className="font-display text-xl text-foreground mb-3">Still have questions?</h3>
            <p className="text-muted-foreground text-sm mb-6">
              Our team is available on WhatsApp to answer any question you have.
            </p>
            <div className="flex flex-col sm:flex-row gap-3 justify-center">
              <a
                href={WHATSAPP_URL}
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center justify-center gap-2 bg-primary text-primary-foreground px-6 py-3 rounded-full text-sm font-semibold hover:bg-primary/90 transition-all min-h-[44px]"
              >
                <Icon name="ChatBubbleLeftRightIcon" size={16} />
                Chat on WhatsApp
              </a>
              <Link
                href="/contact"
                className="inline-flex items-center justify-center gap-2 border border-primary text-primary px-6 py-3 rounded-full text-sm font-semibold hover:bg-primary/10 transition-all min-h-[44px]"
              >
                Contact Us
              </Link>
            </div>
          </div>
        </div>
      </section>
      <Footer />
    </main>
  );
}
