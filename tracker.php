<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'pwd1');
define('DB_USER', 'newuser');
define('DB_PASS', 'strong_password');

// Create connection
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

// Create tables if they don't exist
$sql = "CREATE TABLE IF NOT EXISTS visitor_stats (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    visit_time DATETIME NOT NULL,
    page_visited VARCHAR(255) NOT NULL,
    country VARCHAR(100),
    country_code VARCHAR(10),
    region VARCHAR(100),
    city VARCHAR(100),
    device_type VARCHAR(50),
    browser VARCHAR(100),
    browser_version VARCHAR(50),
    os VARCHAR(100),
    os_version VARCHAR(50),
    referrer VARCHAR(512),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$pdo->exec($sql);

// Create a table for daily summary (for better performance)
$sql = "CREATE TABLE IF NOT EXISTS visitor_daily_summary (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    summary_date DATE NOT NULL,
    total_visits INT(11) DEFAULT 0,
    unique_visitors INT(11) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_date (summary_date)
)";

$pdo->exec($sql);

// Function to get client IP address
function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Function to get detailed browser information
function getBrowser($user_agent) {
    $browser = "Unknown";
    $version = "";
    
    if (preg_match('/MSIE/i', $user_agent) && !preg_match('/Opera/i', $user_agent)) {
        $browser = 'Internet Explorer';
        $pattern = '/(MSIE|Trident)\/([0-9\.]+)/';
    } elseif (preg_match('/Firefox/i', $user_agent)) {
        $browser = 'Mozilla Firefox';
        $pattern = '/Firefox\/([0-9\.]+)/';
    } elseif (preg_match('/Chrome/i', $user_agent)) {
        $browser = 'Google Chrome';
        $pattern = '/Chrome\/([0-9\.]+)/';
    } elseif (preg_match('/Safari/i', $user_agent)) {
        $browser = 'Apple Safari';
        $pattern = '/Version\/([0-9\.]+)/';
    } elseif (preg_match('/Opera/i', $user_agent)) {
        $browser = 'Opera';
        $pattern = '/Opera\/([0-9\.]+)|OPR\/([0-9\.]+)/';
    } elseif (preg_match('/Netscape/i', $user_agent)) {
        $browser = 'Netscape';
        $pattern = '/Netscape\/([0-9\.]+)/';
    } elseif (preg_match('/Edge/i', $user_agent)) {
        $browser = 'Microsoft Edge';
        $pattern = '/Edge\/([0-9\.]+)/';
    }
    
    if (isset($pattern) && preg_match($pattern, $user_agent, $matches)) {
        $version = !empty($matches[2]) ? $matches[2] : (!empty($matches[1]) ? $matches[1] : '');
    }
    
    return ['name' => $browser, 'version' => $version];
}

// Function to get OS information
function getOS($user_agent) {
    $os = "Unknown";
    $version = "";
    
    if (preg_match('/Windows/i', $user_agent)) {
        $os = 'Windows';
        if (preg_match('/Windows NT 10.0/i', $user_agent)) {
            $version = '10';
        } elseif (preg_match('/Windows NT 6.3/i', $user_agent)) {
            $version = '8.1';
        } elseif (preg_match('/Windows NT 6.2/i', $user_agent)) {
            $version = '8';
        } elseif (preg_match('/Windows NT 6.1/i', $user_agent)) {
            $version = '7';
        } elseif (preg_match('/Windows NT 6.0/i', $user_agent)) {
            $version = 'Vista';
        } elseif (preg_match('/Windows NT 5.1/i', $user_agent)) {
            $version = 'XP';
        } elseif (preg_match('/Windows NT 5.0/i', $user_agent)) {
            $version = '2000';
        }
    } elseif (preg_match('/Macintosh|Mac OS X/i', $user_agent)) {
        $os = 'Mac OS X';
        if (preg_match('/Mac OS X 10_15/i', $user_agent)) {
            $version = 'Catalina';
        } elseif (preg_match('/Mac OS X 10_14/i', $user_agent)) {
            $version = 'Mojave';
        } elseif (preg_match('/Mac OS X 10_13/i', $user_agent)) {
            $version = 'High Sierra';
        }
    } elseif (preg_match('/Linux/i', $user_agent)) {
        $os = 'Linux';
        if (preg_match('/Ubuntu/i', $user_agent)) {
            $version = 'Ubuntu';
        } elseif (preg_match('/Debian/i', $user_agent)) {
            $version = 'Debian';
        } elseif (preg_match('/Fedora/i', $user_agent)) {
            $version = 'Fedora';
        }
    } elseif (preg_match('/Android/i', $user_agent)) {
        $os = 'Android';
        if (preg_match('/Android ([0-9\.]+)/i', $user_agent, $matches)) {
            $version = $matches[1];
        }
    } elseif (preg_match('/iOS|iPhone|iPad|iPod/i', $user_agent)) {
        $os = 'iOS';
        if (preg_match('/OS ([0-9_]+)/i', $user_agent, $matches)) {
            $version = str_replace('_', '.', $matches[1]);
        }
    }
    
    return ['name' => $os, 'version' => $version];
}

// Function to get device type
function getDeviceType($user_agent) {
    if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobile))/i', $user_agent)) {
        return 'Tablet';
    } elseif (preg_match('/Mobile|Android|iPhone|iPad|iPod|Windows Phone|IEMobile|Opera Mini/i', $user_agent)) {
        return 'Mobile';
    } else {
        return 'Desktop';
    }
}

// Function to get location information using ipapi.co
function getLocationInfo($ip) {
    $info = ['country' => 'Unknown', 'country_code' => 'XX', 'region' => 'Unknown', 'city' => 'Unknown'];
    
    // Use ipapi.co API for location data
    try {
        $url = "https://ipapi.co/{$ip}/json/";
        $options = [
            'http' => [
                'method' => 'GET',
                'timeout' => 2,
                'header' => "User-Agent: PHP\r\n"
            ]
        ];
        $context = stream_context_create($options);
        $json = @file_get_contents($url, false, $context);
        
        if ($json) {
            $data = json_decode($json, true);
            if (!empty($data) && !isset($data['error'])) {
                $info['country'] = $data['country_name'] ?? 'Unknown';
                $info['country_code'] = $data['country_code'] ?? 'XX';
                $info['region'] = $data['region'] ?? 'Unknown';
                $info['city'] = $data['city'] ?? 'Unknown';
            }
        }
    } catch (Exception $e) {
        // Fallback if ipapi.co fails
        error_log("IPAPI.co error: " . $e->getMessage());
    }
    
    return $info;
}

// Function to get visitor information - FIXED VERSION
function getVisitorInfo() {
    $info = [];
    
    // IP Address
    $info['ip'] = getClientIP();
    
    // Current time
    $info['time'] = date('Y-m-d H:i:s');
    
    // Page visited - FIXED: Get the correct page URL
    $info['page'] = getCurrentPageUrl();
    
    // Referrer
    $info['referrer'] = $_SERVER['HTTP_REFERER'] ?? '';
    
    // User agent
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    
    // Location using ipapi.co
    $location = getLocationInfo($info['ip']);
    $info['country'] = $location['country'];
    $info['country_code'] = $location['country_code'];
    $info['region'] = $location['region'];
    $info['city'] = $location['city'];
    
    // Device detection
    $info['device'] = getDeviceType($user_agent);
    
    // Browser detection
    $browser = getBrowser($user_agent);
    $info['browser'] = $browser['name'];
    $info['browser_version'] = $browser['version'];
    
    // OS detection
    $os = getOS($user_agent);
    $info['os'] = $os['name'];
    $info['os_version'] = $os['version'];
    
    return $info;
}

// NEW FUNCTION: Get the current page URL correctly
function getCurrentPageUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];
    
    // Remove query string if present
    $uri = strtok($uri, '?');
    
    return $protocol . $host . $uri;
}

// Get and store visitor info if not an admin page
if (strpos($_SERVER['REQUEST_URI'], 'stats.php') === false) {
    $visitorInfo = getVisitorInfo();
    
    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO visitor_stats (ip_address, visit_time, page_visited, country, country_code, region, city, device_type, browser, browser_version, os, os_version, referrer) 
                          VALUES (:ip, :visit_time, :page, :country, :country_code, :region, :city, :device, :browser, :browser_version, :os, :os_version, :referrer)");
    $stmt->execute([
        ':ip' => $visitorInfo['ip'],
        ':visit_time' => $visitorInfo['time'],
        ':page' => $visitorInfo['page'],
        ':country' => $visitorInfo['country'],
        ':country_code' => $visitorInfo['country_code'],
        ':region' => $visitorInfo['region'],
        ':city' => $visitorInfo['city'],
        ':device' => $visitorInfo['device'],
        ':browser' => $visitorInfo['browser'],
        ':browser_version' => $visitorInfo['browser_version'],
        ':os' => $visitorInfo['os'],
        ':os_version' => $visitorInfo['os_version'],
        ':referrer' => $visitorInfo['referrer']
    ]);
    
    // Update daily summary
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("INSERT INTO visitor_daily_summary (summary_date, total_visits, unique_visitors) 
                          VALUES (:date, 1, 1)
                          ON DUPLICATE KEY UPDATE 
                          total_visits = total_visits + 1,
                          updated_at = NOW()");
    $stmt->execute([':date' => $today]);
}

// Function to get statistics
function getStatistics($pdo, $period = '7days') {
    $stats = [];
    
    // Determine date range based on period
    $dateCondition = "";
    $params = [];
    
    switch ($period) {
        case 'today':
            $dateCondition = "WHERE DATE(visit_time) = CURDATE()";
            break;
        case 'yesterday':
            $dateCondition = "WHERE DATE(visit_time) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            break;
        case '7days':
            $dateCondition = "WHERE visit_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            break;
        case '30days':
            $dateCondition = "WHERE visit_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            break;
        case 'month':
            $dateCondition = "WHERE YEAR(visit_time) = YEAR(CURDATE()) AND MONTH(visit_time) = MONTH(CURDATE())";
            break;
    }
    
    // Total visits
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM visitor_stats $dateCondition");
    $stmt->execute($params);
    $stats['total_visits'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Unique visitors
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT ip_address) as unique_visitors FROM visitor_stats $dateCondition");
    $stmt->execute($params);
    $stats['unique_visitors'] = $stmt->fetch(PDO::FETCH_ASSOC)['unique_visitors'];
    
    // Visits by country
    $stmt = $pdo->prepare("SELECT country, country_code, COUNT(*) as count FROM visitor_stats $dateCondition GROUP BY country, country_code ORDER BY count DESC LIMIT 10");
    $stmt->execute($params);
    $stats['by_country'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Visits by device
    $stmt = $pdo->prepare("SELECT device_type, COUNT(*) as count FROM visitor_stats $dateCondition GROUP BY device_type ORDER BY count DESC");
    $stmt->execute($params);
    $stats['by_device'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Visits by browser
    $stmt = $pdo->prepare("SELECT browser, COUNT(*) as count FROM visitor_stats $dateCondition GROUP BY browser ORDER BY count DESC LIMIT 10");
    $stmt->execute($params);
    $stats['by_browser'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Visits by OS
    $stmt = $pdo->prepare("SELECT os, COUNT(*) as count FROM visitor_stats $dateCondition GROUP BY os ORDER BY count DESC LIMIT 10");
    $stmt->execute($params);
    $stats['by_os'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Top pages
    $stmt = $pdo->prepare("SELECT page_visited, COUNT(*) as count FROM visitor_stats $dateCondition GROUP BY page_visited ORDER BY count DESC LIMIT 10");
    $stmt->execute($params);
    $stats['top_pages'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent visits
    $stmt = $pdo->prepare("SELECT * FROM visitor_stats ORDER BY visit_time DESC LIMIT 10");
    $stmt->execute();
    $stats['recent_visits'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Daily visits for chart
    $stmt = $pdo->query("SELECT summary_date, total_visits, unique_visitors FROM visitor_daily_summary ORDER BY summary_date DESC LIMIT 30");
    $dailyData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stats['daily_visits'] = array_reverse($dailyData);
    
    return $stats;
}

// Get statistics for display
$period = $_GET['period'] ?? '7days';
$statistics = getStatistics($pdo, $period);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Visitor Statistics Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --info: #4895ef;
            --warning: #f72585;
            --danger: #e63946;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f7fb;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .period-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .period-btn {
            padding: 8px 16px;
            background-color: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 20px;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .period-btn:hover, .period-btn.active {
            background-color: white;
            color: var(--primary);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            text-align: center;
        }
        
        .stat-card h3 {
            font-size: 16px;
            color: var(--gray);
            margin-bottom: 10px;
        }
        
        .stat-card .value {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary);
        }
        
        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        
        .chart-title {
            font-size: 18px;
            margin-bottom: 15px;
            color: var(--dark);
        }
        
        .stats-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stats-table {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .stats-table h2 {
            font-size: 18px;
            margin-bottom: 15px;
            color: var(--dark);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--light-gray);
        }
        
        table th {
            background-color: var(--light);
            font-weight: 500;
        }
        
        table tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .country-flag {
            width: 20px;
            height: 15px;
            margin-right: 8px;
            vertical-align: middle;
            border: 1px solid #ddd;
        }
        
        .recent-visits {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        
        .recent-visits h2 {
            font-size: 18px;
            margin-bottom: 15px;
            color: var(--dark);
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .badge-primary {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary);
        }
        
        .badge-success {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success);
        }
        
        .badge-warning {
            background-color: rgba(247, 37, 133, 0.1);
            color: var(--warning);
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-section {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Website Visitor Statistics</h1>
            <div class="period-selector">
                <a href="?period=today" class="period-btn <?php echo $period === 'today' ? 'active' : ''; ?>">Today</a>
                <a href="?period=yesterday" class="period-btn <?php echo $period === 'yesterday' ? 'active' : ''; ?>">Yesterday</a>
                <a href="?period=7days" class="period-btn <?php echo $period === '7days' ? 'active' : ''; ?>">Last 7 Days</a>
                <a href="?period=30days" class="period-btn <?php echo $period === '30days' ? 'active' : ''; ?>">Last 30 Days</a>
                <a href="?period=month" class="period-btn <?php echo $period === 'month' ? 'active' : ''; ?>">This Month</a>
            </div>
        </header>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Visits</h3>
                <div class="value"><?php echo number_format($statistics['total_visits']); ?></div>
            </div>
            <div class="stat-card">
                <h3>Unique Visitors</h3>
                <div class="value"><?php echo number_format($statistics['unique_visitors']); ?></div>
            </div>
            <div class="stat-card">
                <h3>Avg. Visits per Day</h3>
                <div class="value"><?php echo number_format($period === 'today' || $period === 'yesterday' ? $statistics['total_visits'] : round($statistics['total_visits'] / ($period === '7days' ? 7 : 30))); ?></div>
            </div>
        </div>
        
        <div class="chart-container">
            <h2 class="chart-title">Visits Over Time</h2>
            <canvas id="visitsChart"></canvas>
        </div>
        
        <div class="stats-section">
            <div class="stats-table">
                <h2>Top Countries</h2>
                <table>
                    <tr>
                        <th>Country</th>
                        <th>Visits</th>
                        <th>Percentage</th>
                    </tr>
                    <?php foreach ($statistics['by_country'] as $country): 
                        $percentage = round(($country['count'] / $statistics['total_visits']) * 100, 1);
                    ?>
                    <tr>
                        <td>
                            <img src="https://flagcdn.com/w40/<?php echo strtolower($country['country_code']); ?>.png" 
                                 srcset="https://flagcdn.com/w80/<?php echo strtolower($country['country_code']); ?>.png 2x" 
                                 class="country-flag" 
                                 alt="<?php echo $country['country']; ?>">
                            <?php echo $country['country']; ?>
                        </td>
                        <td><?php echo $country['count']; ?></td>
                        <td><?php echo $percentage; ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            
            <div class="stats-table">
                <h2>Top Pages</h2>
                <table>
                    <tr>
                        <th>Page</th>
                        <th>Visits</th>
                    </tr>
                    <?php foreach ($statistics['top_pages'] as $page): ?>
                    <tr>
                        <td><?php echo strlen($page['page_visited']) > 40 ? substr($page['page_visited'], 0, 40) . '...' : $page['page_visited']; ?></td>
                        <td><?php echo $page['count']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
        
        <div class="stats-section">
            <div class="stats-table">
                <h2>Devices</h2>
                <table>
                    <tr>
                        <th>Device</th>
                        <th>Visits</th>
                        <th>Percentage</th>
                    </tr>
                    <?php foreach ($statistics['by_device'] as $device): 
                        $percentage = round(($device['count'] / $statistics['total_visits']) * 100, 1);
                    ?>
                    <tr>
                        <td><?php echo $device['device_type']; ?></td>
                        <td><?php echo $device['count']; ?></td>
                        <td><?php echo $percentage; ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            
            <div class="stats-table">
                <h2>Browsers</h2>
                <table>
                    <tr>
                        <th>Browser</th>
                        <th>Visits</th>
                    </tr>
                    <?php foreach ($statistics['by_browser'] as $browser): ?>
                    <tr>
                        <td><?php echo $browser['browser']; ?></td>
                        <td><?php echo $browser['count']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
        
        <div class="stats-section">
            <div class="stats-table">
                <h2>Operating Systems</h2>
                <table>
                    <tr>
                        <th>OS</th>
                        <th>Visits</th>
                        <th>Percentage</th>
                    </tr>
                    <?php foreach ($statistics['by_os'] as $os): 
                        $percentage = round(($os['count'] / $statistics['total_visits']) * 100, 1);
                    ?>
                    <tr>
                        <td><?php echo $os['os']; ?></td>
                        <td><?php echo $os['count']; ?></td>
                        <td><?php echo $percentage; ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
        
        <div class="recent-visits">
            <h2>Recent Visits</h2>
            <table>
                <tr>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Device</th>
                    <th>Browser & OS</th>
                    <th>Page</th>
                </tr>
                <?php foreach ($statistics['recent_visits'] as $visit): ?>
                <tr>
                    <td><?php echo date('M j, H:i', strtotime($visit['visit_time'])); ?></td>
                    <td>
                        <?php if ($visit['country'] !== 'Unknown'): ?>
                        <img src="https://flagcdn.com/w20/<?php echo strtolower($visit['country_code']); ?>.png" 
                             class="country-flag" 
                             alt="<?php echo $visit['country']; ?>">
                        <?php echo $visit['city'] !== 'Unknown' ? $visit['city'] . ', ' : ''; ?>
                        <?php echo $visit['country']; ?>
                        <?php else: ?>
                        Unknown
                        <?php endif; ?>
                    </td>
                    <td><span class="badge badge-primary"><?php echo $visit['device_type']; ?></span></td>
                    <td>
                        <span class="badge badge-success"><?php echo $visit['browser']; ?></span>
                        <span class="badge badge-warning"><?php echo $visit['os']; ?></span>
                    </td>
                    <td><?php echo strlen($visit['page_visited']) > 30 ? substr($visit['page_visited'], 0, 30) . '...' : $visit['page_visited']; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <script>
        // Visits chart
        const ctx = document.getElementById('visitsChart').getContext('2d');
        const visitsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [<?php echo implode(',', array_map(function($item) { return "'" . date('M j', strtotime($item['summary_date'])) . "'"; }, $statistics['daily_visits'])); ?>],
                datasets: [
                    {
                        label: 'Total Visits',
                        data: [<?php echo implode(',', array_map(function($item) { return $item['total_visits']; }, $statistics['daily_visits'])); ?>],
                        borderColor: '#4361ee',
                        backgroundColor: 'rgba(67, 97, 238, 0.1)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Unique Visitors',
                        data: [<?php echo implode(',', array_map(function($item) { return $item['unique_visitors']; }, $statistics['daily_visits'])); ?>],
                        borderColor: '#4cc9f0',
                        backgroundColor: 'rgba(76, 201, 240, 0.1)',
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>