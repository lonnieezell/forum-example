<?php

namespace App\Libraries;

use CodeIgniter\Session\Session;

class Alerts
{
    protected array $data = [];

    public function __construct(protected Session $session)
    {
    }

    /**
     * Set alert type and message
     */
    public function set(string $type, string $message): static
    {
        if (! isset($this->data[$type])) {
            $this->data[$type] = [];
        }

        $this->data[$type][] = $message;

        return $this;
    }

    /**
     * Get alerts.
     */
    public function get(?string $type = null): array
    {
        if ($type === null) {
            return $this->data;
        }

        return $this->data[$type] ?? [];
    }

    /**
     * Clear alerts.
     */
    public function clear(?string $type = null): static
    {
        if ($type === null) {
            $this->data = [];
        } else {
            unset($this->data[$type]);
        }

        return $this;
    }

    /**
     * Display alerts inline.
     */
    public function inline(): string
    {
        if ($this->data !== []) {
            return view('_alerts/inline', ['alerts' => $this->data]);
        }

        return '';
    }

    /**
     * Store alerts in the session with the flash data.
     */
    public function session(): void
    {
        if ($this->data !== []) {
            $this->session->setFlashdata('alerts', $this->data);
        }
    }

    /**
     * Display alerts container.
     */
    public function container(): string
    {
        return view('_alerts/container');
    }
}
