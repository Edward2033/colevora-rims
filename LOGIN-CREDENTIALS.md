# Login Credentials - Colevora RIMS

**Status**: ✅ All Passwords Reset  
**Date**: July 20, 2026  
**Default Password**: `password`

---

## Important Note

All user passwords have been reset to `password` for testing and development purposes.

**⚠️ SECURITY WARNING**: These are default credentials for development only. 
**Change all passwords before deploying to production!**

---

## Administrator Account

**Email**: `edwardcole203@gmail.com`  
**Password**: `password`  
**User Type**: Administrator  
**Access**: Full system access, admin dashboard

**Dashboard**: `/admin/dashboard`

---

## Employee Accounts

### Manager
**Email**: `manager@colevora.com`  
**Password**: `password`  
**User Type**: Employee  
**Role**: Manager  
**Dashboard**: `/employee/manager/dashboard`

### Chef
**Email**: `chef@colevora.com`  
**Password**: `password`  
**User Type**: Employee  
**Role**: Chef  
**Dashboard**: `/employee/chef/dashboard`

### Waiter
**Email**: `waiter@colevora.com`  
**Password**: `password`  
**User Type**: Employee  
**Role**: Waiter  
**Dashboard**: `/employee/waiter/dashboard`

### Cashier
**Email**: `cashier@colevora.com`  
**Password**: `password`  
**User Type**: Employee  
**Role**: Cashier  
**Dashboard**: `/employee/cashier/dashboard`

### Inventory Officer
**Email**: `inventory@colevora.com`  
**Password**: `password`  
**User Type**: Employee  
**Role**: Inventory Officer  
**Dashboard**: `/employee/inventory-officer/dashboard`

### Receptionist
**Email**: `reception@colevora.com`  
**Password**: `password`  
**User Type**: Employee  
**Role**: Receptionist  
**Dashboard**: `/employee/receptionist/dashboard`

---

## Customer Accounts

### Test Customer
**Email**: `customer@colevora.com`  
**Password**: `password`  
**User Type**: Customer  
**Dashboard**: `/customer/dashboard`

### John David
**Email**: `brownmira086@gmail.com`  
**Password**: `password`  
**User Type**: Customer  
**Dashboard**: `/customer/dashboard`

---

## Quick Test Login

To test the login functionality:

1. Navigate to: `http://localhost/colevora-rims/public/login`
2. Use any of the emails above
3. Password: `password`
4. Click "Sign In"
5. You should be redirected to the appropriate dashboard

---

## Login URL

**Development**: `http://localhost/colevora-rims/public/login`

---

## Role-Based Access

### Administrator Access
- ✅ Admin dashboard
- ✅ User management
- ✅ Food & category management
- ✅ Order management
- ✅ Inventory management
- ✅ Reports & analytics
- ✅ Site settings
- ✅ All system features

### Employee Access (Based on Role)
- **Chef**: Kitchen orders, food preparation status
- **Waiter**: Table management, take orders, order status
- **Cashier**: Payment processing, order checkout
- **Inventory Officer**: Stock management, purchases, suppliers
- **Receptionist**: Reservations, customer management
- **Manager**: Overview reports, staff management

### Customer Access
- ✅ Browse menu
- ✅ Place orders
- ✅ View order history
- ✅ Make reservations
- ✅ Profile management
- ✅ Cart management

---

## Password Reset (If Needed)

If you need to reset passwords again, use Laravel Tinker:

```bash
php artisan tinker
```

Then run:
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email', 'edwardcole203@gmail.com')->first();
$user->password = Hash::make('newpassword');
$user->save();
```

Or reset all passwords:
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::all()->each(function($user) {
    $user->password = Hash::make('password');
    $user->save();
});
```

---

## Account Status

All accounts have:
- ✅ Active status (`account_status = 'active'`)
- ✅ Verified email (`email_verified_at` is set)
- ✅ Password hashed with Bcrypt

---

## Login Flow

1. **User enters credentials** → Email + Password
2. **Livewire validates** → Required fields, email format
3. **Laravel authenticates** → Checks email/password against database
4. **Session created** → User logged in
5. **Role-based redirect** → Redirected to appropriate dashboard:
   - Admin → `/admin/dashboard`
   - Employee (Chef) → `/employee/chef/dashboard`
   - Employee (Waiter) → `/employee/waiter/dashboard`
   - Employee (Cashier) → `/employee/cashier/dashboard`
   - Employee (Inventory) → `/employee/inventory-officer/dashboard`
   - Employee (Receptionist) → `/employee/receptionist/dashboard`
   - Customer → `/customer/dashboard`

---

## Security Features

- ✅ **Password Hashing**: Bcrypt with cost factor 12
- ✅ **Rate Limiting**: Max 5 login attempts per minute
- ✅ **Session Regeneration**: New session ID after login
- ✅ **Remember Me**: Optional 30-day persistent login
- ✅ **CSRF Protection**: Automatic via Livewire
- ✅ **Email Verification**: Supported (all test accounts pre-verified)

---

## Troubleshooting

### "These credentials do not match our records"

**Possible Causes**:
1. Wrong email address (check spelling)
2. Wrong password (must be exactly: `password`)
3. Account doesn't exist
4. Password hasn't been reset yet

**Solution**: Use the exact credentials listed above

### Rate Limiting Error

If you see "Too many login attempts":
- Wait 60 seconds
- Try again
- Rate limit resets automatically

### User Not Found

Check the email address is exactly as listed (copy/paste recommended)

---

## Production Deployment

**Before deploying to production**:

1. ✅ Change ALL user passwords
2. ✅ Remove/disable test accounts
3. ✅ Update administrator email
4. ✅ Enable email verification for new users
5. ✅ Configure proper mail settings
6. ✅ Set strong passwords (12+ characters, mixed case, numbers, symbols)
7. ✅ Enable two-factor authentication (if implemented)
8. ✅ Review and tighten rate limiting rules

---

## Database Location

**Database**: `colevora_rims`  
**Host**: `127.0.0.1` (localhost)  
**Port**: `3306`  
**Table**: `users`

---

## Summary

✅ All 9 user accounts have been reset to password: `password`  
✅ All accounts are active and email verified  
✅ Login system is working correctly  
✅ Role-based redirects are configured  
✅ Ready for testing and development  

---

*Last Updated: July 20, 2026*  
*Password Reset: Completed Successfully*
