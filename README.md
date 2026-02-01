# UC Nexus - Admin Portal

A complete front-end admin management system for the University of Cebu with features for login, room assignment, AI helper, and schedule management.

## Features

### 1. **Admin Login** ✅
- Login page with authentication
- Default credentials: `admin` / `admin123`
- Session management using localStorage
- No account creation (front-end only)

### 2. **Dashboard** ✅
- Overview statistics (Total Rooms, Scheduled Classes, Pending Tasks, System Status)
- Quick action buttons for easy navigation
- Clean and organized layout

### 3. **AI Helper** ✅
- Chat-based AI assistant
- Quick question templates
- Responses for common questions about:
  - CSV import procedures
  - Room capacity information
  - Schedule export
  - Room management
  - Class scheduling

### 4. **Room Assignment** ✅
- View all available rooms in a table format
- Add new rooms manually
- **CSV Import Feature:**
  - Drag-and-drop CSV file upload
  - CSV preview before importing
  - Batch import of rooms
  - CSV Export functionality
- Edit and delete rooms
- Room status indicators (Available/Occupied)

### 5. **Schedule Overview** ✅
- View all scheduled classes
- Filter schedules (This Week, This Month, All Schedules)
- Add new class schedules
- Edit and delete schedules
- Display by time slots with room and instructor information

## How to Use

### Accessing the System
1. Open `index.html` in your web browser
2. Login with default credentials:
   - Username: `admin`
   - Password: `admin123`

### Importing Rooms from CSV
1. Go to **Room Assignment** tab
2. Click **Import CSV** button
3. Drag and drop your CSV file or click to select
4. Review the preview of your data
5. Click **Import Data** to add all rooms

**CSV Format:**
```
Room Name,Capacity,Floor,Building
Room 101,50,1st Floor,Main Building
Lab A,30,2nd Floor,Science Building
```

### Adding Rooms Manually
1. Click **Add Room** button
2. Fill in Room Name, Capacity, Floor, and Building
3. Click **Add Room** to save

### Managing Schedules
1. Go to **Schedule Overview** tab
2. Click **Add Schedule** to create a new class schedule
3. Fill in all required details (Class Name, Room, Instructor, Date, Time)
4. Use filters to view schedules by week, month, or all

### Using AI Helper
1. Go to **AI Assistant** tab
2. Type your question or click a quick question button
3. Get instant responses about system usage and management

## File Structure

```
UCnexus/
├── index.html          # Main HTML file with all page structure
├── styles.css          # Complete styling with responsive design
├── script.js           # All interactive functionality
├── sample_rooms.csv    # Sample CSV file for testing
└── README.md          # This file
```

## Features Implemented

✅ User Authentication (Login/Logout)
✅ Dashboard with statistics
✅ AI Helper with chat functionality
✅ Room management (Add, Edit, Delete, View)
✅ CSV Import with preview
✅ CSV Export
✅ Schedule management (Add, Edit, Delete, Filter)
✅ Responsive design (Desktop, Tablet, Mobile)
✅ Data persistence using localStorage

## Browser Compatibility

- Chrome/Edge (recommended)
- Firefox
- Safari
- Any modern browser with ES6 support

## Notes

- This is a **front-end only** implementation
- All data is stored in the browser's memory (localStorage)
- Data will be lost if browser cache is cleared
- Ready for backend integration

## Future Enhancements

- Backend API integration for persistent data storage
- User account creation
- Role-based access control (Admin, Instructor, Student)
- Calendar view for schedules
- Email notifications
- Advanced filtering and search
- Room availability real-time updates
- Integration with university systems
