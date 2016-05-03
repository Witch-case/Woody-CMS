<?php

class Police {
    
    function Police( $args )
    {
        $this->id                   = $args['id'];
        $this->fk_profile           = $args['fk_profile'];
        $this->module               = $args['module'];
        $this->action               = $args['action'];
        $this->positionString       = $args['position'];
        $this->rigths_limitation    = $args['rigths_limitation'];
        $this->group_fk_profile     = $args['group_fk_profile'];
    }
    
    function formArray()
    {
        global $db;
        global $log;
        
        if( !isset($this->localisation) )
        {
            if( !isset($this->position) )
            {   $this->position = explode(',', $this->positionString);  }
            
            $localisationPosition   = [];
            $this->inherit_subtree  = false;
            foreach( $this->position as $i => $levelValue )
            {
                if( strcmp($levelValue, '*') == 0 )
                {   $this->inherit_subtree = true;  }
                else
                {   $localisationPosition[$i+1] = $levelValue;  }
            }
            
            $this->localisation = Localisation::getFromPosition($localisationPosition);
        }
        
        $return =   array(
                        "id"            => $this->id,
                        "module"        => $this->module." - ".$this->action,
                        "localisation"  => $this->localisation,
                        "inherit"       => $this->inherit_subtree,
                        "limitation"    => $this->rigths_limitation,
                        "inherit"       => $this->inherit_subtree,
                    );
        
        return $return;
    }
}
