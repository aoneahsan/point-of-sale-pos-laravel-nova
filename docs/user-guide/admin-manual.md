# Admin Manual - Nova Back Office

## Accessing the Admin Panel

1. Navigate to: `https://your-domain.com/admin`
2. Log in with your admin credentials
3. You'll see the main dashboard

## Dashboard Overview

The dashboard provides real-time insights:
- **Today's Sales**: Total sales value for current day
- **Transactions Today**: Number of sales completed
- **Low Stock Items**: Products below reorder point
- **Top Selling Products**: Best performers
- **Sales Chart**: Visual sales trend

## Managing Products

### Adding a New Product

1. Click "Products" in sidebar
2. Click "Create Product" button
3. Fill in product details:
   - **Name**: Product name (required)
   - **SKU**: Stock keeping unit (auto-generated if blank)
   - **Barcode**: Scan or enter barcode
   - **Description**: Detailed product description
   - **Category**: Select from dropdown
   - **Brand**: Select brand (optional)
   - **Status**: Active/Inactive
4. Add product variants (sizes, colors, etc.)
5. Upload product images
6. Click "Create Product"

### Product Variants

**What are variants?**
Variants are different versions of the same product (e.g., T-shirt in Small, Medium, Large).

**Adding Variants:**
1. When creating/editing a product, scroll to "Variants" section
2. Click "Add Variant"
3. Enter variant details:
   - Name (e.g., "Small", "Red")
   - SKU (unique per variant)
   - Price
   - Cost (for profit calculation)
   - Stock quantity
4. Repeat for each variant
5. Save product

### Bulk Import Products

**Upload products from Excel/CSV:**

1. Click "Products" → "Actions" → "Import Products"
2. Download the template file
3. Fill in your product data
4. Upload completed file
5. Review import preview
6. Confirm import

**Template Columns:**
- name, sku, barcode, category, brand, price, cost, stock, description

### Bulk Update Prices

**Update prices for multiple products at once:**

1. Select products (checkboxes)
2. Click "Actions" → "Update Prices"
3. Choose update method:
   - Increase by percentage (e.g., +10%)
   - Decrease by percentage (e.g., -5%)
   - Set fixed amount
4. Preview changes
5. Confirm update

## Managing Inventory

### Stock Adjustments

**When to use:**
- Physical inventory count doesn't match system
- Damaged/stolen products
- Promotional giveaways

**Creating an Adjustment:**
1. Click "Inventory" → "Stock Adjustments"
2. Click "Create Adjustment"
3. Select store
4. Add products that need adjustment
5. Enter:
   - Current system quantity
   - Actual counted quantity
   - Reason for discrepancy
6. Save as "Pending"
7. Manager reviews and approves

### Purchase Orders

**Ordering stock from suppliers:**

1. Click "Inventory" → "Purchase Orders"
2. Click "Create Purchase Order"
3. Select supplier
4. Add products to order
5. Enter quantity and unit cost
6. Review total
7. Save order (status: "Pending")
8. When received, mark as "Received"
9. Stock automatically updates

### Stock Transfers

**Moving stock between stores:**

1. Click "Inventory" → "Stock Transfers"
2. Select "From Store" and "To Store"
3. Add products and quantities
4. Review transfer
5. Create transfer
6. Stock updates at both stores

### Low Stock Alerts

**System automatically alerts when products are low:**

1. Click "Inventory" → "Low Stock Items"
2. Review list of low stock products
3. Reorder point is set per product
4. Create purchase orders as needed

## Managing Customers

### Adding a Customer

1. Click "Customers" in sidebar
2. Click "Create Customer"
3. Fill in details:
   - Name (required)
   - Email
   - Phone
   - Address
   - Customer Group (Retail, Wholesale, VIP)
4. Initial loyalty points (if any)
5. Store credit balance
6. Click "Create"

### Customer Groups

**Setting up groups with different pricing:**

1. Click "Customers" → "Customer Groups"
2. Click "Create Group"
3. Enter:
   - Group name (e.g., "Wholesale")
   - Discount percentage (e.g., 20%)
4. Save group
5. Assign customers to this group
6. Discounts apply automatically at POS

### Viewing Customer History

1. Click "Customers"
2. Select a customer
3. View tabs:
   - **Profile**: Basic info
   - **Purchase History**: All transactions
   - **Loyalty Points**: Points earned/redeemed
   - **Store Credit**: Credit balance history

### Exporting Customer Data

1. Click "Customers"
2. Click "Actions" → "Export to Excel"
3. Choose fields to include
4. Download file

## Managing Sales & Returns

### Viewing Sales

1. Click "Sales" in sidebar
2. Filter by:
   - Date range
   - Store
   - Cashier
   - Payment method
   - Status
3. Click on sale to view details

### Processing Refunds

**Most refunds are processed at POS, but admins can also:**

1. Click "Returns" → "Create Return"
2. Enter sale reference number
3. Select items being returned
4. Enter return reason
5. Choose refund method:
   - Original payment method
   - Store credit
6. Approve refund
7. Receipt emails to customer

### Voiding Sales

**Only for same-day mistakes:**

1. Find sale in "Sales" list
2. Click "Void" action
3. Enter reason
4. Confirm void
5. Stock returns to inventory

## Discounts & Promotions

### Creating a Discount

**Types of discounts:**
- Percentage off
- Fixed amount off
- Buy X Get Y free
- Bundle pricing

**Steps:**
1. Click "Promotions" → "Discounts"
2. Click "Create Discount"
3. Fill in:
   - Name (e.g., "Summer Sale")
   - Type (percentage, fixed, BOGO)
   - Value (e.g., 15%)
   - Start date and end date
   - Minimum purchase amount (optional)
   - Maximum uses (optional)
   - Applicable products/categories
4. Set status to "Active"
5. Save discount

### Creating Coupon Codes

1. Click "Promotions" → "Coupons"
2. Click "Create Coupon"
3. Enter:
   - Coupon code (e.g., "SAVE20")
   - Linked discount
   - Max uses per customer
   - Expiration date
4. Share code with customers

### Time-Based Promotions

**Example: Happy Hour (3-5 PM daily)**

1. Create discount
2. Set start time: 15:00
3. Set end time: 17:00
4. Set to repeat daily
5. Discount applies automatically during those hours

## Reporting & Analytics

### Daily Sales Report

1. Click "Reports" → "Sales Reports"
2. Select "Daily Summary"
3. Choose date
4. View metrics:
   - Total sales
   - Number of transactions
   - Average transaction value
   - Sales by payment method
   - Sales by product
   - Sales by cashier
5. Export to PDF or Excel

### Inventory Reports

**Current Stock Levels:**
1. Click "Reports" → "Inventory Reports"
2. Select "Stock Levels"
3. Filter by category, brand, or store
4. Export to Excel

**Stock Movement:**
1. Select "Stock Movement"
2. Choose date range
3. View all stock changes (sales, returns, adjustments)
4. Export for analysis

**Inventory Valuation:**
1. Select "Inventory Valuation"
2. View total value of stock
3. Breakdown by category
4. Export to PDF

### Financial Reports

**Revenue Report:**
1. Click "Reports" → "Financial Reports"
2. Select "Revenue Report"
3. Choose date range
4. View:
   - Gross sales
   - Refunds
   - Net sales
   - Tax collected
5. Export to Excel

**Profit Margin Report:**
1. Select "Profit Margins"
2. View:
   - Cost of goods sold
   - Revenue
   - Gross profit
   - Profit margin %
3. Breakdown by product or category

### Tax Reports

**For accounting/tax filing:**

1. Click "Reports" → "Tax Reports"
2. Select period (month, quarter, year)
3. View:
   - Total sales subject to tax
   - Tax collected by rate
   - Exempt sales
4. Export to Excel for accountant

## User Management

### Adding Staff Users

1. Click "Users" in sidebar
2. Click "Create User"
3. Fill in:
   - Name
   - Email
   - Password
   - Role (Super Admin, Manager, Cashier, etc.)
   - Assign to store
4. Set permissions if needed
5. Click "Create"
6. User receives email with login details

### User Roles

**Super Admin:**
- Full system access
- Manage all stores
- System configuration

**Store Manager:**
- Manage assigned store
- View reports
- Approve refunds
- Manage staff

**Cashier:**
- POS interface only
- Process sales and returns
- Cannot change prices or settings

**Inventory Manager:**
- Manage products
- Stock adjustments
- Purchase orders
- Cannot process sales

**Accountant:**
- Read-only access to reports
- Cannot make changes

### Deactivating a User

1. Find user in "Users" list
2. Click edit
3. Set status to "Inactive"
4. User can no longer log in
5. Historical data preserved

## Store Settings

### Store Information

1. Click "Settings" → "Store Settings"
2. Update:
   - Store name
   - Address
   - Phone
   - Email
   - Tax ID number
3. Upload logo
4. Save changes

### Tax Configuration

**Setting up tax rates:**

1. Click "Settings" → "Tax Rates"
2. Click "Create Tax Rate"
3. Enter:
   - Name (e.g., "State Sales Tax")
   - Rate (e.g., 8.5%)
   - Active status
4. Save
5. Tax applies to all sales automatically

**Multiple tax rates:**
- Create separate rate for each tax
- All applicable taxes add up at checkout

### Payment Methods

**Configuring payment options:**

1. Click "Settings" → "Payment Methods"
2. Enable/disable methods:
   - Cash
   - Credit Card (Stripe)
   - Debit Card
   - Store Credit
3. For Stripe:
   - Enter API keys in .env file
   - Test in sandbox mode first
   - Switch to live keys for production

### Receipt Customization

1. Click "Settings" → "Receipt Settings"
2. Upload logo
3. Edit footer text
4. Preview receipt
5. Save changes

## System Maintenance

### Database Backup

**Automated backups run daily**

**Manual backup:**
1. Click "Settings" → "Maintenance"
2. Click "Backup Now"
3. Backup downloads as SQL file
4. Store securely offsite

### Activity Logs

**View system activity:**

1. Click "Settings" → "Activity Logs"
2. Filter by:
   - User
   - Action type
   - Date range
3. View detailed activity
4. Export for auditing

### Cache Management

**If system seems slow:**

1. Click "Settings" → "Maintenance"
2. Click "Clear All Caches"
3. System clears:
   - Application cache
   - Config cache
   - Route cache
   - View cache
4. Performance improves

## Best Practices

### Daily Tasks
- ✅ Review sales dashboard
- ✅ Check for low stock items
- ✅ Verify cash drawer reconciliations
- ✅ Review any refunds/voids

### Weekly Tasks
- ✅ Review sales reports
- ✅ Check inventory levels
- ✅ Process purchase orders
- ✅ Review customer feedback
- ✅ Check system logs for errors

### Monthly Tasks
- ✅ Generate financial reports
- ✅ Review profit margins
- ✅ Audit user access
- ✅ Clean up old data
- ✅ Review and update pricing
- ✅ Tax report for accountant

### Data Security
- 🔒 Use strong passwords
- 🔒 Enable two-factor authentication
- 🔒 Limit user permissions
- 🔒 Regular backups
- 🔒 Keep software updated
- 🔒 Review access logs

## Troubleshooting

### Products Not Showing in POS
- Check product is "Active"
- Check product has stock
- Clear cache

### Reports Not Generating
- Check date range is valid
- Try smaller date range
- Contact support if persists

### Import Failed
- Check CSV format matches template
- Check for special characters
- Verify all required fields filled

### Can't Delete Product
- Product may have transaction history
- Set to "Inactive" instead
- Contact support if needed

## Getting Help

### Support Resources
- **Documentation**: https://your-domain.com/docs
- **Email Support**: admin-support@yourpos.com
- **Phone Support**: 1-800-POS-HELP
- **Live Chat**: Available in admin panel

### Training
- **New User Training**: First Monday of each month
- **Advanced Features**: Quarterly webinars
- **Video Tutorials**: Help menu → Video Library

---

**For technical issues, contact IT support immediately.**

**Thank you for managing your POS system efficiently!** 🚀
