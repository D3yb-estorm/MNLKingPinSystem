# KingPin System - Order & Cancel Guide (Latest Version)

## 🎯 Current Features

### Order Management
- ✅ Place orders with customizations (size, color, name, number)
- ✅ View all orders in "My Orders"
- ✅ Track order status (Design Approval → Printing → Completed)
- ✅ Payment proof upload for GCash orders

### Cancel/Refund Request Feature (NEW)
- ✅ **Cancel Order** - Customer can request to cancel with reasons
- ✅ **Refund Request** - Customer can request refund for delivered orders
- ✅ **Auto-Cancel** - Order is immediately cancelled when request is submitted
- ✅ **Order Locked** - No further processing can happen once request is submitted
- ✅ **Success Notification** - Browser alert + toast notification confirms submission

---

## 📝 How to Use: Order & Cancel Flow

### Step 1: Login as Customer
```
Email: alice@test.com (or create new account)
Password: alice123
```

### Step 2: Browse & Add Products to Cart
- Click on a product "View Details"
- Choose customizations:
  - **Size** (S, M, L, XL, XXL)
  - **Color** (Black, White, Blue, Red, etc.)
  - **Name** (Optional - your name on the jersey)
  - **Number** (Optional - back number)
- Click "Add to Cart"

### Step 3: Checkout
- Go to Cart section
- Review total amount
- Select payment method (GCash or Cash on Delivery)
- Click "Proceed to Checkout"

### Step 4: Place Order
- Confirm order details
- Click "Place Order"
- ✅ Order is created and appears in "My Orders"

### Step 5: Cancel/Refund Request (NEW)
- Go to "My Orders"
- Find the order you want to cancel
- Click **"Cancel / Change of Mind"** OR **"Request Refund"**
- Select a reason from dropdown:
  - Cancel: "Change of mind", "No longer needed", "Processing too long", "Other"
  - Refund: "Product damaged", "Wrong item", "Doesn't match order", "Other"
- Click **"Submit Request"**
- ✅ See success popup
- ✅ Order status changes to "CANCELLED"
- ✅ Order is locked - cannot be processed further

---

## 🔧 Technical Implementation

### Key Files Modified

#### 1. **js/app.js** - requestOrderAction() function
```javascript
function requestOrderAction(orderId, requestType, reason) {
    const order = appData.orders.find(item => item.id === orderId);
    
    // Validation
    if (order.requestStatus === 'pending') {
        alert('Order already has a pending request.');
        return;
    }
    
    // Mark order as cancelled immediately
    order.requestType = requestType;          // 'cancel' or 'refund'
    order.requestReason = reason;
    order.requestStatus = 'pending';
    order.status = 'cancelled';               // ← AUTO-CANCEL
    order.cancelledByRequest = true;          // ← LOCK FLAG
    
    saveOrders();
    
    // Send notifications
    addNotification('admin', `⚠️ ${requestLabel} request for order #${orderId}`);
    addNotification('customer', `✓ Your ${requestLabel} request was submitted`);
    
    // Show success to user
    showStatusUpdateToast(`✓ ${requestLabel} request submitted successfully!`);
    alert(`✓ SUCCESS!\n\nOrder #${orderId} has been auto-cancelled and locked.`);
}
```

#### 2. **js/app.js** - showOrderRequestReasons() function
```javascript
function showOrderRequestReasons(orderId, requestType) {
    const reasonContainer = document.getElementById(`orderReason-${orderId}`);
    
    // Create dropdown + button using addEventListener (more reliable)
    reasonContainer.innerHTML = `
        <label>Reason:</label>
        <select id="orderReasonSelect-${orderId}">
            ${reasons.map(r => `<option value="${r}">${r}</option>`)}
        </select>
        <button class="btn btn-small" id="submitRequestBtn-${orderId}">
            Submit Request
        </button>
    `;
    
    // Attach click listener
    document.getElementById(`submitRequestBtn-${orderId}`)
        .addEventListener('click', (e) => {
            e.preventDefault();
            const reason = document.getElementById(`orderReasonSelect-${orderId}`).value;
            requestOrderAction(orderId, requestType, reason);
        });
}
```

#### 3. **js/app.js** - isOrderLockedByRequest() function
```javascript
function isOrderLockedByRequest(order) {
    return order.status === 'cancelled' || 
           order.cancelledByRequest || 
           isCancellationRequestLocked(order);
}
```
This prevents admin from changing the status once a request is submitted.

#### 4. **css/style.css** - Order action buttons
```css
.order-actions {
    display: grid;
    gap: 8px;
    margin-top: 16px;
    padding-top: 14px;
    border-top: 1px solid rgba(212, 175, 55, .35);
}

.order-action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.order-lock-banner {
    margin-top: 10px;
    padding: 8px 10px;
    background: rgba(255, 183, 77, 0.12);
    border: 1px solid rgba(255, 183, 77, 0.5);
    border-radius: 6px;
    color: #ffd180;
    font-size: 0.9em;
}
```

---

## 📊 Order States

```
NEW ORDER
  ↓
DESIGN-APPROVAL (waiting for design review)
  ↓
PRINTING (design approved, being printed)
  ↓
COMPLETED (ready for pickup/delivery)

OR at ANY stage:
  ↓
[CANCEL REQUEST SUBMITTED]
  ↓
ORDER → "CANCELLED" (LOCKED - no changes allowed)
```

---

## 🛡️ Safety Mechanisms

1. **Cannot re-cancel** - Once request submitted, button is hidden
2. **Cannot modify status** - Admin cannot change cancelled order status
3. **Request locked** - Cannot submit another request once one exists
4. **Data persistence** - All request details saved to localStorage
5. **Notification audit trail** - Both admin and customer notified

---

## 📱 Live System Link

**http://localhost/KingPinSystem/index.html**

### Test Credentials:
- **Customer**: alice@test.com / alice123
- **Admin**: admin / admin123

---

## 🔄 Testing Checklist

- [ ] Login as customer
- [ ] View available orders in "My Orders"
- [ ] Click "Cancel / Change of Mind" button
- [ ] Select reason from dropdown
- [ ] See success alert popup
- [ ] Order status changes to "CANCELLED"
- [ ] Admin cannot modify cancelled order
- [ ] Toast notification appears at bottom-right
- [ ] Check notifications for confirmation message

---

## 📞 Support

For issues with the order/cancel feature:
1. Hard refresh browser: **Ctrl+F5** (Windows) or **Cmd+Shift+R** (Mac)
2. Clear browser cache if navbar disappears
3. Check browser console (F12) for JavaScript errors

