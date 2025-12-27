# Authentication & Customer Dashboard Setup Guide

## Overview
Complete user authentication system and customer dashboard for Sakinah Style e-commerce platform.

## Database Migration

**IMPORTANT:** Before using the authentication system, run the SQL migration:

```sql
-- File: database_auth_migration.sql
ALTER TABLE Pelanggan 
ADD COLUMN Password VARCHAR(255) NULL AFTER Email,
ADD COLUMN Role ENUM('customer', 'admin') DEFAULT 'customer' AFTER Password;
```

Run this in phpMyAdmin or MySQL command line before testing authentication.

## Files Created/Modified

### 1. Authentication Pages

#### `pages/register.php`
- **Features:**
  - Registration form (Name, Email, Password, Confirm Password, Phone)
  - Email and Phone uniqueness validation
  - Password hashing using `password_hash($pass, PASSWORD_DEFAULT)`
  - Auto-login after successful registration
  - Auto-generates Customer ID (PLG-YYYY-XXX format)

#### `pages/login.php`
- **Features:**
  - Login form with Email and Password
  - Password verification using `password_verify()`
  - Session storage: `$_SESSION['user'] = ['id', 'name', 'email', 'role', 'phone', 'address']`
  - Redirect support (can redirect after login)
  - "Remember Me" checkbox (placeholder for future cookie implementation)

#### `pages/logout.php`
- **Features:**
  - Destroys session completely
  - Redirects to homepage

### 2. Customer Dashboard

#### `pages/profile.php`
- **Features:**
  - Sidebar menu: Profil Saya, Riwayat Pesanan, Keranjang, Logout
  - Edit Profile section:
    - Update Name, Phone, Address
    - Email is read-only (cannot be changed)
    - Updates `Pelanggan` table
    - Updates session data after save
  - Change Password section:
    - Requires current password verification
    - New password validation (min 6 characters)
    - Password confirmation matching

#### `pages/my-orders.php`
- **Features:**
  - Displays all orders for logged-in customer
  - Order table columns:
    - Date (formatted)
    - Order ID (No_Transaksi)
    - Total Amount
    - Status (with colored badges)
    - "Lihat Detail" button
  - Status Badge Colors:
    - **Pending**: Yellow/Warning badge
    - **Paid**: Blue/Info badge
    - **Sent**: Green/Success badge
    - **Cancelled**: Red/Danger badge
  - Order Detail Modal:
    - Shows order information
    - Lists all items from `Detail_Transaksi`
    - Displays product images, names, colors, quantities, prices
    - Shows total amount

### 3. Updated Files

#### `checkout.php`
- **Guest & Member Support:**
  - Detects if user is logged in
  - Pre-fills form fields for logged-in users
  - Shows info message for logged-in users
  - Shows login prompt for guests
  - Passes `user_id` to `processCheckout()` function (NULL for guests)

#### `functions.php`
- **Updated `processCheckout()` function:**
  - Accepts optional `user_id` parameter
  - Inserts `ID_Pelanggan` into `Transaksi` table (NULL for guests)
  - Works seamlessly for both guest and member checkout

#### `includes/header.php`
- **Updated Navigation:**
  - Shows user dropdown menu when logged in
  - Displays user name in dropdown
  - Quick links to Profile and Order History
  - Logout option
  - Shows Login/Register buttons for guests

## Security Features

1. **Password Security:**
   - Passwords hashed using `password_hash()` with `PASSWORD_DEFAULT`
   - Verification using `password_verify()`
   - Minimum 6 characters required
   - Password confirmation required

2. **Input Validation:**
   - Email format validation
   - Email/Phone uniqueness checks
   - Required field validation
   - SQL injection prevention (PDO prepared statements)

3. **Session Management:**
   - Secure session storage
   - Session destruction on logout
   - Protected routes (redirect to login if not authenticated)

4. **Data Protection:**
   - All user inputs sanitized with `htmlspecialchars()`
   - PDO prepared statements prevent SQL injection
   - Error logging for debugging (not exposed to users)

## User Flow

### Registration Flow:
1. User fills registration form
2. System validates inputs
3. Checks email/phone uniqueness
4. Hashes password
5. Generates Customer ID
6. Inserts into database
7. Auto-login and redirect to profile

### Login Flow:
1. User enters email and password
2. System queries database for user
3. Verifies password with `password_verify()`
4. Creates session with user data
5. Redirects to requested page or profile

### Checkout Flow:
1. **Guest:**
   - Fills checkout form manually
   - Creates transaction with `ID_Pelanggan = NULL`
   - Can register later to track orders

2. **Member:**
   - Form pre-filled with profile data
   - Can edit if needed
   - Creates transaction with `ID_Pelanggan` linked
   - Order appears in Order History

## Testing Checklist

- [ ] Run database migration
- [ ] Test user registration
- [ ] Test email/phone uniqueness validation
- [ ] Test login with correct credentials
- [ ] Test login with incorrect credentials
- [ ] Test logout functionality
- [ ] Test profile update
- [ ] Test password change
- [ ] Test guest checkout
- [ ] Test member checkout (with pre-filled data)
- [ ] Test order history display
- [ ] Test order detail modal
- [ ] Test status badge colors
- [ ] Test session persistence
- [ ] Test protected routes (redirect to login)

## Notes

1. **Email Uniqueness:** Email addresses must be unique across all customers
2. **Phone Uniqueness:** Phone numbers must be unique (optional but validated)
3. **Guest Orders:** Guest orders cannot be tracked in Order History (no account)
4. **Password Reset:** Not implemented yet (can be added later)
5. **Remember Me:** Checkbox exists but cookie functionality not implemented
6. **Role System:** Role field added for future admin panel implementation

## Future Enhancements

- Password reset functionality
- Email verification
- Remember me cookie implementation
- Two-factor authentication
- Order tracking integration
- Wishlist functionality
- Address book (multiple addresses)
- Order cancellation request
- Review/rating system

---

**Security Best Practices Implemented:**
- ✅ Password hashing (bcrypt)
- ✅ Prepared statements (SQL injection prevention)
- ✅ Input sanitization
- ✅ Session security
- ✅ Error logging (not exposed to users)
- ✅ CSRF protection (can be added with tokens)

