<?php namespace JR\Porter;

use JR\Porter\Exceptions\AccessDeniedException;

trait Controller
{
    protected $_currentUser;

    protected function requireAccess($action, $thing)
    {
        if (!$this->currentUser() || $this->currentUser()->cannot($action, $thing))
            throw new AccessDeniedException("Not authorised to $action a " . (is_object($thing) ? get_class($thing) : (string)$thing));
    }
}