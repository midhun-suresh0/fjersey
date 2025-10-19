-- Add Razorpay-related columns to orders table
ALTER TABLE orders
ADD COLUMN payment_id VARCHAR(255) NULL AFTER payment_method,
ADD COLUMN razorpay_order_id VARCHAR(255) NULL AFTER payment_id;