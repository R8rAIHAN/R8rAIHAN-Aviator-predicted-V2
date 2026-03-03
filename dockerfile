# PHP 8.2 Apache ইমেজ ব্যবহার করা হচ্ছে
FROM php:8.2-apache

# প্রয়োজনীয় এক্সটেনশন ইন্সটল (ফাইল রাইটিং এবং নেটওয়ার্কিংয়ের জন্য)
RUN docker-php-ext-install opcache

# Apache-এর Rewrite Module অন করা
RUN a2enmod rewrite

# ওয়ার্কিং ডিরেক্টরি সেট করা
WORKDIR /var/www/html

# বর্তমান ফোল্ডারের সব ফাইল কন্টেইনারে কপি করা
COPY . .

# ফাইল রাইটিং পারমিশন সেট করা (যাতে data.json এবং history.json সেভ হতে পারে)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# পোর্ট এক্সপোজ করা
EXPOSE 80

# Apache সার্ভার স্টার্ট করা
CMD ["apache2-foreground"]
