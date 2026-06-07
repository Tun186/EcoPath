<?php

class Auth extends Controller {
    private $userModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->userModel = $this->model('User');
    }

    public function login() {
        $data = [
            'title' => 'Login to EcoPath',
            'email' => '',
            'password' => '',
            'email_err' => '',
            'password_err' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data['email'] = trim($_POST['email']);
            $data['password'] = trim($_POST['password']);

            if (empty($data['email'])) $data['email_err'] = 'Please enter email';
            if (empty($data['password'])) $data['password_err'] = 'Please enter password';

            if (empty($data['email_err']) && empty($data['password_err'])) {
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);

                if ($loggedInUser) {
                    $_SESSION['user_id'] = $loggedInUser->UserID;
                    $_SESSION['username'] = $loggedInUser->Username;
                    $_SESSION['role_id'] = $loggedInUser->RoleID;
                    $this->redirectUserByRole($loggedInUser->RoleID);
                } else {
                    $data['password_err'] = 'Password incorrect or user not found';
                }
            }
        }

        $this->view('auth/login', $data);
    }

    public function register() {
        $data = [
            'title' => 'Join EcoPath',
            'username' => '',
            'email' => '',
            'phone' => '',
            'password' => '',
            'confirm_password' => '',
            'username_err' => '',
            'email_err' => '',
            'password_err' => '',
            'confirm_password_err' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data['username'] = trim($_POST['username']);
            $data['email'] = trim($_POST['email']);
            $data['phone'] = trim($_POST['phone'] ?? '');
            $data['password'] = trim($_POST['password']);
            $data['confirm_password'] = trim($_POST['confirm_password']);

            if (empty($data['username'])) $data['username_err'] = 'Please enter username';
            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            } else {
                if ($this->userModel->findUserByEmail($data['email'])) {
                    $data['email_err'] = 'Email is already taken';
                }
            }
            if (empty($data['password'])) $data['password_err'] = 'Please enter password';
            if ($data['password'] != $data['confirm_password']) $data['confirm_password_err'] = 'Passwords do not match';

            if (empty($data['username_err']) && empty($data['email_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                if ($this->userModel->register($data)) {
                    header('Location: ' . URLROOT . '/auth/login?success=registered');
                    exit;
                } else {
                    die('Something went wrong');
                }
            }
        }

        $this->view('auth/register', $data);
    }

    // Google OAuth Flow
    public function google() {
        $redirectUri = URLROOT . '/auth/google_callback';
        $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
            'client_id' => GOOGLE_CLIENT_ID,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'email profile openid',
            'access_type' => 'offline',
            'state' => bin2hex(random_bytes(16))
        ]);
        header('Location: ' . $authUrl);
        exit;
    }

    public function google_callback() {
        if (!isset($_GET['code'])) {
            header('Location: ' . URLROOT . '/auth/login?error=no_code');
            exit;
        }

        $redirectUri = URLROOT . '/auth/google_callback';
        $tokenUrl = 'https://oauth2.googleapis.com/token';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'client_id' => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
            'code' => $_GET['code']
        ]));
        
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);
        
        if (isset($data['access_token'])) {
            $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $userInfoUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $data['access_token']]);
            $userInfo = json_decode(curl_exec($ch), true);
            curl_close($ch);

            $this->handleOAuthLogin($userInfo['email'], $userInfo['name'], 'google');
        } else {
            header('Location: ' . URLROOT . '/auth/login?error=auth_failed');
        }
    }

    // Facebook OAuth Flow
    public function facebook() {
        $redirectUri = URLROOT . '/auth/facebook_callback';
        $authUrl = 'https://www.facebook.com/v20.0/dialog/oauth?' . http_build_query([
            'client_id' => FACEBOOK_CLIENT_ID,
            'redirect_uri' => $redirectUri,
            'scope' => 'email,public_profile',
            'state' => bin2hex(random_bytes(16))
        ]);
        header('Location: ' . $authUrl);
        exit;
    }

    public function facebook_callback() {
        if (!isset($_GET['code'])) {
            header('Location: ' . URLROOT . '/auth/login?error=no_code');
            exit;
        }

        $redirectUri = URLROOT . '/auth/facebook_callback';
        $tokenUrl = 'https://graph.facebook.com/v20.0/oauth/access_token';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'client_id' => FACEBOOK_CLIENT_ID,
            'client_secret' => FACEBOOK_CLIENT_SECRET,
            'redirect_uri' => $redirectUri,
            'code' => $_GET['code']
        ]));
        
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);
        
        if (isset($data['access_token'])) {
            $userInfoUrl = 'https://graph.facebook.com/me?fields=id,name,email&access_token=' . $data['access_token'];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $userInfoUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $userInfo = json_decode(curl_exec($ch), true);
            curl_close($ch);

            $email = isset($userInfo['email']) ? $userInfo['email'] : $userInfo['id'].'@facebook.com';
            $name = $userInfo['name'];
            $this->handleOAuthLogin($email, $name, 'facebook');
        } else {
            header('Location: ' . URLROOT . '/auth/login?error=auth_failed');
        }
    }

    private function handleOAuthLogin($email, $name, $provider) {
        $userExists = $this->userModel->findUserByEmail($email);
        
        if ($userExists) {
            $userData = $this->userModel->getUserDataByEmail($email);
            $_SESSION['user_id'] = $userData->UserID;
            $_SESSION['username'] = $userData->Username;
            $_SESSION['role_id'] = $userData->RoleID;
            $this->redirectUserByRole($userData->RoleID);
        } else {
            $password = bin2hex(random_bytes(8)); 
            $data = [
                'username' => $name,
                'email' => $email,
                'phone' => '',
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ];
            
            if ($this->userModel->registerOAuth($data)) {
                $userData = $this->userModel->getUserDataByEmail($email);
                $_SESSION['user_id'] = $userData->UserID;
                $_SESSION['username'] = $userData->Username;
                $_SESSION['role_id'] = $userData->RoleID;
                $this->redirectUserByRole($userData->RoleID);
            } else {
                die('Something went wrong during registration');
            }
        }
    }

    private function redirectUserByRole($roleId) {
        switch ($roleId) {
            case '01': // Admin
                header('Location: ' . URLROOT . '/admin');
                break;
            case '03': // Planner
                header('Location: ' . URLROOT . '/planner');
                break;
            case '04': // Accountant
                header('Location: ' . URLROOT . '/accountant');
                break;
            case '02': // User
            default:
                header('Location: ' . URLROOT . '/');
                break;
        }
        exit;
    }

    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['role_id']);
        session_destroy();
        header('Location: ' . URLROOT . '/auth/login');
        exit;
    }

    public function forgotPassword() {
        $data = [
            'title' => 'Forgot Password - EcoPath'
        ];
        $this->view('auth/forgot_password', $data);
    }

    public function sendResetLink() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
            $email = trim($_POST['email']);
            $user = $this->userModel->getUserDataByEmail($email);
            
            if ($user) {
                $token = bin2hex(random_bytes(32));
                // 5 minutes expiry
                $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));
                
                if ($this->userModel->setResetToken($email, $token, $expiry)) {
                    $resetLink = URLROOT . '/auth/resetPassword?token=' . $token;
                    
                    if (sendPasswordResetEmail($email, $resetLink)) {
                        $this->view('auth/forgot_password', ['success' => 'Password reset link sent! Check your email.']);
                        return;
                    } else {
                        $this->view('auth/forgot_password', ['error' => 'Failed to send email. Please try again later.']);
                        return;
                    }
                }
            } else {
                // To prevent email enumeration, we can still show success message, or error. 
                // We'll just show an error for now since it's an internal tool, or "If the email exists, a link was sent".
                $this->view('auth/forgot_password', ['success' => 'If an account exists with that email, a reset link has been sent.']);
                return;
            }
        }
        header('Location: ' . URLROOT . '/auth/forgotPassword');
        exit;
    }

    public function resetPassword() {
        if (!isset($_GET['token'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
        $token = $_GET['token'];
        $user = $this->userModel->findByResetToken($token);

        $data = [];
        if (!$user) {
            $data['error'] = 'Invalid or expired token.';
        }
        
        $this->view('auth/reset_password', $data);
    }

    public function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['token'], $_POST['password'], $_POST['confirm_password'])) {
            $token = $_POST['token'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            $user = $this->userModel->findByResetToken($token);
            
            if (!$user) {
                $this->view('auth/reset_password', ['error' => 'Invalid or expired token.']);
                return;
            }

            if ($password !== $confirm_password) {
                $this->view('auth/reset_password', ['error' => 'Passwords do not match.']);
                return;
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $this->userModel->updatePassword($user->UserID, $passwordHash);
            
            header('Location: ' . URLROOT . '/auth/login?success=password_reset');
            exit;
        }
        header('Location: ' . URLROOT . '/auth/login');
        exit;
    }
}
