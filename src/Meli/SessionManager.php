<?php

namespace Meli;

class SessionManager {

    public function start() {
        if (!session_id()) {
            session_start();
        }
    }
}