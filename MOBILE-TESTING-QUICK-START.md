# Mobile Testing Quick Start Guide
**Test the admin dashboard mobile fix immediately**

---

## 1. Browser Testing (Fastest)

### Chrome/Edge DevTools
1. Open: `http://localhost/colevora-rims/public/admin/dashboard`
2. Press `F12` (DevTools)
3. Press `Ctrl+Shift+M` (Toggle device toolbar)
4. Select: **iPhone 12 Pro** from dropdown
5. Login: `edwardcole203@gmail.com` / `password`

### What to Check ✅
- [ ] Hamburger menu (☰) visible in top-left
- [ ] Tap hamburger → Sidebar slides in from left
- [ ] Dark backdrop appears behind sidebar
- [ ] Tap backdrop or (×) → Sidebar closes
- [ ] All 4 stat cards stack vertically (2 columns on small phones)
- [ ] Both charts visible and fit width
- [ ] No horizontal page scrolling
- [ ] All text readable
- [ ] Quick actions at bottom (2 columns)

---

## 2. Real Device Testing

### Find Your IP
```cmd
ipconfig
```
Look for **IPv4 Address** (e.g., 192.168.1.100)

### On Phone
1. Connect to same WiFi as computer
2. Open browser
3. Navigate to: `http://[YOUR-IP]/colevora-rims/public/`
4. Login as admin
5. Test navigation

---

## 3. Quick Screen Size Tests

### In DevTools
Test these widths (change in responsive mode):
- **320px** - iPhone SE (smallest)
- **375px** - iPhone 12
- **768px** - iPad
- **1024px** - Desktop (sidebar always visible)

### Expected Behavior

#### Mobile (<1024px)
- Sidebar hidden
- Hamburger menu visible
- Content full-width
- Stats: 1-2 columns
- Charts: 1 column

#### Desktop (≥1024px)
- Sidebar always visible
- No hamburger menu
- Stats: 4 columns
- Charts: 2 columns

---

## 4. Common Issues & Fixes

### Issue: Hamburger not visible
**Solution:** You're on desktop size (≥1024px). Resize to <1024px.

### Issue: Sidebar won't open
**Fix:** Hard refresh with `Ctrl+Shift+R`

### Issue: Content overflowing
**Fix:** 
```bash
php artisan optimize:clear
npm run build
```

### Issue: Charts not showing
**Fix:** Check browser console for JavaScript errors

---

## 5. Feature Checklist

### Navigation ✅
- [ ] Hamburger opens sidebar
- [ ] Backdrop closes sidebar
- [ ] Close button works
- [ ] All nav links accessible
- [ ] Active menu highlighted

### Layout ✅
- [ ] Welcome banner displays
- [ ] 4 stat cards visible
- [ ] Daily sales chart renders
- [ ] Order status chart renders
- [ ] Recent orders list visible
- [ ] Low stock alerts visible
- [ ] 6 quick action buttons visible

### Responsive ✅
- [ ] No horizontal scroll
- [ ] Content stacks vertically
- [ ] Text readable on 375px
- [ ] Touch targets ≥44px
- [ ] Animations smooth

---

## 6. Test URLs

### Admin
```
http://localhost/colevora-rims/public/admin/dashboard
edwardcole203@gmail.com / password
```

### Other Admin Pages
```
Orders:    /admin/orders
Foods:     /admin/foods
Users:     /admin/users
Inventory: /admin/inventory/items
Reports:   /admin/reports
```

All should be mobile-responsive!

---

## 7. Screenshot Locations

Take screenshots at these widths for documentation:
- 320px (iPhone SE)
- 375px (iPhone 12)
- 768px (iPad)
- 1024px (Desktop)

---

## Success = All Green ✅

If all checkboxes above are checked, the mobile fix is working perfectly!

---

**Quick Start Time:** 2 minutes  
**Full Test Time:** 10 minutes  
**Status:** Ready to test now!
