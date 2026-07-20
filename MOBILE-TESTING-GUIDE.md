# Mobile Testing Guide
**Colevora Restaurant Management System**  
**Date:** July 20, 2026

---

## Quick Test URLs

### Admin Dashboard
```
http://localhost/colevora-rims/public/admin/dashboard
```
**Login:** `edwardcole203@gmail.com` / `password`

### Employee Dashboards
```
Chef: http://localhost/colevora-rims/public/employee/chef
Waiter: http://localhost/colevora-rims/public/employee/waiter
Cashier: http://localhost/colevora-rims/public/employee/cashier
Inventory: http://localhost/colevora-rims/public/employee/inventory-officer
Receptionist: http://localhost/colevora-rims/public/employee/receptionist
```

**Login Credentials:**
- Chef: `chef@colevora.com` / `password`
- Waiter: `waiter@colevora.com` / `password`
- Cashier: `cashier@colevora.com` / `password`
- Inventory: `inventory@colevora.com` / `password`
- Receptionist: `receptionist@colevora.com` / `password`

---

## Browser DevTools Testing

### Chrome/Edge
1. Open browser
2. Navigate to: `http://localhost/colevora-rims/public/`
3. Press `F12` to open DevTools
4. Press `Ctrl+Shift+M` to toggle device toolbar
5. Select device from dropdown:
   - iPhone SE (375px)
   - iPhone 12 Pro (390px)
   - iPad (768px)
   - Responsive (custom)

### Test Checklist

#### Sidebar Tests
- [ ] Sidebar hidden by default on mobile
- [ ] Hamburger menu button visible on mobile
- [ ] Clicking hamburger opens sidebar
- [ ] Dark backdrop appears behind sidebar
- [ ] Clicking backdrop closes sidebar
- [ ] Close (X) button works in sidebar
- [ ] Sidebar slides smoothly (300ms transition)

#### Layout Tests
- [ ] No horizontal scrolling (unless tables)
- [ ] Stats cards stack properly (2 cols mobile → 4 desktop)
- [ ] Content readable on 375px width
- [ ] Text not cut off
- [ ] Images scale properly
- [ ] Buttons full-width on mobile

#### Navigation Tests
- [ ] All nav links clickable
- [ ] Active menu items highlighted
- [ ] Submenu expand/collapse works
- [ ] User dropdown works
- [ ] Logout button accessible

#### Table Tests
- [ ] Tables scroll horizontally on mobile
- [ ] Table headers visible
- [ ] Touch scrolling smooth
- [ ] Action buttons accessible

#### Form Tests
- [ ] Input fields large enough for touch (min 44px height)
- [ ] Checkboxes/radios touchable
- [ ] Dropdown menus work
- [ ] Buttons have proper spacing
- [ ] Form validation messages visible

---

## Real Device Testing

### Setup
1. Find your computer's local IP:
   ```cmd
   ipconfig
   ```
   Look for "IPv4 Address" (e.g., 192.168.1.100)

2. On your phone, connect to same WiFi network

3. Open browser and navigate to:
   ```
   http://[YOUR-IP]/colevora-rims/public/
   ```
   Example: `http://192.168.1.100/colevora-rims/public/`

### Test Checklist

#### Touch Interactions
- [ ] Tap hamburger menu
- [ ] Tap navigation links
- [ ] Tap action buttons
- [ ] Tap dropdowns
- [ ] Swipe to scroll tables
- [ ] Pinch to zoom (should be disabled)

#### Visual Tests
- [ ] Colors look correct
- [ ] Fonts readable (min 14px body text)
- [ ] Icons clear
- [ ] Images load
- [ ] Spacing comfortable

#### Performance
- [ ] Page loads within 3 seconds
- [ ] Transitions smooth (60fps)
- [ ] No lag when opening sidebar
- [ ] Live updates work (polling)
- [ ] No memory leaks after 5 minutes

#### Orientation Tests
- [ ] Portrait mode works
- [ ] Landscape mode works
- [ ] Orientation change doesn't break layout
- [ ] Sidebar adapts to orientation

---

## Screen Size Breakpoints

| Device | Width | Expected Behavior |
|--------|-------|-------------------|
| iPhone SE | 375px | Sidebar hidden, hamburger visible, 1 col cards |
| iPhone 12 | 390px | Same as above |
| iPhone 12 Pro Max | 428px | Same as above |
| iPad Mini | 744px | Same as above |
| iPad | 768px | 2 col cards, sidebar still hidden |
| iPad Pro | 1024px | Sidebar always visible, 4 col cards |
| Laptop | 1366px | Full desktop layout |
| Desktop | 1920px | Full desktop layout |

---

## Common Issues to Check

### Layout Issues
- ❌ Content going under sidebar
- ❌ Horizontal scrolling (except tables)
- ❌ Text overflow
- ❌ Buttons too small
- ❌ Images breaking layout

### Interaction Issues
- ❌ Hamburger menu not working
- ❌ Sidebar not closing
- ❌ Backdrop not preventing clicks
- ❌ Dropdowns opening off-screen
- ❌ Forms not submitting

### Performance Issues
- ❌ Slow page load
- ❌ Janky animations
- ❌ Memory leaks
- ❌ Battery drain
- ❌ Data usage excessive

---

## Browser Compatibility

### Required Browsers
- [x] Chrome Mobile (Android)
- [x] Safari iOS
- [x] Firefox Mobile
- [x] Samsung Internet
- [x] Edge Mobile

### Known Limitations
- Safari iOS < 13: No backdrop-filter blur
- Firefox: Custom scrollbar not supported
- Opera Mini: Limited CSS support

---

## Accessibility Testing

### Screen Reader
- [ ] Navigation announced correctly
- [ ] Buttons have labels
- [ ] Form inputs have labels
- [ ] ARIA landmarks present

### Keyboard Navigation
- [ ] Tab order logical
- [ ] All interactive elements reachable
- [ ] Focus visible
- [ ] Escape closes modals

### Color Contrast
- [ ] Text readable (WCAG AA)
- [ ] Icons visible
- [ ] Buttons distinguishable
- [ ] Links identifiable

---

## Performance Metrics

### Target Metrics
- First Contentful Paint: < 1.5s
- Largest Contentful Paint: < 2.5s
- Time to Interactive: < 3.5s
- Cumulative Layout Shift: < 0.1
- First Input Delay: < 100ms

### Tools
- Chrome Lighthouse (F12 → Lighthouse tab)
- WebPageTest.org
- PageSpeed Insights

---

## Debugging Mobile Issues

### Chrome Remote Debugging
1. Enable USB debugging on Android phone
2. Connect phone via USB
3. Chrome → `chrome://inspect`
4. Click "Inspect" on device

### Safari Web Inspector
1. Enable "Web Inspector" on iPhone (Settings → Safari → Advanced)
2. Connect iPhone via USB
3. Safari → Develop → [Your iPhone] → [Page]

### Console Logging
```javascript
console.log('Mobile width:', window.innerWidth);
console.log('Sidebar open:', Alpine.store('sidebarOpen'));
```

---

## Reporting Issues

When reporting a mobile issue, include:

1. **Device**: iPhone 12 Pro, iOS 16.3
2. **Browser**: Safari Mobile 16.3
3. **Screen Width**: 390px
4. **Orientation**: Portrait
5. **Issue**: Sidebar doesn't close when clicking backdrop
6. **Steps to Reproduce**:
   - Navigate to /admin/dashboard
   - Tap hamburger menu
   - Tap backdrop area
   - Sidebar remains open
7. **Screenshot**: (attach image)
8. **Console Errors**: (if any)

---

## Success Criteria

The mobile implementation is successful when:

✅ All features accessible on mobile
✅ No horizontal scrolling
✅ Touch targets ≥44x44px
✅ Text readable without zoom
✅ Forms usable with on-screen keyboard
✅ Performance metrics met
✅ Works on major browsers
✅ Accessible to screen readers
✅ No critical bugs

---

**Last Updated:** July 20, 2026  
**Version:** 1.0
