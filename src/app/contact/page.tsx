import React from 'react';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import Icon from '@/components/ui/AppIcon';

const WHATSAPP_URL = 'https://wa.me/919492060241';

export default function ContactPage() {
  return (
    <main className="min-h-screen bg-background">
      <Header />

      <section className="pt-28 pb-16 bg-foreground text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <p className="text-secondary text-xs font-semibold uppercase tracking-widest mb-4">Get In Touch</p>
          <h1 className="font-display text-5xl md:text-6xl text-white mb-4">Contact Us</h1>
          <p className="text-white/55 text-lg max-w-xl">
            Have a question, want to list an item, or donate to an orphanage? We're just a WhatsApp away.
          </p>
        </div>
      </section>

      <section className="py-20">
        <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-10">
            {/* Contact Info */}
            <div>
              <h2 className="font-display text-2xl text-foreground mb-8">Reach us directly</h2>

              <div className="space-y-5">
                <a
                  href={WHATSAPP_URL}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="flex items-start gap-4 p-5 bg-card border border-border rounded-2xl hover:border-primary/40 hover:shadow-card transition-all group"
                >
                  <div className="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-primary/20 transition-colors">
                    <Icon name="ChatBubbleLeftRightIcon" size={22} className="text-primary" />
                  </div>
                  <div>
                    <div className="text-xs font-semibold text-muted-foreground uppercase tracking-widest mb-1">WhatsApp</div>
                    <div className="text-foreground font-semibold">+91 94920 60241</div>
                    <div className="text-muted-foreground text-sm mt-1">Fastest response — usually within 2 hours</div>
                  </div>
                </a>

                <a
                  href="mailto:srikanth.v@useagain.in"
                  className="flex items-start gap-4 p-5 bg-card border border-border rounded-2xl hover:border-primary/40 hover:shadow-card transition-all group"
                >
                  <div className="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-accent/20 transition-colors">
                    <Icon name="EnvelopeIcon" size={22} className="text-accent" />
                  </div>
                  <div>
                    <div className="text-xs font-semibold text-muted-foreground uppercase tracking-widest mb-1">Email</div>
                    <div className="text-foreground font-semibold">srikanth.v@useagain.in</div>
                    <div className="text-muted-foreground text-sm mt-1">For detailed enquiries and partnerships</div>
                  </div>
                </a>

                <div className="flex items-start gap-4 p-5 bg-card border border-border rounded-2xl">
                  <div className="w-12 h-12 bg-secondary/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <Icon name="MapPinIcon" size={22} className="text-secondary" />
                  </div>
                  <div>
                    <div className="text-xs font-semibold text-muted-foreground uppercase tracking-widest mb-1">Location</div>
                    <div className="text-foreground font-semibold">Hyderabad, Telangana</div>
                    <div className="text-muted-foreground text-sm mt-1">Serving all areas of Greater Hyderabad</div>
                  </div>
                </div>
              </div>
            </div>

            {/* Quick Form (static, with WhatsApp fallback) */}
            <div className="bg-card border border-border rounded-3xl p-8 shadow-card">
              <h2 className="font-display text-xl text-foreground mb-6">Send a message</h2>
              <div className="space-y-4">
                <div>
                  <label className="block text-xs font-semibold text-muted-foreground uppercase tracking-widest mb-2">
                    Your Name
                  </label>
                  <input
                    type="text"
                    placeholder="e.g. Priya Sharma"
                    className="w-full px-4 py-3 bg-background border border-border rounded-xl text-foreground placeholder:text-muted-foreground text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                  />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-muted-foreground uppercase tracking-widest mb-2">
                    Phone / WhatsApp
                  </label>
                  <input
                    type="tel"
                    placeholder="+91 98765 43210"
                    className="w-full px-4 py-3 bg-background border border-border rounded-xl text-foreground placeholder:text-muted-foreground text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                  />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-muted-foreground uppercase tracking-widest mb-2">
                    Message
                  </label>
                  <textarea
                    rows={4}
                    placeholder="Tell us about what you'd like to buy, sell, or donate..."
                    className="w-full px-4 py-3 bg-background border border-border rounded-xl text-foreground placeholder:text-muted-foreground text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all resize-none"
                  />
                </div>
                <a
                  href={WHATSAPP_URL}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="flex items-center justify-center gap-2 w-full bg-primary text-primary-foreground py-4 rounded-xl font-semibold hover:bg-primary/90 transition-all min-h-[52px]"
                >
                  <Icon name="ChatBubbleLeftRightIcon" size={18} />
                  Send via WhatsApp
                </a>
                <p className="text-center text-muted-foreground text-xs">
                  Messages are handled via WhatsApp for faster response
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <Footer />
    </main>
  );
}
