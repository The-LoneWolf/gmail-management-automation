<?php

namespace App\Http\Controllers;

use App\Models\EmailMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmailMessagePreviewController extends Controller
{
    public function __invoke(Request $request, EmailMessage $emailMessage): Response
    {
        abort_unless($request->user()?->id === $emailMessage->gmailAccount->user_id, 403);

        $body = $emailMessage->sanitized_html_body
            ?: nl2br(e($emailMessage->text_body ?: $emailMessage->snippet ?: 'No message body imported.'), false);

        return response($this->document($body))
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('Content-Security-Policy', "default-src 'none'; style-src 'unsafe-inline'; img-src 'none'; base-uri 'none'; form-action 'none'");
    }

    private function document(string $body): string
    {
        return <<<HTML
            <!doctype html>
            <html>
                <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <style>
                        :root { color-scheme: light; }
                        body {
                            margin: 0;
                            padding: 20px;
                            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                            color: #111827;
                            background: #ffffff;
                            line-height: 1.55;
                        }
                        table { max-width: 100%; }
                        img { display: none !important; }
                        a { color: #2563eb; word-break: break-word; }
                        pre, code { white-space: pre-wrap; word-break: break-word; }
                    </style>
                </head>
                <body>{$body}</body>
            </html>
            HTML;
    }
}
