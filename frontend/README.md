# E-commerce Loyalty Program - Frontend

This is the frontend application for the E-commerce Loyalty Program, built with React and Vite. It consumes the Laravel backend API to display a user's unlocked achievements, current loyalty badge, and progress toward their next badge.

## Features
- **Responsive Dashboard**: Beautiful, glassmorphism-inspired UI displaying current tier, progress, and achievements.
- **Dynamic Achievements**: Separate lists clearly displaying unlocked achievements and the next available achievements.
- **Badge Progress Tracking**: A visual progress bar detailing how many more actions/purchases are needed to unlock the *next badge*.
- **Graceful Fallback**: If the API is unreachable, the dashboard displays robust mock data for UI testing.

## Prerequisites
- **Node.js** (v18.x or newer recommended)
- **npm** (comes with Node.js)

## Setup Instructions

1. **Navigate to the frontend directory**
   Ensure you are in the `frontend` directory in your terminal:
   ```bash
   cd frontend
   ```

2. **Install Dependencies**
   Run the following command to download all required packages (including React, Axios for API calls, and Lucide React for icons):
   ```bash
   npm install
   ```

3. **Configure the API Endpoint (Optional but Recommended)**
   The application is currently configured to point to the local Laravel backend at `http://localhost:8000/api`. 
   If your Laravel app runs on a different port, open `src/api/achievements.js` and update the `baseURL` property.

## Running the Application

1. **Start the Development Server**
   Start the Vite development server by running:
   ```bash
   npm run dev
   ```

2. **Open Your Browser**
   The terminal will output a local URL (typically `http://localhost:5173`). Open this URL in your web browser. 
   You should immediately see the Loyalty Dashboard!

## Available Scripts

- `npm run dev`: Starts the development server.
- `npm run build`: Bundles the app into static files for production.
- `npm run preview`: Boot up a local web server to preview your production build.

## API Payload Structure
The components are built to seamlessly integrate with the `GET /api/users/{user}/achievements` endpoint requiring the specific payload shape:
```json
{
  "unlocked_achievements": ["First Purchase", "Bronze Tier"],
  "next_available_achievements": ["Silver Tier", "Gold Tier"],
  "current_badge": "Bronze",
  "next_badge": "Silver",
  "remaining_to_unlock_next_badge": 2
}
```
