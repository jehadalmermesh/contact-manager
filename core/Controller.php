<?php
abstract class Controller
{
    /** Render a view inside the shared layout. $data keys become variables in the view. */
    protected function view(string $name, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/' . $name . '.php';
        require __DIR__ . '/../views/layout/footer.php';
    }

    protected function requirePost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method not allowed.');
        }
        Csrf::verify();
    }

    protected function notFound(): never
    {
        http_response_code(404);
        exit('Page not found.');
    }
}
