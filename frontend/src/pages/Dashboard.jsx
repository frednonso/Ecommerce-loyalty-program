import React, { useEffect, useState } from 'react';
import { getUserAchievements } from '../api/achievements';
import AchievementCard from '../components/AchievementCard';
import BadgeDisplay from '../components/BadgeDisplay';
import NextBadgeSection from '../components/NextBadgeSection';
import { Sparkles, Target } from 'lucide-react';
import '../styles/dashboard.css';

const Dashboard = () => {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // Hardcoded user ID for the assignment showcase (e.g., User 1)
  const USER_ID = 1;

  useEffect(() => {
    const fetchData = async () => {
      try {
        setLoading(true);
        const result = await getUserAchievements(USER_ID);
        setData(result);
      } catch (err) {
        console.error("Dashboard fetch error:", err);
        setError("Could not connect to the backend API. Showing mock data for preview.");

        // Exact payload shape from the Assessment scenario
        setData({
          unlocked_achievements: ["First Purchase", "3 Purchases", "Bronze Tier"],
          next_available_achievements: ["Silver Tier", "Gold Tier"],
          current_badge: "Bronze",
          next_badge: "Silver",
          remaining_to_unlock_next_badge: 2
        });
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, []);

  if (loading) {
    return (
      <div className="dashboard-container" style={{ alignItems: 'center', justifyContent: 'center', minHeight: '80vh' }}>
        <div style={{ animation: 'pulse 1.5s infinite', color: 'var(--color-accent-gold)' }}>
          <Sparkles size={48} />
        </div>
        <h2 style={{ marginTop: '1rem', color: 'var(--color-text-secondary)' }}>Loading Loyalty Profile...</h2>
      </div>
    );
  }

  const unlocked = data?.unlocked_achievements || [];
  const nextAvailable = data?.next_available_achievements || [];

  return (
    <div className="dashboard-container">
      {error && (
        <div style={{ backgroundColor: 'rgba(239, 68, 68, 0.1)', border: '1px solid #ef4444', padding: '1rem', borderRadius: '8px', color: '#fca5a5' }}>
          <strong>Note:</strong> {error}
        </div>
      )}

      <header className="dashboard-header">
        <h1 className="dashboard-title">Loyalty Rewards</h1>
        <p className="dashboard-subtitle">Track your progress, unlock achievements, and earn rewards!</p>
      </header>

      <div className="dashboard-grid">
        {/* Sidebar overview pane */}
        <aside className="profile-overview">
          <BadgeDisplay currentBadge={data?.current_badge} />

          <NextBadgeSection
            nextBadge={data?.next_badge}
            remaining={data?.remaining_to_unlock_next_badge}
          />
        </aside>

        {/* Main Content Pane */}
        <main className="achievements-section glass-panel">
          <h2 className="section-title">
            <Sparkles size={24} color="var(--color-accent-gold)" />
            Unlocked Achievements
          </h2>

          <div className="achievements-grid" style={{ marginBottom: '3rem' }}>
            {unlocked.map((achievementString, index) => (
              <div
                key={`unlocked-${index}`}
                style={{
                  animationDelay: `${index * 0.1}s`,
                  animation: 'fadeInDown 0.5s ease-out forwards',
                  opacity: 0,
                  transform: 'translateY(-10px)'
                }}
              >
                <AchievementCard
                  achievement={achievementString}
                  isUnlocked={true}
                  delayIndex={index}
                />
              </div>
            ))}
            {unlocked.length === 0 && (
              <div style={{ gridColumn: '1 / -1', color: 'var(--color-text-secondary)' }}>
                No achievements unlocked yet.
              </div>
            )}
          </div>

          <h2 className="section-title">
            <Target size={24} color="var(--color-text-secondary)" />
            Next Available
          </h2>

          <div className="achievements-grid">
            {nextAvailable.map((achievementString, index) => (
              <div
                key={`locked-${index}`}
                style={{
                  animationDelay: `${(unlocked.length + index) * 0.1}s`,
                  animation: 'fadeInDown 0.5s ease-out forwards',
                  opacity: 0,
                  transform: 'translateY(-10px)'
                }}
              >
                <AchievementCard
                  achievement={achievementString}
                  isUnlocked={false}
                  delayIndex={unlocked.length + index}
                />
              </div>
            ))}
            {nextAvailable.length === 0 && (
              <div style={{ gridColumn: '1 / -1', color: 'var(--color-text-secondary)' }}>
                You've unlocked everything! Amazing!
              </div>
            )}
          </div>
        </main>
      </div>
    </div>
  );
};

export default Dashboard;
