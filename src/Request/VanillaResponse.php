<?php

namespace IsmaCortGtz\EasyPayPalPhp\Request;

class VanillaResponse {
    private $status = null;
    private $body = null;
    private $contentType = null;

    public function __construct($status = null, $body = null, $contentType = null) {
        $this->status = $status;
        $this->body = $body;
        $this->contentType = $contentType;
    }

    public function failed() {
        if ($this->status === null) return true;
        if (gettype($this->status) !== "integer") return true;
        if ($this->status < 200) return true;
        if ($this->status > 299) return true;
        return false;
    }

    public function success() { return !$this->failed(); }
    public function status() { return $this->status; }

    public function body() { return $this->body; }
    public function getContentType() { return $this->contentType; }

    public function object() {
        if ($this->body === null) return null;
        if ($this->contentType === null) return null;
        if (gettype($this->body) !== "string") return null;
        if (gettype($this->contentType) !== "string") return null;
        if ($this->contentType != "application/json") return null;
        return json_decode($this->body, false);
    }

    public function json() {
        if ($this->body === null) return null;
        if ($this->contentType === null) return null;
        if (gettype($this->body) !== "string") return null;
        if (gettype($this->contentType) !== "string") return null;
        if ($this->contentType != "application/json") return null;
        return json_decode($this->body, true);
    }
}