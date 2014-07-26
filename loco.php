<?php
/**
 * LOCO - Light Object Calling Order
 */
class LOCO {
  protected $actions;
  protected $object;
  
  function __construct() {
    $args = func_get_args();
    if(count($args) > 0) {
      $class = array_shift($args);
    } else {
      $class = null;
    }
    $this->actions = array(array('construct', $class, $args));
  }
  
  function __set($property, $value) {
    $this->actions[] = array('set', $property, $value);
  }
  
  function __call($function, $args) {
    if($function == '__lococall') {
      $function = array_shift($args);
      $this->actions[] = array('lococall', $function, $args);
      return $this->actions;
    } elseif($function == '__locoinclude') {
      $function = array_shift($args);
      array_unshift($this->actions, array('include', $function, $args));
    } else {
      $this->actions[] = array('call', $function, $args);
    }
  }
  
}

/**
 * xLOCO - Executable Light Object Calling Order
 */
class xLOCO extends LOCO {
  public static function Execute($actions) {
    $result = false;
    if(is_array($actions)) {
      $object = null;
      foreach($actions as $action) {
        if($action[0] == 'set') {
          $property = $action[1];
          $object->$property = $action[2];
        } elseif($action[0] == 'call') {
          $function = $action[1];
          call_user_method_array($function, $object, $action[2]);
        } elseif($action[0] == 'include') {
          $filename = $action[1];
          include_once($filename);
        } elseif($action[0] == 'construct') {
          $class = $action[1];
          if(($action[2] == null) || (count($action[2]) == 0)) {
            $object = new $class();
          } else {
            $eval = '$object = new $class(';
            $first = true;
            for($i = 0, $n = count($action[2]); $i < $n; $i++) {
              if($first) {
                $first = false;
              } else {
                $eval .= ',';
              }
              $eval .= '$action['.$i.']';
            }
            $eval .= ');';
            eval($eval);
          }
        } elseif($action[0] == 'lococall') {
          $function = $action[1];
          $result = call_user_method_array($function, $object, $action[2]);
          break;
        }
      }
    }
    return $result;
  }
  
  public static function ExecuteFile($filename) {
    if(is_file($filename)) {
      $data = file_get_contents($filename);
      if(is_string($data)) {
        $actions = unserialize($data);
        if(is_array($actions)) {
          return self::Execute($actions);
        }
      }
    }
    throw(new LOCOException('File not found ['.$filename.']'));
    return false;
  }
  
}

/**
 * GoLOCO - Go Light Object Calling Order
 * A special version of LOCO that sums up all features of LOCO and
 * xLOCO at the same time it adds new functionality.
 */
class GoLOCO extends LOCO {
  function __call($function, $args) {
    if($function == '__locogo') {
      $result = null;
      if(is_array($this->actions)) {
        $this->object = null;
        foreach($this->actions as $action) {
          if($action[0] == 'set') {
            $property = $action[1];
            $this->object->$property = $action[2];
          } elseif($action[0] == 'call') {
            $function = $action[1];
            $result = call_user_method_array($function, $this->object, $action[2]);
          } elseif($action[0] == 'include') {
            $filename = $action[1];
            include_once($filename);
          } elseif($action[0] == 'construct') {
            $class = $action[1];
            if(($action[2] == null) || (count($action[2]) == 0)) {
              $this->object = new $class();
            } else {
              $eval = '$this->object = new $class(';
              $first = true;
              for($i = 0, $n = count($action[2]); $i < $n; $i++) {
                if($first) {
                  $first = false;
                } else {
                  $eval .= ',';
                }
                $eval .= '$action['.$i.']';
              }
              $eval .= ');';
              eval($eval);
            }
          } elseif($action[0] == 'lococall') {
            $function = $action[1];
            $result = call_user_method_array($function, $this->object, $action[2]);
            break;
          }
        }
        if(count($args) > 0) {
          $function = array_shift($args);
          $result = call_user_method_array($function, $this->object, $args);
        }
      }
      return $result;
    } elseif($function == '__locoobject') {
      return $this->object;
    } elseif($function == '__locoget') {
      return $this->actions;
    } elseif($function == '__locoset') {
      $this->actions = $args[0];
    } elseif($function == '__locosave') {
      $filename = $args[0];
      return file_put_contents($filename, serialize($this->actions));
    } elseif($function == '__locoload') {
      $filename = $args[0];
      if(is_file($filename)) {
        $actions = unserialize(file_get_contents($filename));
        if(is_array($actions)) {
          $this->actions = $actions;
          return true;
        }
      }
      return false;
    } else {
      return parent::__call($function, $args);
    }
    return null;
  }
}

/**
 * LOCOException - Light Object Calling Order Exception
 * Just a simple wrapper to extend at a later stage.
 */
class LOCOException extends Exception {
}

?>