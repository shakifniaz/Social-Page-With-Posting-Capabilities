# MetroPost - Social Media Platform

A modern, responsive social media platform built with PHP that allows users to create posts, share images, interact with content through likes, and manage their profiles.

## 🚀 Features

### 🔐 Authentication & Security
- User registration and login system
- Secure password hashing with bcrypt
- Session-based authentication
- Input validation and sanitization

### 📝 Post Management
- Create posts with text content
- Upload images with posts (PNG, JPG, GIF, WebP)
- Edit existing posts with image management
- Delete posts with associated images
- Character limit (255) with real-time counter
- Image size validation (up to 5MB)

### ❤️ Social Interactions
- Like/unlike posts with real-time updates
- Like count display
- Visual feedback for user interactions
- Real-time like synchronization across users

### 🎨 User Experience
- Responsive design that works on all devices
- Modern UI with Tailwind CSS
- Interactive forms with validation
- Drag-and-drop image upload
- Loading states and animations
- Toast notifications for user feedback

### 👤 Dashboard
- Personal dashboard showing user's posts
- Post management (edit/delete)
- User information display
- Post creation interface

## 🛠️ Technology Stack

- **Backend**: PHP (Custom MVC Framework)
- **Frontend**: HTML, CSS, JavaScript, Tailwind CSS
- **Database**: MySQL
- **File Upload**: Local file system with image processing
- **Email**: PHPMailer for welcome emails
- **Session Management**: Custom session handler

## 🗄️ Database Schema

### Users Table
- `id` - Primary key
- `name` - User's full name
- `email` - Unique email address
- `password` - Hashed password
- `created_at` - Registration timestamp

### Posts Table
- `id` - Primary key
- `user_id` - Foreign key to users
- `content` - Post content (max 255 chars)
- `image` - Optional image path
- `edited` - Flag for edited posts
- `created_at` - Post creation timestamp

### Post Likes Table
- `id` - Primary key
- `post_id` - Foreign key to posts
- `user_id` - Foreign key to users
- `created_at` - Like timestamp
- Unique constraint on (post_id, user_id)

## 🎯 Usage

### For Users
1. **Register** - Create a new account with email and password
2. **Login** - Access your personalized dashboard
3. **Create Posts** - Share thoughts and upload images
4. **Interact** - Like posts from other users
5. **Manage Content** - Edit or delete your own posts

### For Developers
The application follows MVC architecture:

- **Models**: `app/Models/` - Database interactions
- **Views**: `app/Views/` - Presentation layer
- **Controllers**: `app/Controllers/` - Business logic
- **Core**: `app/Core/` - Framework components

## 🔧 API Endpoints

### Authentication
- `GET/POST /login` - User login
- `GET/POST /register` - User registration
- `GET /logout` - User logout

### Posts
- `GET /posts` - View all posts
- `GET /posts/create` - Create post form
- `POST /posts/create` - Create new post
- `POST /posts/update` - Update existing post
- `POST /posts/delete` - Delete post
- `POST /posts/like` - Toggle like on post

### Dashboard
- `GET /dashboard` - User's personal dashboard
- `GET /` - Redirects to dashboard

## 🎨 Customization

### Styling
- Modify Tailwind CSS classes in views
- Update color scheme by changing orange-* classes
- Customize components in `app/Views/` files

### Features
- Extend Post model for additional functionality
- Add new controllers for additional features
- Modify validation rules in controllers

## 🔒 Security Features

- Password hashing with bcrypt
- SQL injection prevention with prepared statements
- XSS protection with htmlspecialchars
- CSRF protection (recommended to implement)
- File upload validation
- Session security

## 📱 Responsive Design

The application is fully responsive and optimized for:
- Desktop computers
- Tablets
- Mobile devices

## 🐛 Troubleshooting

### Common Issues

1. **File upload errors**
   - Check `uploads/` directory permissions
   - Verify PHP file_uploads setting
   - Check upload_max_filesize in php.ini

2. **Database connection errors**
   - Verify database credentials in .env
   - Check if database exists and user has permissions

3. **Email sending issues**
   - Verify SMTP settings
   - Check Mailtrap or SMTP server status


## 🙏 Acknowledgments

- Tailwind CSS for styling
- PHPMailer for email functionality
- Modern PHP practices and patterns

---

**MetroPost** - Connect, Share, and Engage! 🚇✨
