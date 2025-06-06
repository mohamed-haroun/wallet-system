# 💼 Laravel Wallet System

This is a modular and scalable **Wallet System** built with Laravel. It features separate guards for Admins and Users, manual permission management (no external packages), real-time dashboard/email notifications, referral-based registration, and API support.

---

## 🚀 Features

### 🛠 Admin Panel

- Built with a **dedicated Admin guard**.
- Manual permission system (no Laravel packages like Spatie).
- Admin-to-admin request handling:
    - Withdrawals: request → approve/reject → update wallet balance accordingly.
    - Top-ups: approve/reject user top-up requests.
- Notifications:
    - Dashboard alerts for all admins.
    - Email alerts for request updates.

### 👤 User API

- Users authenticate via a separate **API guard**.
- Registration via **referral code**:
    - Admins can generate codes.
    - New users registering with a referral code reward both admin and user with **10 EGP**.
    - Users can also generate and share referral codes.
- Top-up Requests:
    - Users submit top-up requests to Admins.
    - Status updates are sent via email notifications.

---

## 🔐 Permissions

Manual implementation of fine-grained permissions:

- `can_accept_withdrawals`
- `can_reject_withdrawals`
- `can_accept_topup`
- `can_reject_topup`

---

## ✉️ Notifications

- **Admins**:

    - Dashboard notifications on new requests.
    - Email alerts for request status changes.

- **Users**:
    - Email alerts on the status of top-up requests.

---

## 🧰 Technical Stack

- Laravel (no permission packages used)
- Laravel API Resources
- Token-based API Authentication (Laravel Sanctum or Passport)
- Laravel Notifications (Mail + Database)
- Modular and clean code structure
- Designed for extensibility and scalability

---

## 📦 Deliverables

- Laravel codebase (admin panel + API)
- Postman collection for API testing
- Database schema (wallets, transactions, permissions, referral codes, etc.)

---

## 📁 Getting Started

1. Clone the repo:
    ```bash
    git clone https://github.com/mohamed-haroun/wallet-system.git
    cd wallet-system
    ```
