<?php

class LogViewerController
{
    use Controller;

    public function user_onboarding(): void
    {
        $user = new User();

        // Only admins allowed
        if (!$user->logged_in() || user('user_role') !== 'Admin') {
            redirect('auth/login');
        }

        $logFile = __DIR__ . '/../private/password_log_file.txt';
        $logEntries = [];

        if (file_exists($logFile)) {
            $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            // Skip header + separator
            $lines = array_slice($lines, 2);

            foreach ($lines as $line) {
                $parts = array_map('trim', explode('|', $line));
                if (count($parts) === 4) {
                    $logEntries[] = [
                        'datetime'   => $parts[0],
                        'username'   => $parts[1],
                        'password'   => $parts[2],
                        'created_by' => $parts[3],
                    ];
                }
            }
        }

        $data['page_title']  = 'Password Log Viewer';
        $data['log_entries'] = $logEntries;

        $this->view('admin/logviewer/user-onboarding', $data);
    }

    public function exportCsv(): never
    {
        $user = new User();

        // Only admins allowed
        if (!$user->logged_in() || user('user_role') !== 'Admin') {
            redirect('auth/login');
        }

        $logFile = __DIR__ . '/../private/password_log_file.txt';

        if (!file_exists($logFile)) {
            die('No log file found.');
        }

        // Prepare headers
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="password_log.csv"');

        $output = fopen("php://output", "w");

        // Write CSV header
        fputcsv($output, ["Date and Time", "Username", "Password", "Created By"]);

        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $lines = array_slice($lines, 2); // skip header + separator

        foreach ($lines as $line) {
            $parts = array_map('trim', explode('|', $line));
            if (count($parts) === 4) {
                fputcsv($output, $parts);
            }
        }

        fclose($output);
        exit;
    }

    public function updateLogRecord(string $file, string $oldUsername, string $newUsername, string $newPassword): bool
    {
        if (!file_exists($file)) return false;

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $updated = false;

        foreach ($lines as &$line) {
            list($username, $password) = explode(" | ", $line);

            if ($username === $oldUsername) {
                $line = $newUsername . " | " . $newPassword;
                $updated = true;
            }
        }

        if ($updated) {
            file_put_contents($file, implode(PHP_EOL, $lines) . PHP_EOL);
        }

        return $updated;
    }
}
