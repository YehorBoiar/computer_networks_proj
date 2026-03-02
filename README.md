# Project Summary

This project is a dynamic web application with database integration. It includes several pages:

* **index.php** (main page)
* **products.php** (Товари)
* **prodSupply.php** (Постачання товарів)
* **sales.php** (Продаж товарів)
* **suppliers.php** (Постачальники)
* **contacts.php** (Контакти)
* **zap.php** (Запитання)

Users can browse products, view supply and sales data, explore supplier information, and submit inquiries.

# Sales Page (sales.php)

The Sales page allows users to view and filter sales by price range and date.
Sales functionality is implemented using the Stripe API for secure payment processing and transaction handling.

# Admin Page

An admin panel provides content management features, including adding, updating, and deleting records. It also supports user account management with different privilege levels.

# Security

Access to the admin page is protected using `.htaccess` and `.htpasswd` for authentication and access control.
