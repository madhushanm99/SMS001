<?php

use Illuminate\Support\Facades\Broadcast;

// Private user notifications
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Public staff notifications channel
Broadcast::channel('staff-notifications', function () {
    return true; // Allow all authenticated users to listen
});

// Public appointments channel
Broadcast::channel('appointments', function () {
    return true; // Allow all authenticated users to listen
});
