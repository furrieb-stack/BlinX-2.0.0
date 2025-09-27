# BlinX 2.0.0 🚀
*Revolutionary Social Platform - Public Test Phase*

![BlinX Banner](https://img.shields.io/badge/BlinX-2.0.0-purple?style=for-the-badge&logo=starship)
![License](https://img.shields.io/badge/License-AGPL--3.0-important?style=for-the-badge)
![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)

> **Next-generation social networking experience** · *Currently in Public Testing*

---

## ✨ What is BlinX?

BlinX is a cutting-edge social media platform built with modern web technologies, offering a seamless and intuitive user experience. Designed for communities, creators, and everyday users who value privacy and customization.

### 🌟 Core Features

| Category | Features |
|----------|----------|
| **Social Core** | Posts, Comments, Likes, User Profiles, Follow System |
| **Communities** | Create/Join Communities, Community Posts, Moderation |
| **Media Rich** | Image/Video Uploads, Embedded Media, File Sharing |
| **Security** | CSRF Protection, IP Validation, Email Verification |
| **Customization** | Multiple Themes, User Badges, Premium Features |

---

## 🏗️ Architecture Overview

BlinX Ecosystem
├── Frontend Layer
│ ├── Responsive Design
│ ├── Real-time Updates
│ └── Progressive Web App
├── Business Logic
│ ├── User Management
│ ├── Content Moderation
│ └── Community System
└── Data Layer
├── MySQL Database
├── File Storage
└── Session Management

---

## 🎯 Key Innovations

### 🔐 Advanced Security
- **IP-based session validation** for enhanced security
- **CSRF token protection** on all forms
- **Email verification system** with 6-digit codes
- **Role-based access control** (User, Moderator, Admin)

### 🎨 Premium Experience
- **Multiple theme system** with custom wallpapers
- **User badge system** (Beta Tester, Supporter, VIP)
- **Employee recognition** with special badges
- **Premium member features**

### 👥 Community Focus
- **Dedicated community spaces** with custom branding
- **Community moderation tools**
- **Member management system**
- **Verified community status**

---

## 🚀 Technical Stack

### Backend
- **PHP 8.0+** - Core application logic
- **MySQL** - Database management
- **Custom MVC Architecture** - Scalable structure

### Frontend
- **Vanilla JavaScript** - Interactive features
- **CSS3 with Variables** - Theme system
- **Font Awesome Icons** - Rich iconography
- **Google Fonts (Inter)** - Modern typography

### Security
- **Prepared Statements** - SQL injection prevention
- **Input Sanitization** - XSS protection
- **Session Management** - Secure authentication

---

## 📊 Project Status

| Component | Status | Version |
|-----------|---------|---------|
| Core Platform | ✅ Stable | 2.0.0 |
| User System | ✅ Complete | 2.0.0 |
| Communities | ✅ Implemented | 2.0.0 |
| Media Handling | ✅ Working | 2.0.0 |
| Mobile Optimization | 🔄 In Progress | 2.1.0 |

---

## 🏆 Featured Highlights

### 🎪 Multi-Role System
```php
User Roles: [
    'standard' => Basic permissions,
    'verified' => Media verified account,
    'moderator' => Content moderation,
    'admin' => Full system access,
    'employee' => Staff recognition
]
