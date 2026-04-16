import React from 'react';
import { Target } from 'lucide-react';
import ProgressBar from './ProgressBar';

const NextBadgeSection = ({ nextBadge, remainingToUnlock }) => {
  if (!nextBadge) {
    return (
      <div className="next-badge-section glass-panel" style={{ padding: '1.5rem', textAlign: 'center' }}>
        <h3 className="next-badge-name">Max Tier Reached!</h3>
        <p style={{ color: 'var(--color-success)', marginTop: '0.5rem' }}>You are at the maximum loyalty tier.</p>
      </div>
    );
  }

  // Without a total threshold from the backend, we approximate a percentage based on an assumed scale 
  // (e.g. they need 'remainingToUnlock' actions, we can just show a visual representation)
  // Let's assume a standard 5 milestone jump if we don't have total.
  const visualTotal = Math.max(5, remainingToUnlock + 1);
  const currentProgress = Math.max(0, visualTotal - remainingToUnlock);
  const progressPercentage = (currentProgress / visualTotal) * 100;

  return (
    <div className="next-badge-section glass-panel" style={{ padding: '1.5rem' }}>
      <div className="next-badge-header">
        <div className="next-badge-info">
          <p>Next Milestone</p>
          <div className="next-badge-name" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
            <Target size={20} color="var(--color-accent-gold)" />
            {nextBadge}
          </div>
        </div>
        <div className="next-badge-target">
          {remainingToUnlock} <span style={{ fontSize: '1rem', color: 'var(--color-text-secondary)', fontWeight: 400 }}>more to go</span>
        </div>
      </div>

      <div style={{ marginTop: '1rem' }}>
        <ProgressBar percentage={progressPercentage} />
      </div>
    </div>
  );
};

export default NextBadgeSection;
