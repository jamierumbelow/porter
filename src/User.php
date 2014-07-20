<?php namespace JR\Porter;

trait User
{
    protected $_abilities = array();
    protected $_overlord = FALSE;

    public function may($action, $thing, callable $callback = null)
    {
        // Do we have a record of $thing already?
        if (!isset($this->_abilities[$thing]))
            $this->_abilities[$thing] = array();

        // Do we have a callback? If not, just use TRUE (bool).
        $this->_abilities[$thing][$action] = $callback ?: TRUE;
    }

    public function mayIfEquals($action, $thing, $what, $compareValue)
    {
        $this->may($action, $thing, function($instance) use ($what, $compareValue)
        {
            return ($instance->$what == $compareValue);
        });
    }

    public function can($action, $thing)
    {
        if ($this->_overlord === TRUE) return TRUE;

        $result = FALSE;
            
        // $thing is likely to be an instance of a specific class
        if (is_object($thing))
            $class = get_class($thing);
        else
            $class = $thing;

        if (isset($this->_abilities[$class]) && isset($this->_abilities[$class][$action]))
        {
            // If we have specified a callback, we want to pass it whatever $thing is
            // and use the return value of that as our result. This lets us have custom
            // callback code on a per-instance basis.
            if (is_callable($this->_abilities[$class][$action]))
            {
                $result = call_user_func_array($this->_abilities[$class][$action], array( $thing ));
            }

            // Else, just check to see if it's obvious that we're allowed to do something.
            else
            {
                $result = ($this->_abilities[$class][$action] === TRUE);
            }
        }

        // We've not managed to find this specific action. Maybe we need to check for 'manage'?
        elseif ( isset($this->_abilities[$class]) && ( $action == 'read' || $action == 'create' || $action == 'update' || $action == 'destroy' ) && ( isset($this->_abilities[$class]['manage']) ) )
        {
            if (is_callable($this->_abilities[$class]['manage']))
                $result = call_user_func_array($this->_abilities[$class]['manage'], array( $thing ));
            else
                $result = ($this->_abilities[$class]['manage'] === TRUE);
        }

        // Nope, we've got nothing. Better not let them do it, then!
        else
        {
            $result = FALSE;
        }

        return $result;
    }

    public function cannot($action, $thing)
    {
        return !$this->can($action, $thing);
    }

    public function isOverlord()
    {
        $this->_overlord = TRUE;
    }
}