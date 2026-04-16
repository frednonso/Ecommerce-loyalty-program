import React from 'react';
import { Award, Shield, Star, Crown } from 'lucide-react';

const BadgeDisplay = ({ badgeName }) => {

  // A helper to determine icon based on badge string.
  const getIcon = (name) => {
    if (!name) return <Award size={48} />;

    const lowerName = name.toLowerCase();
    if (lowerName.includes('bronze')) return <Shield size={48} color="#cd7f32" />;
    if (lowerName.includes('silver')) return <Star size={48} color="#c0c0c0" />;
    if (lowerName.includes('gold')) return <Crown size={48} color="#ffd700" />;
    if (lowerName.includes('platinum') || lowerName.includes('diamond')) return <Crown size={48} color="#e5e4e2" />;

    return <Award size={48} color="var(--color-accent-gold)" />;
  };

  return (
    <div className="badge-display glass-panel">
      <div className="badge-label">Current Tier</div>

      {badgeName ? (
        <div className="badge-icon-wrapper">
          {getIcon(badgeName)}
        </div>
      ) : (
        <div className="badge-icon-empty">
          <Award size={40} color="var(--color-text-secondary)" />
        </div>
      )}

      <div className="badge-name">
        {badgeName || 'No Badge Yet'}
      </div>
    </div>
  );
};

export default BadgeDisplay;
