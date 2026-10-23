<?php
declare(strict_types=1);
/* Local XAMPP configuration. For production, move secrets to environment variables. */
const SITE_NAME = 'Aashirwad Mart';
const SITE_TAGLINE = 'Your One Stop Shopping Destination';
const BASE_URL = '/AashirwadMart_Combined'; // Change if your deployed URL uses a different path; use '' at domain root.
const CURRENCY = '₹';
const DB_HOST = '127.0.0.1';
const DB_NAME = 'aashirwadmart';
const DB_USER = 'root';
const DB_PASS = ''; // Default XAMPP. Set a strong DB password for production.
const BUSINESS_PHONE = '8329513245';
const BUSINESS_EMAIL = 'admin@ashirwadmart.com';
const BUSINESS_ADDRESS = 'Aashirwad Mart, Main Road, Hindustan Awas';
const DELIVERY_PINCODES = ['431005'];
const UPI_QR_IMAGE = 'QR.jpeg'; // Existing Aashirwad Mart QR copied from the previous website.
const UPI_ID = ''; // Optional: enter the verified merchant UPI ID if you want a UPI deep link.
const UPI_MERCHANT_NAME = 'Aashirwad Mart';
date_default_timezone_set('Asia/Kolkata');
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    session_start();
}
