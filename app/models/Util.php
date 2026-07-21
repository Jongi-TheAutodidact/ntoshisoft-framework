<?php

/**
 * Util Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Util
{

	use Model;

	// Set a Flash Message
	public static function setFlash(string $name, string $message): void
	{
		if (!empty($_SESSION[$name])) {
			unset($_SESSION[$name]);
		}
		$_SESSION[$name] = $message;
	}

	/**
	 * Display a Flash Message with beautiful slide animation
	 * @param string $name - Session variable name
	 * @param string $type - Alert type (success, danger, warning, info)
	 * @param int $timeout - Auto-dismiss timeout in milliseconds (default 5000)
	 * @return string - HTML with animations
	 */
	public static function displayFlash(string $name, string $type, int $timeout = 7000): string
	{
		if (isset($_SESSION[$name])) {
			$message = $_SESSION[$name];
			unset($_SESSION[$name]);

			// Map type to icon
			$icons = [
				'success' => 'fa-check-circle',
				'danger' => 'fa-exclamation-circle',
				'warning' => 'fa-exclamation-triangle',
				'info' => 'fa-info-circle'
			];
			$icon = $icons[$type] ?? 'fa-bell';

			$html = '<div class="flash-notification flash-notification-' . $type . '" id="flashMessage">
            <div class="flash-content">
                <i class="fas ' . $icon . '"></i>
                <span>' . htmlspecialchars($message) . '</span>
                <button class="flash-close" onclick="this.parentElement.parentElement.remove();">&times;</button>
            </div>
            <div class="flash-progress"></div>
        </div>';

			$html .= '<style>
            .flash-notification {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                min-width: 300px;
                max-width: 450px;
                background: rgba(15, 25, 45, 0.95);
                backdrop-filter: blur(12px);
                border-radius: 0.75rem;
                border-left: 4px solid;
                box-shadow: 0 10px 25px rgba(0,0,0,0.2);
                animation: slideInRight 0.3s ease-out;
                overflow: hidden;
            }
            
            body.light .flash-notification {
                background: rgba(255, 255, 255, 0.95);
                box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            }
            
            .flash-notification-success { border-left-color: #2dd4bf; }
            .flash-notification-danger { border-left-color: #dc3545; }
            .flash-notification-warning { border-left-color: #ffc107; }
            .flash-notification-info { border-left-color: #38bdf8; }
            
            .flash-content {
                padding: 1rem 1rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }
            
            .flash-content i {
                font-size: 1.25rem;
            }
            
            .flash-notification-success i { color: #2dd4bf; }
            .flash-notification-danger i { color: #dc3545; }
            .flash-notification-warning i { color: #ffc107; }
            .flash-notification-info i { color: #38bdf8; }
            
            .flash-content span {
                flex: 1;
                font-size: 0.9rem;
            }
            
            .flash-close {
                background: none;
                border: none;
                font-size: 1.5rem;
                cursor: pointer;
                opacity: 0.5;
                transition: opacity 0.2s;
                color: inherit;
            }
            
            .flash-close:hover {
                opacity: 1;
            }
            
            .flash-progress {
                height: 3px;
                background: rgba(255,255,255,0.3);
                width: 100%;
                animation: progressShrink ' . ($timeout / 1000) . 's linear forwards;
                transform-origin: left;
            }
            
            body.light .flash-progress {
                background: rgba(0,0,0,0.2);
            }
            
            .flash-notification-success .flash-progress { background: #2dd4bf; }
            .flash-notification-danger .flash-progress { background: #dc3545; }
            .flash-notification-warning .flash-progress { background: #ffc107; }
            .flash-notification-info .flash-progress { background: #38bdf8; }
            
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes progressShrink {
                from { transform: scaleX(1); }
                to { transform: scaleX(0); }
            }
            
            @media (max-width: 768px) {
                .flash-notification {
                    left: 20px;
                    right: 20px;
                    min-width: auto;
                }
            }
        </style>
        <script>
            (function() {
                var flash = document.getElementById("flashMessage");
                if (flash) {
                    // Auto dismiss after timeout
                    setTimeout(function() {
                        if (flash) {
                            flash.style.animation = "slideInRight 0.3s ease-out reverse";
                            setTimeout(function() {
                                if (flash) flash.remove();
                            }, 300);
                        }
                    }, ' . $timeout . ');
                    
                    // Manual close button
                    var closeBtn = flash.querySelector(".flash-close");
                    if (closeBtn) {
                        closeBtn.addEventListener("click", function() {
                            flash.style.animation = "slideInRight 0.3s ease-out reverse";
                            setTimeout(function() { flash.remove(); }, 300);
                        });
                    }
                }
            })();
        </script>';

			return $html;
		}
		return '';
	}

	public static function ntoshiDate(string $date): string
	{
		$date = new DateTime($date);
		$day = $date->format('j');
		$dayWithSuffix = $day . date('S', mktime(0, 0, 0, 1, $day)); // Add ordinal suffix
		$formatted = $dayWithSuffix . ' ' . $date->format('F Y');
		return $formatted;
	}

	public static function displayLegalPages(): void
	{ ?>
		<a href="<?= ROOT . '/popia' ?>">POPIA</a> | <a href="<?= ROOT . '/privacy' ?>">Privacy</a>
<?php
	}

	public static function getSystemModules(): array
	{
		return [
			'auth' 					=> 'Auth',
			'blog' 					=> 'Blog',
			'category' 				=> 'Category',
			'chatmessage' 			=> 'Chatmessage',
			'chatroom' 				=> 'Chatroom',
			'client' 				=> 'Client',
			'comment' 				=> 'Comment',
			'companydetail' 		=> 'Companydetail',
			'deleteditem' 			=> 'Deleteditem',
			'employee' 				=> 'Employee',
			'expenditure' 			=> 'Expenditure',
			'fleetlog' 				=> 'Fleetlog',
			'fleetmodel' 			=> 'Fleetmodel',
			'fleetservicehistory' 	=> 'Fleetservicehistory',
			'formbuilder' 			=> 'Formbuilder',
			'formvalidator' 		=> 'Formvalidator',
			'home' 					=> 'Home',
			'mailer' 				=> 'Mailer',
			'meeting' 				=> 'Meeting',
			'ntoshitable' 			=> 'Ntoshitable',
			'onlineuser' 			=> 'Onlineuser',
			'operatinghour' 		=> 'Operatinghour',
			'passwordreset' 		=> 'Passwordreset',
			'payment' 				=> 'Payment',
			'permission' 			=> 'Permission',
			'permissionseeder' 		=> 'Permissionseeder',
			'post' 					=> 'Post',
			'role' 					=> 'Role',
			'rolepermission' 		=> 'Rolepermission',
			'sociallink' 			=> 'Sociallink',
			'supplier' 				=> 'Supplier',
			'user' 					=> 'User',
			'util' 					=> 'Util',
			'visitor' 				=> 'Visitor',
		];
	}


	/**
	 * Render a view file and return its output as a string
	 */
	public static function render(string $viewPath, array $data = []): string
	{
		$filename = "../app/views/" . $viewPath . ".ntoshi.php";
		$filepath = realpath(dirname(__FILE__, 3)) . '/views/' . $viewPath . '.ntoshi.php';

		// Resolve absolute path to avoid issues with relative paths
		$fullPath = realpath(dirname(__FILE__, 3)) . '/views/' . ltrim($viewPath, '/') . '.ntoshi.php';

		if (!file_exists($fullPath)) {
			throw new Exception("Email template not found: $fullPath");
		}

		extract($data);
		ob_start();
		include $fullPath;
		return ob_get_clean();
	}
}
