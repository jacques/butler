<?php

/**
 * Butler_butler
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

require_once 'Butler/Common.php';

/**
 * Reflection Butler service
 *
 * This service will self-inspect all butler servants that are currently 
 * installed alongside of the Butler. It doesn't provide insight into
 * parameters that are allowed though.
 *
 * This is an internal service only and shouldn't be called directly from the
 * web service.
 *
 * @category  System
 * @package   Butler
 * @author    Joe Stump <joe@joestump.net> 
 * @copyright 2008 Digg, Inc.
 * @license   http://tinyurl.com/new-bsd-license New BSD License
 * @link      http://code.google.com/p/digg
 */
class Butler_butler extends Butler_Common
{
    /**
     * Reflect events that are available for a given service
     * 
     * @link http://php.net/reflection
     * @see Butler::factory()
     * @return array
     */
    public function reflectEvents()
    {
        $service = Butler::factory($_GET['butler_service']);
        $refl    = new ReflectionClass($service);
        $methods = array();
        foreach ($refl->getMethods() as $method) {
            if ($method->isPublic() && substr($method->name, 0, 2) != '__') {
                $methods[] = $method->getName();
            }
        }

        return array(
            'service' => $_GET['butler_service'],
            'methods' => $methods
        );
    }

    /**
     * Discover and reflect services and their methods
     * 
     * @link http://php.net/reflection
     * @see Butler::factory()
     * @return array
     */
    public function reflectServices()
    {
        $ref  = new ReflectionClass('Butler');
        $path = preg_replace('/\.php$/', '', $ref->getFileName());

        $allServices = array();
        $services    = array();
        $ignore      = array('butler', 'Common', 'Exception', '.svn', 
                             'Serialize');

        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                if (!is_dir($file) && 
                    preg_match('/^[a-z].*\.php$/i', $file)) {
                    $file = ereg_replace('\.php', '', $file);
                    if (!in_array($file, $ignore)) {
                        $services[] = "$file";
                    }
                }
            }
            closedir($handle);
        }

        foreach ($services as $s) {
            $service = Butler::factory($s);
            $refl    = new ReflectionClass($service);
            $methods = array();
            foreach ($refl->getMethods() as $method) {
                if ($method->isPublic() && 
                    substr($method->name, 0, 2) != '__') {
                    $methods[] = $method->getName();
                }
            }

            $allServices[] = array('name' => $s, 'method' => $methods);
        }

        return array(
            'service' => $allServices
        );
    }
}

?>
