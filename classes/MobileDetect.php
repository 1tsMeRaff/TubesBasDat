<?php
class MobileDetect {
    public function isMobile() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // Common mobile device strings
        $mobileAgents = [
            'Android', 'iPhone', 'iPad', 'iPod', 
            'BlackBerry', 'Windows Phone', 'Opera Mini', 
            'IEMobile', 'Mobile'
        ];
        
        foreach ($mobileAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                return true;
            }
        }
        
        // Check for mobile-specific headers
        if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || 
            isset($_SERVER['HTTP_PROFILE']) ||
            (isset($_SERVER['HTTP_ACCEPT']) && 
             strpos($_SERVER['HTTP_ACCEPT'], 'text/vnd.wap.wml') !== false)) {
            return true;
        }
        
        // Check screen width via JavaScript (fallback)
        if (isset($_COOKIE['is_mobile'])) {
            return $_COOKIE['is_mobile'] === 'true';
        }
        
        return false;
    }
}