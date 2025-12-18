# fix_structure.ps1
Write-Host "=== FIXING STRUCTURE ===" -ForegroundColor Green
Write-Host "Current directory: $(Get-Location)"

# 1. Cek apakah file ada
if (Test-Path "src\php\config.php") {
    Write-Host "Moving config.php..." -ForegroundColor Yellow
    Move-Item -Path "src\php\config.php" -Destination "config.php" -Force
} else {
    Write-Host "config.php not found in src\php\" -ForegroundColor Red
}

if (Test-Path "src\php\functions.php") {
    Write-Host "Moving functions.php..." -ForegroundColor Yellow
    Move-Item -Path "src\php\functions.php" -Destination "functions.php" -Force
} else {
    Write-Host "functions.php not found in src\php\" -ForegroundColor Red
}

if (Test-Path "style\custom.css") {
    Write-Host "Moving custom.css..." -ForegroundColor Yellow
    # Buat folder assets/css jika belum ada
    if (!(Test-Path "assets\css")) {
        New-Item -ItemType Directory -Path "assets\css" -Force
    }
    Move-Item -Path "style\custom.css" -Destination "assets\css\custom.css" -Force
} else {
    Write-Host "custom.css not found in style\" -ForegroundColor Red
}

# 2. Buat folder yang diperlukan
$folders = @(
    "assets\images",
    "assets\images\products",
    "assets\js",
    "includes",
    "uploads",
    "uploads\products",
    "pages"
)

foreach ($folder in $folders) {
    if (!(Test-Path $folder)) {
        Write-Host "Creating $folder..." -ForegroundColor Cyan
        New-Item -ItemType Directory -Path $folder -Force
    } else {
        Write-Host "$folder already exists" -ForegroundColor Gray
    }
}

# 3. Hapus index.html jika ada
if (Test-Path "index.html") {
    Write-Host "Removing index.html..." -ForegroundColor Yellow
    Remove-Item -Path "index.html" -Force
}

# 4. Tampilkan struktur terbaru
Write-Host "`n=== FINAL STRUCTURE ===" -ForegroundColor Green
Get-ChildItem -Recurse | Format-Table Name, @{Label="Type";Expression={if($_.PSIsContainer){"Folder"}else{"File"}}} -AutoSize

Write-Host "`n=== CHECKLIST ===" -ForegroundColor Green
$check_files = @(
    @{Path="index.php"; Description="Main Page"},
    @{Path="config.php"; Description="Configuration"},
    @{Path="functions.php"; Description="Functions"},
    @{Path="database.sql"; Description="Database Schema"},
    @{Path="assets\css\custom.css"; Description="CSS File"}
)

foreach ($item in $check_files) {
    if (Test-Path $item.Path) {
        Write-Host "✅ $($item.Description) ($($item.Path))" -ForegroundColor Green
    } else {
        Write-Host "❌ $($item.Description) ($($item.Path))" -ForegroundColor Red
    }
}

Write-Host "`n=== DONE ===" -ForegroundColor Green
Write-Host "Access your site at: http://localhost/TubesBasDat/" -ForegroundColor Cyan