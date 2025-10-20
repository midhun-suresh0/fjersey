-- Add new columns to products table
ALTER TABLE products
ADD COLUMN league VARCHAR(50) NOT NULL AFTER team,
ADD COLUMN season VARCHAR(20) NOT NULL AFTER league,
ADD COLUMN featured TINYINT(1) NOT NULL DEFAULT 0 AFTER image;