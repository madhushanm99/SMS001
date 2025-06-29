<?php

if (!function_exists('getStatusBadge')) {
    function getStatusBadge($status)
    {
        return match ($status) {
            'draft' => 'status-draft',
            'pending' => 'status-pending',
            'approved' => 'status-approved',
            'received' => 'status-received',
            'cancelled' => 'status-cancelled',
            default => 'status-default',
        };
    }
}
