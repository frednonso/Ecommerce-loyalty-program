import axios from 'axios';

// Note: Since this is a local environment, we'll use a local URL. 
// Typically this should use an environment variable (import.meta.env.VITE_API_BASE_URL).
const apiClient = axios.create({
  baseURL: 'http://localhost:8000/api', // Point to the Laravel backend
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  // If you add Laravel Sanctum for auth later, you might need this:
  // withCredentials: true, 
});

/**
 * Fetch achievements and badges for a specific user.
 *
 * @param {number|string} userId 
 * @returns {Promise<Object>} Responds with milestones structure from backend
 */
export const getUserAchievements = async (userId) => {
  try {
    const response = await apiClient.get(`/users/${userId}/achievements`);
    return response.data;
  } catch (error) {
    console.error('Error fetching achievements data from backend:', error);
    throw error;
  }
};
