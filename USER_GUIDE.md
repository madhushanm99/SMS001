## User Guide

### Admin Actions

This section helps Admin users navigate and perform key actions in the system.

- **Login**
  - Go to the login page and sign in with an Admin account.

- **Dashboard Overview**
  - Review key stats, recent activities, and quick links after login.

- **Suppliers Management**
  - Create, edit, and view suppliers.
  - Ensure supplier email is set to receive purchase order notifications.

- **Products/Inventory**
  - Add and update products and related details.
  - Manage stock via GRNs and returns.

- **Purchase Orders (POs)**
  - Create draft POs to suppliers.
  - Update PO status: draft → pending → approved → received → cancelled.
  - When approving, the system will queue an email to the supplier if an email exists.

- **Goods Received Notes (GRNs)**
  - Receive and record incoming goods against POs.
  - Adjust stock levels accordingly.

- **Purchase Returns**
  - Create returns to suppliers for faulty or excess items.
  - Track return status and inventory adjustments.

- **Sales Invoices**
  - Create and edit invoices.
  - Edit/update finalized invoices (Admin and Manager only); others can edit only invoices on "hold".

- **Invoice Returns**
  - Create and manage returns for issued invoices.
  - Admin/Manager have full access to invoice returns.

- **Appointments**
  - View overall appointment schedule and statuses.
  - Confirm/reject/complete appointments as needed.

- **Notifications**
  - View notifications from the bell icon.
  - Filter by read/unread; mark all as read.

- **User Access & Permissions**
  - Admin can access all modules.
  


---

### Manager Actions

This section helps Manager users perform day-to-day operations similar to Admins, with a few scope limits.

- **Login**
  - Sign in with a Manager account.

- **Dashboard Overview**
  - Review KPIs, recent transactions, quick links.

- **Suppliers Management**
  - Create, edit, and view suppliers.
  - Ensure supplier email is set to receive purchase order notifications.

- **Products/Inventory**
  - Add and update products and related details.
  - Manage stock levels via GRNs and returns.

- **Purchase Orders (POs)**
  - Create draft POs to suppliers.
  - Update PO status: draft → pending → approved → received → cancelled.
  - Approving a PO will queue an email to the supplier when an email is available.
  - Note: Managers are authorized to change PO status.

- **Goods Received Notes (GRNs)**
  - Receive and record incoming goods against POs.
  - Verify quantities and prices; stock adjusts accordingly.

- **Purchase Returns**
  - Create returns for faulty/excess items.
  - Track return status and ensure inventory corrections.

- **Sales Invoices**
  - Create and edit invoices.
  - Managers can edit/update finalized invoices; others can edit only invoices on "hold".

- **Invoice Returns**
  - Full access to create and manage returns for issued invoices.

- **Appointments**
  - View schedule and statuses.
  - Confirm/reject/complete appointments as needed.

- **Notifications**
  - View notifications, filter by read/unread, and mark all as read.

- **Access Notes**
  - Managers have broad access similar to Admins for operational modules.
  - Certain system-level settings may remain Admin-only.


### Staff (Default User) Actions

This section helps Staff users understand their allowed actions and limits.

- **Login**
  - Sign in with a standard (staff) user account.

- **Dashboard Overview**
  - View key stats and quick links relevant to daily work.

- **Suppliers / Products / POs / GRNs / Purchase Returns**
  - Access to these modules depends on your assigned permissions.
  - If a screen is restricted, contact an Admin/Manager to adjust your access.

- **Purchase Orders (POs)**
  - Create and edit draft POs if permitted.
  - Cannot change PO status (approval/receive/cancel is Manager/Admin only).

- **Goods Received Notes (GRNs)**
  - Record received goods against POs if permitted.
  - Ensure quantities and pricing match documents.

- **Sales Invoices**
  - Create and edit invoices.
  - Can edit/update only invoices on "hold".
  - Cannot edit finalized invoices (requires Manager/Admin).

- **Invoice Returns**
  - Not permitted for Staff by default.
  - Escalate to Manager/Admin when a return is needed.

- **Appointments**
  - View schedule and assist with status updates as allowed by role.

- **Notifications**
  - View notifications, filter by read/unread, and mark all as read.

### Customer Actions

This section helps Customer users navigate the customer portal and manage their account.

- **Login & Registration**
  - Access the customer portal with your registered email and password.
  - First-time users may need to verify their email via OTP.
  - If using a default password, you'll be prompted to change it immediately.

- **Dashboard Overview**
  - View your account summary, recent invoices, and credit balance.
  - Quick access to vehicles and upcoming appointments.
  - QR code display for vehicles.

- **Appointments Management**
  - **Create Appointment**: Select service type, vehicle, date/time, and add notes.
  - **View Appointments**: See all your appointments with status (pending, confirmed, completed, cancelled).
  - **Filter & Search**: Filter by status, service type, or search by appointment number/vehicle.
  - **Cancel Appointment**: Cancel appointments as needed (subject to business rules).
  - **Status Updates**: Receive real-time updates when staff confirm/reject/complete appointments.

- **Vehicle Management**
  - **View Vehicles**: See all your registered vehicles with brand and route information.
  - **Edit Vehicle Details**: Update vehicle information as needed.
  - **Service Schedule**: Track when your next service is due.
  - **Vehicle Approval**: New vehicles may require staff approval before full access.

- **Invoices & Payments**
  - **View Invoices**: Access your sales and service invoices.
  - **Payment History**: Track payment status and credit balance.
  - **Download Invoices**: Save or print invoices for your records.

- **Service History**
  - **Past Services**: Review completed service appointments and work performed.
  - **Service Records**: Access detailed service history for each vehicle.

- **Account Management**
  - **Profile Updates**: Modify contact information and preferences.
  - **Password Changes**: Update your password securely.
  - **Email Verification**: Ensure your email is verified for important notifications.

- **Notifications**
  - **Appointment Updates**: Receive notifications for status changes.
  - **Service Reminders**: Get notified about upcoming service appointments.
  - **Email Notifications**: Important updates sent to your registered email.

- **Access Notes**
  - Customers use a separate authentication system from staff.
  - Access is limited to your own data (vehicles, appointments, invoices).
  - Cannot access staff-only features or other customer information.
---

## Getting Help

If you encounter issues or need assistance:
- **Staff Users**: Contact your Manager or Admin for permission changes.
- **Managers**: Contact your Admin for system-level access.
- **Customers**: Contact the business directly for account or service issues.
- **All Users**: Ensure your `usertype` is correctly set and permissions are configured.

## System Requirements

- Modern web browser (Chrome, Firefox, Safari, Edge)
- Stable internet connection
- For staff: Queue worker should be running for optimal email/notification delivery

