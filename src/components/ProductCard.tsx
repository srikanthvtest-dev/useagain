import React from 'react';
import Link from 'next/link';
import AppImage from '@/components/ui/AppImage';
import Icon from '@/components/ui/AppIcon';

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

interface ProductCardProps {
  product: Product;
}

const conditionBadgeClass: Record<string, string> = {
  'Like New': 'badge-condition-like-new',
  'Very Good': 'badge-condition-very-good',
  'Good': 'badge-condition-good',
  'Fair': 'badge-condition-fair',
};

const categoryIcons: Record<string, string> = {
  toys: 'SparklesIcon',
  bicycles: 'TruckIcon',
  books: 'BookOpenIcon',
  'baby-products': 'HeartIcon',
  sports: 'TrophyIcon',
  furniture: 'HomeIcon',
};

const categoryLabels: Record<string, string> = {
  toys: 'Toys',
  bicycles: 'Bicycles',
  books: 'Books',
  'baby-products': 'Baby Products',
  sports: 'Sports',
  furniture: 'Furniture',
};

export default function ProductCard({ product }: ProductCardProps) {
  const badgeClass = conditionBadgeClass[product.condition] || 'badge-condition-good';
  const iconName = categoryIcons[product.category] || 'TagIcon';
  const categoryLabel = categoryLabels[product.category] || product.category;

  return (
    <Link
      href={`/product-details?id=${product.id}`}
      className="group block bg-card rounded-2xl overflow-hidden border border-border hover-lift shadow-card"
    >
      {/* Image */}
      <div className="relative overflow-hidden aspect-[4/3] bg-muted">
        <AppImage
          src={product.images[0]}
          alt={`${product.title} — ${product.condition} condition, listed in ${product.location}, Hyderabad`}
          fill
          sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw"
          className="object-cover transition-transform duration-500 group-hover:scale-105"
        />
        {/* Donation badge */}
        {product.forDonation && (
          <div className="absolute top-3 left-3 flex items-center gap-1.5 bg-primary text-primary-foreground px-3 py-1 rounded-full text-xs font-semibold">
            <Icon name="GiftIcon" size={12} variant="solid" />
            Free Donation
          </div>
        )}
        {product.featured && !product.forDonation && (
          <div className="absolute top-3 left-3 flex items-center gap-1.5 bg-accent text-accent-foreground px-3 py-1 rounded-full text-xs font-semibold">
            <Icon name="StarIcon" size={12} variant="solid" />
            Featured
          </div>
        )}
        {/* Condition badge */}
        <div className={`absolute top-3 right-3 px-2.5 py-1 rounded-full text-xs font-semibold ${badgeClass}`}>
          {product.condition}
        </div>
      </div>

      {/* Content */}
      <div className="p-4">
        {/* Category tag */}
        <div className="flex items-center gap-1.5 mb-2">
          <Icon name={iconName as any} size={13} className="text-primary" />
          <span className="text-xs font-medium text-muted-foreground uppercase tracking-wide">
            {categoryLabel}
          </span>
        </div>

        {/* Title */}
        <h3 className="font-display text-card-foreground text-base leading-snug mb-2 line-clamp-2 group-hover:text-primary transition-colors">
          {product.title}
        </h3>

        {/* Description */}
        <p className="text-sm text-muted-foreground leading-relaxed mb-3 line-clamp-2">
          {product.description}
        </p>

        {/* Footer row */}
        <div className="flex items-center justify-between pt-3 border-t border-border">
          <div className="flex items-center gap-1.5 text-muted-foreground">
            <Icon name="MapPinIcon" size={13} />
            <span className="text-xs font-medium">{product.location}</span>
          </div>
          <span className="text-xs font-semibold text-primary bg-muted px-2.5 py-1 rounded-full">
            Available for Sale
          </span>
        </div>
      </div>
    </Link>
  );
}
