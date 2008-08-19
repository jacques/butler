<?php

/**
 * Butler_Serialize
 *
 * PHP versions 5.1.0+
 *
 * Copyright (c) 2008, Digg, Inc.
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice, 
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice, 
 *   this list of conditions and the following disclaimer in the documentation 
 *   and/or other materials provided with the distribution.
 * - Neither the name of the Digg, Inc. nor the names of its contributors 
 *   may be used to endorse or promote products derived from this software 
 *   without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE 
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE 
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR 
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF 
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS 
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN 
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  System
 * @package   Butler
 * @author    Joe Stump <joe@joestump.net> 
 * @copyright 2008 Digg, Inc.
 * @license   http://tinyurl.com/new-bsd-license New BSD License
 * @version   CVS: $Id:$
 * @link      http://code.google.com/p/digg
 */

/**
 * Serialization framework for Butler
 *
 * @category  System
 * @package   Butler
 * @author    Joe Stump <joe@joestump.net> 
 * @copyright 2008 Digg, Inc.
 * @license   http://tinyurl.com/new-bsd-license New BSD License
 * @link      http://code.google.com/p/digg
 */
final class Butler_Serialize
{
    /**
     * Don't allow instantiation
     *
     * @access private
     * @return void
     */
    private function __construct()
    {

    }

    /**
     * Create serializer instance
     *
     * @param string $type Type of serializer to load (xml or json)
     *
     * @see Butler_Serialize_json, Butler_Serialize_xml
     * @return object Instance of serializer 
     */
    static public function factory($type)
    {
        $file = 'Butler/Serialize/' . $type . '.php';
        include_once $file;

        $class    = 'Butler_Serialize_' . $type;
        $instance = new $class();
        return $instance;
    }
}

?>
