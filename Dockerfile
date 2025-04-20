# =========================
#  Dockerfile للمشروع
# =========================
# صورة رسمية فيها Apache + PHP 8.3
FROM php:8.3-apache

# (اختياري) تفعيل mod_rewrite لو تحتاجه
RUN a2enmod rewrite

# ضبط المنطقة الزمنية لكي لا تتفوّت مواعيد البريد
ENV TZ=UTC

# تثبيت Composer لتحميل الاعتماديات
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# مجلد العمل داخل الحاوية
WORKDIR /var/www/html

# نسخ الملفات إلى الحاوية
COPY . .

# إذا كان لديك composer.json نفّذ install
RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader; fi

# فتح المنفذ (Render سيعيد توجيه 80 تلقائياً)
EXPOSE 80

# أمر التشغيل
CMD ["apache2-foreground"]
