<?php

/**
 * Route Configuration
 * Maps URLs to controllers and methods
 */

class Router
{
    private $routes = [];
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Register a GET route
     */
    public function get($uri, $controller, $method)
    {
        $this->register('GET', $uri, $controller, $method);
    }

    /**
     * Register a POST route
     */
    public function post($uri, $controller, $method)
    {
        $this->register('POST', $uri, $controller, $method);
    }

    /**
     * Register a route
     */
    private function register($httpMethod, $uri, $controller, $method)
    {
        $this->routes[] = [
            'method' => $httpMethod,
            'uri' => $uri,
            'controller' => $controller,
            'action' => $method
        ];
    }

    /**
     * Dispatch the request to appropriate controller
     */
    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        // Strip /public prefix if webroot isn't set to public/ directory
        $uri = preg_replace('#^/public#', '', $uri);
        if (empty($uri)) $uri = '/';

        // Try to match route
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = $this->uriToPattern($route['uri']);
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove full match

                // Instantiate controller
                $controllerClass = 'App\\Http\\Controllers\\' . $route['controller'];
                $controller = new $controllerClass($this->pdo);

                // Call action
                $action = $route['action'];
                $response = call_user_func_array([$controller, $action], $matches);

                // Handle response
                if (is_array($response) && isset($response['view'])) {
                    // Render view
                    return $this->render($response['view'], $response['data'] ?? []);
                } elseif (is_string($response)) {
                    // Return plain string (likely JSON or view name)
                    if (strpos($response, '{') === 0 || strpos($response, '[') === 0) {
                        echo $response;
                    } else {
                        // View name
                        return $this->render($response);
                    }
                } else {
                    echo $response;
                }
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        echo "Page not found";
    }

    /**
     * Convert URI pattern to regex
     */
    private function uriToPattern($uri)
    {
        $pattern = preg_replace_callback('/\{([a-zA-Z_]+)\}/', function($matches) {
            return '([0-9a-zA-Z-]+)';
        }, $uri);
        return '#^' . $pattern . '$#';
    }

    /**
     * Render a view
     */
    private function render($view, $data = [])
    {
        $viewPath = __DIR__ . '/../resources/views/' . $view . '.blade.php';

        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo "View not found: " . $view;
            return;
        }

        extract($data);
        ob_start();
        include $viewPath;
        echo ob_get_clean();
    }
}

// Create router instance and register all routes
$router = new Router($pdo);

// ===== AUTHENTICATION ROUTES =====
$router->get('/', 'AuthController', 'showLoginForm');
$router->post('/login', 'AuthController', 'login');
$router->get('/logout', 'AuthController', 'logout');

// ===== POLL ROUTES =====
$router->get('/dashboard', 'PollController', 'dashboard');
$router->get('/poll/{pollId}', 'PollController', 'show');

// Poll creation (Admin only)
$router->get('/polls/create', 'PollController', 'create');
$router->post('/polls/store', 'PollController', 'store');

// ===== AJAX API ROUTES =====

// Voting endpoints
$router->post('/api/vote/cast', 'VoteController', 'castVote');
$router->get('/api/vote/status', 'VoteController', 'checkVoteStatus');
$router->get('/api/results', 'VoteController', 'getResults');

// Poll data endpoints
$router->get('/api/polls', 'PollController', 'getPolls');
$router->get('/api/polls/{pollId}', 'PollController', 'getPoll');

// ===== ADMIN ROUTES =====
$router->get('/admin/dashboard', 'AdminController', 'dashboard');
$router->get('/admin/polls/{pollId}', 'AdminController', 'managePoll');

// Admin API endpoints
$router->get('/api/admin/voters', 'AdminController', 'getVoters');
$router->post('/api/admin/vote/release', 'AdminController', 'releaseVote');
$router->get('/api/admin/vote-history', 'AdminController', 'getVoteHistory');
$router->post('/api/admin/poll/status', 'AdminController', 'togglePollStatus');
$router->post('/api/admin/poll/delete', 'AdminController', 'deletePoll');

// Admin extended API endpoints
$router->get('/api/admin/activity-logs', 'AdminController', 'getActivityLogs');
$router->post('/api/admin/user/create', 'AdminController', 'createUser');
$router->post('/api/admin/user/delete', 'AdminController', 'deleteUser');
$router->get('/api/admin/user/stats', 'AdminController', 'getUserStats');
$router->get('/api/admin/votes', 'AdminController', 'getVotesForDashboard');

// Dispatch the request
$router->dispatch();
