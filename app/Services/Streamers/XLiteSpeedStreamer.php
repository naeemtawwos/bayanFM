<?php

namespace App\Services\Streamers;

class XLiteSpeedStreamer extends Streamer implements DirectStreamerInterface
{
    /**
     * Stream the current song using Lite Speed's X-LiteSpeed-Location directive.
     */
    public function stream(): void
    {
        header("X-LiteSpeed-Location: {$this->song->path}");
        header("Content-Type: $this->contentType");
        header('Content-Disposition: inline; filename="' . basename($this->song->path) . '"');
        exit;
    }
}
