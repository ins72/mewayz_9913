<?php

namespace App\Yena;

class YenaStream{

    protected $response;

    public function stream($name, $content, $replace = false)
    {
        $this->ensureStreamResponseStarted();

        $this->streamContent(['name' => $name, 'content' => $content, 'replace' => $replace]);
    }

    public function ensureStreamResponseStarted()
    {
        if ($this->response) return;

        $this->response = response()->stream(null , 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
            'X-Accel-Buffering' => 'no',
            'X-Yena-Ai' => true,
        ]);

        $this->response->sendHeaders();
    }

    public function streamContent($body)
    {
        echo json_encode(['stream' => true, 'body' => $body, 'endStream' => true]);

        if (ob_get_level() > 0) {
            ob_flush();
        }

        flush();
    }
}
