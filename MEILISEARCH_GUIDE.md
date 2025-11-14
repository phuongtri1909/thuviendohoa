# 🔍 Hướng dẫn cài đặt và sử dụng Meilisearch

## 📋 Mục lục
1. [Cài đặt trên Laragon (Local - Windows)](#1-cài-đặt-trên-laragon-local---windows)
2. [Cài đặt trên aaPanel (Production - Linux)](#2-cài-đặt-trên-aapanel-production---linux)
3. [Cấu hình Laravel](#3-cấu-hình-laravel)
4. [Index dữ liệu](#4-index-dữ-liệu)
5. [Testing](#5-testing)
6. [Troubleshooting](#6-troubleshooting)
7. [Maintenance](#7-maintenance)

---

## 1. Cài đặt trên Laragon (Local - Windows)

### Bước 1: Download Meilisearch
```bash
# Truy cập: https://github.com/meilisearch/meilisearch/releases/latest
# Download file: meilisearch-windows-amd64.exe
```

### Bước 2: Đặt file vào Laragon
```bash
# 1. Đổi tên file thành meilisearch.exe
# 2. Copy vào C:\laragon\bin\meilisearch\ (tạo folder mới nếu chưa có)
# Hoặc bỏ vào C:\laragon\bin\ luôn
```

### Bước 3: Chạy Meilisearch
```bash
# Mở terminal và chạy:
meilisearch --http-addr 127.0.0.1:7700 --env development --no-analytics

# Hoặc tạo file start_meilisearch.bat:
@echo off
cd /d C:\laragon\bin\meilisearch
start meilisearch.exe --http-addr 127.0.0.1:7700 --env development --no-analytics
```

### Bước 4: Kiểm tra
```bash
# Mở browser: http://127.0.0.1:7700
# Hoặc dùng curl:
curl http://127.0.0.1:7700/health
# Kết quả: {"status":"available"}
```

---

## 2. Cài đặt trên aaPanel (Production - Linux)

### Bước 1: SSH vào server
```bash
ssh root@your-server-ip
```

### Bước 2: Download và cài đặt Meilisearch
```bash
# Download Meilisearch binary
curl -L https://install.meilisearch.com | sh

# Di chuyển vào /usr/local/bin
sudo mv meilisearch /usr/local/bin/
sudo chmod +x /usr/local/bin/meilisearch
```

### Bước 3: Generate Master Key
```bash
# Tạo master key ngẫu nhiên (32 ký tự)
openssl rand -base64 32
# Hoặc:
cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 32 | head -n 1

# Lưu lại key này để dùng trong .env
```

### Bước 4: Tạo systemd service
```bash
sudo nano /etc/systemd/system/meilisearch.service
```

Nội dung file:
```ini
[Unit]
Description=Meilisearch
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=/var/lib/meilisearch
ExecStart=/usr/local/bin/meilisearch --http-addr 127.0.0.1:7700 --env production --master-key YOUR_MASTER_KEY_HERE --db-path /var/lib/meilisearch/data
Restart=on-failure
RestartSec=10
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
```

**Lưu ý quan trọng:**
- Thay `YOUR_MASTER_KEY_HERE` bằng master key đã tạo ở bước 3
- `WorkingDirectory` phải là `/var/lib/meilisearch` (không phải thư mục web)
- `--db-path` phải là thư mục có quyền ghi cho user `www-data`

### Bước 5: Tạo thư mục data và set quyền
```bash
# Tạo thư mục
sudo mkdir -p /var/lib/meilisearch/data

# Set quyền cho www-data
sudo chown -R www-data:www-data /var/lib/meilisearch
sudo chmod -R 755 /var/lib/meilisearch

# Kiểm tra quyền
ls -la /var/lib/meilisearch/
```

**Lưu ý:** Đảm bảo user `www-data` có quyền ghi vào thư mục này.

### Bước 6: Enable và start service
```bash
sudo systemctl daemon-reload
sudo systemctl enable meilisearch
sudo systemctl start meilisearch

# Kiểm tra status
sudo systemctl status meilisearch
```

### Bước 7: Kiểm tra Meilisearch
```bash
curl http://127.0.0.1:7700/health
# Kết quả: {"status":"available"}
```

---

## 3. Cấu hình Laravel

### Bước 1: Cập nhật file .env

**Local (Laragon):**
```env
SCOUT_DRIVER=meilisearch
SCOUT_QUEUE=false
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=
```

**Production (aaPanel):**
```env
SCOUT_DRIVER=meilisearch
SCOUT_QUEUE=true
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=your_master_key_here
```

**Lưu ý**: Thay `your_master_key_here` bằng master key đã tạo ở bước 3 phần aaPanel.

### Bước 2: Kiểm tra config
File `config/scout.php` đã được tạo sẵn với cấu hình Meilisearch.

---

## 4. Index dữ liệu

### Cách 1: Sử dụng command tùy chỉnh
```bash
# Index tất cả models
php artisan meilisearch:index all

# Hoặc index riêng lẻ
php artisan meilisearch:index sets
php artisan meilisearch:index blogs
```

### Cách 2: Sử dụng Scout commands
```bash
# Import Sets
php artisan scout:import "App\Models\Set"

# Import Blogs
php artisan scout:import "App\Models\Blog"

# Sync index settings từ config/scout.php
php artisan scout:sync-index-settings
```

### Cách 3: Flush và re-index (nếu cần)
```bash
# Xóa index cũ
php artisan scout:flush "App\Models\Set"
php artisan scout:flush "App\Models\Blog"

# Import lại
php artisan scout:import "App\Models\Set"
php artisan scout:import "App\Models\Blog"
```

---

## 5. Testing

### Test với Tinker:
```bash
php artisan tinker

# Search Sets
App\Models\Set::search('backdrop')->get();
App\Models\Set::search('tết')->take(10)->get();

# Search với filter
App\Models\Set::search('backdrop')->where('type', 'premium')->get();

# Search Blogs
App\Models\Blog::search('thiết kế')->get();
```

### Test trên browser:
- **Search Sets**: `http://thuviendohoa.local/search?q=backdrop`
- **Search Blogs**: `http://thuviendohoa.local/blog?q=thiết kế`
- **Meilisearch Dashboard (Local)**: `http://127.0.0.1:7700`

### Kiểm tra index stats:
```bash
# Local
curl http://127.0.0.1:7700/indexes/sets/stats
curl http://127.0.0.1:7700/indexes/blogs/stats

# Production (SSH)
curl http://127.0.0.1:7700/indexes/sets/stats
```

---

## 6. Troubleshooting

### ❌ Meilisearch không start

**Local (Laragon):**
- Kiểm tra port 7700 đã bị chiếm chưa: `netstat -ano | findstr :7700`
- Kiểm tra file meilisearch.exe có đúng không
- Thử chạy trực tiếp: `meilisearch --http-addr 127.0.0.1:7700`

**Production (aaPanel):**
```bash
# Xem logs chi tiết
sudo journalctl -u meilisearch -n 50 --no-pager

# Kiểm tra quyền thư mục
ls -la /var/lib/meilisearch/
sudo chown -R www-data:www-data /var/lib/meilisearch
sudo chmod -R 755 /var/lib/meilisearch

# Kiểm tra file service
sudo cat /etc/systemd/system/meilisearch.service

# Kiểm tra port
sudo netstat -tulpn | grep 7700

# Reload và restart
sudo systemctl daemon-reload
sudo systemctl restart meilisearch
sudo systemctl status meilisearch
```

**Lỗi thường gặp:**
- **Permission denied**: Fix quyền thư mục `/var/lib/meilisearch/data`
- **WorkingDirectory sai**: Đổi thành `/var/lib/meilisearch` trong service file
- **Port đã bị chiếm**: Kill process đang dùng port 7700

### ❌ Connection refused

**Kiểm tra:**
1. Meilisearch đang chạy: `curl http://127.0.0.1:7700/health`
2. `MEILISEARCH_HOST` trong `.env` đúng chưa
3. Firewall không block port 7700

**Fix:**
- Local: Đảm bảo Meilisearch đang chạy
- Production: Kiểm tra service status và logs

### ❌ Search không trả về kết quả

**Kiểm tra:**
1. Index có data chưa:
```bash
php artisan tinker
App\Models\Set::search('*')->count()
```

2. Re-index:
```bash
php artisan scout:flush "App\Models\Set"
php artisan scout:import "App\Models\Set"
```

3. Sync settings:
```bash
php artisan scout:sync-index-settings
```

### ❌ Slow search

**Giải pháp:**
1. Enable queue: `SCOUT_QUEUE=true` trong `.env`
2. Chạy queue worker: `php artisan queue:work`
3. Tăng chunk size trong `config/scout.php`
4. Cache kết quả search thường dùng

### ❌ Queue không chạy (Production)

**Nếu set `SCOUT_QUEUE=true`:**
```bash
# Chạy queue worker
php artisan queue:work

# Hoặc setup supervisor (khuyến nghị cho production)
# Xem: https://laravel.com/docs/queues#supervisor-configuration
```

---

## 7. Maintenance

### Commands hữu ích:

```bash
# Xem status của indexes
php artisan scout:status

# Xóa tất cả indexes
php artisan scout:delete-all-indexes

# Re-index một model
php artisan scout:flush "App\Models\Set"
php artisan scout:import "App\Models\Set"

# Clear cache và re-index
php artisan cache:clear
php artisan scout:import "App\Models\Set"
```

### Monitoring (Production):

```bash
# Check Meilisearch health
curl http://127.0.0.1:7700/health

# Check index stats
curl http://127.0.0.1:7700/indexes/sets/stats
curl http://127.0.0.1:7700/indexes/blogs/stats

# View logs
sudo journalctl -u meilisearch -f

# Check service status
sudo systemctl status meilisearch
```

### Backup (Production):

```bash
# Backup Meilisearch data
sudo tar -czf meilisearch-backup-$(date +%Y%m%d).tar.gz /var/lib/meilisearch/data

# Restore
sudo tar -xzf meilisearch-backup-YYYYMMDD.tar.gz -C /
sudo systemctl restart meilisearch
```

### Auto-sync:

Scout tự động sync khi:
- ✅ Tạo mới Set/Blog
- ✅ Update Set/Blog
- ✅ Xóa Set/Blog

**Nếu set `SCOUT_QUEUE=true`:**
- Cần chạy queue worker: `php artisan queue:work`
- Hoặc setup supervisor cho production

---

## 📊 Tính năng đã cấu hình

### Set Model:
- **Searchable**: name, name_no_accent, keywords, keywords_no_accent
- **Filterable**: type, status, price, category_id, album_id
- **Sortable**: created_at, price, views

### Blog Model:
- **Searchable**: title, subtitle, content
- **Filterable**: category_id, is_featured
- **Sortable**: created_at, views

---

## 🚀 Performance

### Trước (LIKE query):
```sql
SELECT * FROM sets 
WHERE name LIKE '%backdrop%' 
   OR description LIKE '%backdrop%'
-- Thời gian: ~500ms với 10,000 records
```

### Sau (Meilisearch):
```php
Set::search('backdrop')->get();
// Thời gian: ~20ms với 10,000 records
```

**Cải thiện: 10-50x nhanh hơn!**

---

## 📝 Lưu ý quan trọng

1. **Local**: Meilisearch chạy trong terminal, giữ terminal mở
2. **Production**: Setup systemd service để tự động start
3. **Master Key**: Production bắt buộc phải có master key
4. **Queue**: Production nên enable queue để tránh block request
5. **Backup**: Định kỳ backup `/var/lib/meilisearch/data`

---

## ✅ Checklist Deployment

### Local (Laragon):
- [ ] Download và chạy Meilisearch
- [ ] Cập nhật `.env`
- [ ] Index data: `php artisan meilisearch:index all`
- [ ] Test search

### Production (aaPanel):
- [ ] Cài đặt Meilisearch binary
- [ ] Generate master key
- [ ] Tạo systemd service
- [ ] Start service
- [ ] Cập nhật `.env` với master key
- [ ] Index data: `php artisan meilisearch:index all`
- [ ] Sync settings: `php artisan scout:sync-index-settings`
- [ ] Test search
- [ ] Setup queue worker (nếu dùng queue)
- [ ] Setup backup schedule

---

**Tài liệu tham khảo:**
- Meilisearch Docs: https://www.meilisearch.com/docs/
- Laravel Scout Docs: https://laravel.com/docs/scout

