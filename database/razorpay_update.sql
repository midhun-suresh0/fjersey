-- Add Razorpay order ID field to orders table
ALTER TABLE orders
ADD COLUMN razorpay_order_id VARCHAR(255) NULL AFTER payment_id;