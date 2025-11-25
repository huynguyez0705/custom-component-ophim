# SignalTrue Ophim Comments - New Features Documentation

## Features Implemented

### 1. Admin Avatar = Site Favicon ✅

- **Location**: `simple-comments.php` - `render_comment_item()` method (lines 310-311)
- **Description**: Admin comments now automatically display the site's favicon as their avatar
- **How it works**: When rendering a comment, if the user is an admin and a site icon exists, it uses `get_site_icon_url(32)` instead of initials or custom avatar

### 2. Admin Bypasses Cloudflare Turnstile ✅

- **Location**: `simple-comments.php`
  - Frontend: lines 173-180 (shortcode)
  - Backend: lines 524-526 (ajax_submit_comment), lines 585-588 (handle_non_ajax_submit)
- **Description**: Admins don't see the Turnstile widget and skip verification
- **How it works**:
  - Frontend checks `! current_user_can( 'manage_options' )` before showing Turnstile
  - Backend checks admin capability before verifying token

### 3. Toggle Turnstile for Users ✅

- **Location**: `simple-comments.php` - Settings page (lines 650-657)
- **Description**: Dashboard setting to enable/disable Turnstile for regular users
- **Option**: `op_turnstile_for_users` (default: enabled)
- **Usage**: Go to **SignalTrue Comments > Cấu hình** and check/uncheck the option

### 4. Blocked Words Filter ✅

- **Location**: `simple-comments.php`
  - Helper method: lines 296-308 (`filter_blocked_words()`)
  - Settings UI: lines 658-665
  - Applied in: lines 529-530 (ajax), lines 591-592 (non-ajax)
- **Description**: Replace blocked words with `***` in comments
- **Usage**:
  1. Go to **SignalTrue Comments > Cấu hình**
  2. Enter blocked words (one per line) in "Từ khóa bị chặn"
  3. Example: If you block "lon", comment "con lon" becomes "con \*\*\*"

### 5. Admin Can Create Comments from Dashboard ✅

- **Location**: `includes/class-op-dashboard.php`
  - Form UI: lines 164-192
  - Handler: lines 33-61
- **Description**: Admins can create comments directly from the dashboard
- **Features**:
  - Create comment with admin credentials
  - Option to pin the comment immediately
  - Automatically approved
- **Usage**:
  1. Go to **SignalTrue Comments > Quản lý bình luận**
  2. Click on a post
  3. Use the "Tạo bình luận mới (Admin)" form

### 6. Pin/Unpin Comments ✅

- **Location**: `includes/class-op-dashboard.php`
  - Pin/Unpin actions: lines 54-61
  - UI buttons: lines 244-251
  - Status display: lines 231-233
- **Description**: Admins can pin/unpin comments from dashboard
- **Features**:
  - Pin button for unpinned comments
  - Unpin button for pinned comments
  - Pinned status shown with 📌 emoji
- **Usage**: Click "Ghim" or "Bỏ ghim" button next to any comment

### 7. Comment View by Date ✅

- **Location**: `templates/comment-view.php`
- **Description**: Standalone page to view comments filtered by day/month/year
- **Features**:
  - Filter by day, month, year
  - Shows total comment count
  - Displays admin badge and pinned status
  - Links back to original post
- **Usage**:
  1. Create a new page in WordPress
  2. Set template to "Comment View by Date"
  3. Access with URL parameters:
     - `?year=2025` - All comments in 2025
     - `?month=11&year=2025` - All comments in November 2025
     - `?day=24&month=11&year=2025` - All comments on Nov 24, 2025

## Settings Overview

### Dashboard: SignalTrue Comments > Cấu hình

1. **Cloudflare Turnstile Sitekey**: Your Turnstile site key
2. **Cloudflare Turnstile Secret**: Your Turnstile secret key
3. **Bật Turnstile cho người dùng**: ✅ Enable/disable Turnstile for regular users (Admin always bypasses)
4. **Từ khóa bị chặn**: List of words to block (one per line, replaced with \*\*\*)

## Database Changes

### New Options

- `op_turnstile_for_users` - Toggle Turnstile for users (1 = enabled, 0 = disabled)
- `op_blocked_words` - Newline-separated list of blocked words

### New Comment Meta

- `op_pinned` - Value: 1 if comment is pinned
- `op_avatar_id` - Custom avatar seed (existing)

## Summary of Files Modified

1. **simple-comments.php**

   - Added `filter_blocked_words()` helper
   - Updated Turnstile display logic
   - Updated verification to skip admins
   - Added new settings registration
   - Updated settings page UI

2. **includes/class-op-dashboard.php**

   - Added comment creation form
   - Added pin/unpin actions
   - Updated comment list to show pin status
   - Added pin/unpin buttons

3. **templates/comment-view.php** (NEW)
   - Standalone comment view by date template

## Testing Checklist

- [ ] Admin comments show site favicon as avatar
- [ ] Admin doesn't see Turnstile widget
- [ ] Admin can submit comments without Turnstile
- [ ] Toggle Turnstile setting works for regular users
- [ ] Blocked words are replaced with \*\*\*
- [ ] Admin can create comments from dashboard
- [ ] Pin/Unpin buttons work correctly
- [ ] Pinned badge shows on frontend and dashboard
- [ ] Comment view page filters by date correctly
