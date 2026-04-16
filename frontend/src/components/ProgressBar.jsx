import React, { useEffect, useState } from 'react';

const ProgressBar = ({ percentage }) => {
  // Use local state to trigger the animation on mount
  const [fillWidth, setFillWidth] = useState(0);

  useEffect(() => {
    // Small delay ensures the animation plays after initial render
    const timer = setTimeout(() => {
      setFillWidth(percentage);
    }, 100);

    return () => clearTimeout(timer);
  }, [percentage]);

  return (
    <div className="progress-track">
      <div
        className="progress-fill"
        style={{ width: `${fillWidth}%` }}
        aria-valuenow={percentage}
        aria-valuemin="0"
        aria-valuemax="100"
        role="progressbar"
      />
    </div>
  );
};

export default ProgressBar;
