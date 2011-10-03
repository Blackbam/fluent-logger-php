<?php
/*
 * The MIT License
 *
 * Copyright (c) 2011 Shuhei Tanuma
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace Fluent;

//use Fluent\Logger;
use Fluent\Event;

class Event
{
    protected $logger;
    protected $action;
    protected $accessor = array();
    protected $values = array();
    protected $nodes = array();
    
    public function __construct($logger, $action, $options /** $option2, */)
    {
        $this->logger = $logger;
        $this->action = $action;
        foreach($options as $option) {
            $this->accessor[$option] = true;
        }
    }
    
    public function with(Event $event)
    {
        if (array_search($event, $this->nodes) !== false) {
            $this->nodes[] = $event;
        }
            
        return $this;
    }
    
    public function getValues()
    {
        return $this->values;
    }
    
    public function has_node()
    {
        return ($this->nodes) ? true : false;
    }
    
    public function post()
    {
        $options = array();
        foreach ($this->nodes as $node) {
            // Todo: support tree nodes.
            $options = array_merge($options, $node->getValues());
        }
        $params = array_merge($options, $this->values);
        $params["action"] = $this->action;
        ksort($params);
        
        //$logger = \Fluent\Logger::$current;
        $this->logger->post($params,$this->action);
    }
    
    public function __call($key, $options)
    {
        if (isset($this->accessor[$key])) {
            $this->values[$key] = $options[0];
            return $this;
        } else {
            throw new \Exception("unexpected accessor name {$key}");
        }
    }
}
