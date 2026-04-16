import React from 'react';
import { CheckCircle2, Lock } from 'lucide-react';

const AchievementCard = ({ title, unlocked }) => {
  const statusClass = unlocked ? 'status-unlocked' : 'status-locked';

  return (
    <div className={`achievement-card ${statusClass}`}>
      <div className="achievement-icon">
        {unlocked ? <CheckCircle2 size={24} /> : <Lock size={24} />}
      </div>

      <div className="achievement-info">
        <h4 className="achievement-title">{title}</h4>
        {!unlocked && (
          <p style={{ fontSize: '0.875rem', color: 'var(--color-text-secondary)' }}>
            Locked
          </p>
        )}
      </div>
    </div>
  );
};

export default AchievementCard;
