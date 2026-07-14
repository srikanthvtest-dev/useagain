'use client';

import React, { useState, useMemo, useEffect, useRef } from 'react';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import ProductCard from '@/components/ProductCard';
import Icon from '@/components/ui/AppIcon';
import productsData from '../../../data/products.json';

const CATEGORIES = [
  { slug: 'all', label: 'All Categories' },
  { slug: 'toys', label: 'Toys' },
  { slug: 'bicycles', label: 'Bicycles' },
  { slug: 'books', label: 'Books' },
  { slug: 'baby-products', label: 'Baby Products' },
  { slug: 'sports', label: 'Sports' },
  { slug: 'furniture', label: 'Furniture' },
];

const CONDITIONS = ['All', 'Like New', 'Very Good', 'Good', 'Fair'];
const SORT_OPTIONS = [
  { value: 'newest', label: 'Newest First' },
  { value: 'oldest', label: 'Oldest First' },
  { value: 'az', label: 'A → Z' },
  { value: 'za', label: 'Z → A' },
];

const PAGE_SIZE = 9;

export default function ProductsPage() {
  const [search, setSearch] = useState('');
  const [category, setCategory] = useState('all');
  const [condition, setCondition] = useState('All');
  const [sort, setSort] = useState('newest');
  const [page, setPage] = useState(1);
  const searchRef = useRef<HTMLInputElement>(null);

  // Read category from URL query param
  useEffect(() => {
    const params = new URLSearchParams(window.location.search);
    const cat = params.get('category');
    if (cat) setCategory(cat);
  }, []);

  const filtered = useMemo(() => {
    let items = [...productsData];

    if (search.trim()) {
      const q = search.toLowerCase();
      items = items.filter(
        (p) =>
          p.title.toLowerCase().includes(q) ||
          p.description.toLowerCase().includes(q) ||
          p.location.toLowerCase().includes(q)
      );
    }

    if (category !== 'all') {
      items = items.filter((p) => p.category === category);
    }

    if (condition !== 'All') {
      items = items.filter((p) => p.condition === condition);
    }

    switch (sort) {
      case 'newest':
        items.sort((a, b) => new Date(b.listedDate).getTime() - new Date(a.listedDate).getTime());
        break;
      case 'oldest':
        items.sort((a, b) => new Date(a.listedDate).getTime() - new Date(b.listedDate).getTime());
        break;
      case 'az':
        items.sort((a, b) => a.title.localeCompare(b.title));
        break;
      case 'za':
        items.sort((a, b) => b.title.localeCompare(a.title));
        break;
    }

    return items;
  }, [search, category, condition, sort]);

  const totalPages = Math.ceil(filtered.length / PAGE_SIZE);
  const paginated = filtered.slice((page - 1) * PAGE_SIZE, page * PAGE_SIZE);

  const handleFilterChange = (setter: (v: any) => void) => (val: any) => {
    setter(val);
    setPage(1);
  };

  return (
    <main className="min-h-screen bg-background">
      <Header />

      {/* Page Hero */}
      <section className="pt-28 pb-10 bg-foreground text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <p className="text-secondary text-xs font-semibold uppercase tracking-widest mb-3">Marketplace</p>
          <h1 className="font-display text-4xl md:text-5xl text-white mb-3">Browse Products</h1>
          <p className="text-white/55 text-base max-w-xl">
            {filtered.length} second-hand items available across Hyderabad.
          </p>
        </div>
      </section>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {/* Filters Bar */}
        <div className="bg-card border border-border rounded-2xl p-5 mb-8 shadow-card">
          {/* Search */}
          <div className="relative mb-4">
            <div className="absolute left-4 top-1/2 -translate-y-1/2 text-muted-foreground">
              <Icon name="MagnifyingGlassIcon" size={18} />
            </div>
            <input
              ref={searchRef}
              type="text"
              placeholder="Search by product name, type, or location..."
              value={search}
              onChange={(e) => handleFilterChange(setSearch)(e.target.value)}
              className="w-full pl-11 pr-4 py-3 bg-background border border-border rounded-xl text-foreground placeholder:text-muted-foreground text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
            />
            {search && (
              <button
                onClick={() => handleFilterChange(setSearch)('')}
                className="absolute right-4 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
              >
                <Icon name="XMarkIcon" size={16} />
              </button>
            )}
          </div>

          {/* Category Pills */}
          <div className="flex gap-2 flex-wrap mb-4">
            {CATEGORIES.map((cat) => (
              <button
                key={cat.slug}
                onClick={() => handleFilterChange(setCategory)(cat.slug)}
                className={`px-4 py-2 rounded-full text-sm font-medium transition-all min-h-[36px] ${
                  category === cat.slug
                    ? 'bg-primary text-primary-foreground shadow-card'
                    : 'bg-muted text-muted-foreground hover:bg-primary/10 hover:text-primary'
                }`}
              >
                {cat.label}
              </button>
            ))}
          </div>

          {/* Condition + Sort row */}
          <div className="flex flex-col sm:flex-row gap-3">
            <div className="flex gap-2 flex-wrap">
              {CONDITIONS.map((cond) => (
                <button
                  key={cond}
                  onClick={() => handleFilterChange(setCondition)(cond)}
                  className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition-all min-h-[32px] ${
                    condition === cond
                      ? 'bg-accent text-accent-foreground'
                      : 'bg-muted text-muted-foreground hover:bg-accent/10 hover:text-accent'
                  }`}
                >
                  {cond}
                </button>
              ))}
            </div>
            <div className="sm:ml-auto">
              <select
                value={sort}
                onChange={(e) => handleFilterChange(setSort)(e.target.value)}
                className="px-4 py-2 bg-background border border-border rounded-xl text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary min-h-[36px]"
              >
                {SORT_OPTIONS.map((opt) => (
                  <option key={opt.value} value={opt.value}>
                    {opt.label}
                  </option>
                ))}
              </select>
            </div>
          </div>
        </div>

        {/* Results */}
        {paginated.length === 0 ? (
          <div className="text-center py-24">
            <div className="w-16 h-16 bg-muted rounded-2xl flex items-center justify-center mx-auto mb-4">
              <Icon name="MagnifyingGlassIcon" size={28} className="text-muted-foreground" />
            </div>
            <h3 className="font-display text-xl text-foreground mb-2">No products found</h3>
            <p className="text-muted-foreground text-sm">
              Try adjusting your filters or search term.
            </p>
            <button
              onClick={() => {
                setSearch('');
                setCategory('all');
                setCondition('All');
                setPage(1);
              }}
              className="mt-4 text-primary font-semibold text-sm hover:underline"
            >
              Clear all filters
            </button>
          </div>
        ) : (
          <>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-10">
              {paginated.map((product) => (
                <ProductCard key={product.id} product={product} />
              ))}
            </div>

            {/* Pagination */}
            {totalPages > 1 && (
              <div className="flex items-center justify-center gap-2 pt-4">
                <button
                  onClick={() => setPage((p) => Math.max(1, p - 1))}
                  disabled={page === 1}
                  className="flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-border text-sm font-medium text-foreground/70 hover:bg-muted disabled:opacity-40 disabled:cursor-not-allowed transition-colors min-h-[44px]"
                >
                  <Icon name="ChevronLeftIcon" size={16} />
                  Prev
                </button>

                {Array.from({ length: totalPages }).map((_, i) => (
                  <button
                    key={i}
                    onClick={() => setPage(i + 1)}
                    className={`w-10 h-10 rounded-xl text-sm font-medium transition-colors min-h-[44px] ${
                      page === i + 1
                        ? 'bg-primary text-primary-foreground'
                        : 'border border-border text-foreground/70 hover:bg-muted'
                    }`}
                  >
                    {i + 1}
                  </button>
                ))}

                <button
                  onClick={() => setPage((p) => Math.min(totalPages, p + 1))}
                  disabled={page === totalPages}
                  className="flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-border text-sm font-medium text-foreground/70 hover:bg-muted disabled:opacity-40 disabled:cursor-not-allowed transition-colors min-h-[44px]"
                >
                  Next
                  <Icon name="ChevronRightIcon" size={16} />
                </button>
              </div>
            )}
          </>
        )}
      </div>

      <Footer />
    </main>
  );
}
