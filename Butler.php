<?php

/**
 * Butler
 *
 * Butler is a simple framework for doing small tasks on a single web
 * server within the PHP userland. At Digg we have many web servers that run
 * APC. We store a few key pieces of information in APC, one of them being our
 * site's configuration data, which needs to be flushed when we push new 
 * configurations. We use Butler to manipulate APC, return PEAR/PHP
 * information and a bunch of other things.
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
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   CVS: $Id:$
 * @link      http://code.google.com/p/digg
 */

require_once 'Butler/Exception.php';
require_once 'Butler/Serialize.php';

/**
 * Butler
 *
 * @category System
 * @package  Butler
 * @author   Joe Stump <joe@joestump.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link     http://code.google.com/p/digg
 */
final class Butler
{
    /**
     * HTTP codes/responses
     *
     * @access protected
     * @var array $httpCodes Recognized HTTP codes
     * @static
     */
    static protected $httpCodes = array(
        200 => 'OK',
        500 => 'Internal Server Error'
    );

    /**
     * Don't allow instantiation
     * 
     * @return void
     */
    private function __construct()
    {

    }

    /**
     * Create a servant
     *
     * @param string $servant Name of servant to load
     * 
     * @throws {@link Butler_Exception} when class is not found
     * @return object Instance of servant for your bidding
     */
    static public function factory($servant)
    {
        $file = 'Butler/' . $servant . '.php';
        include_once $file;

        $class = 'Butler_' . $servant;
        if (!class_exists($class)) {
            throw new Butler_Exception(
                'Service class not found: ' . $class
            );
        }

        $instance = new $class();
        return $instance;
    }

    /**
     * The dispatcher for Butler
     *
     * Butler is Digg's internal servant who does our bidding. 
     * Initially he was brought to life by Joe Stump to reload configurations 
     * inside of APC. He can be extended to do your bidding on each individual 
     * machine. He cannot, however, share any code with Digg's internal code.
     *
     * @access public
     * @return void
     * @static
     */
    static public function dispatch()
    {
        try {
            if (!isset($_GET['butler_service']) || 
                !strlen($_GET['butler_service'])) {
                $service = 'butler';
                $event   = 'reflectServices';
            } else {
                $service = $_GET['butler_service'];
                if (!isset($_GET['butler_event']) || 
                    !strlen($_GET['butler_event'])) {
                    if (isset($_GET['butler_type']) && 
                        strlen($_GET['butler_type'])) {
                        $service = 'butler';
                        $event   = 'reflectEvents';
                    } else {
                        throw new Butler_Exception('No event provided');
                    }
                } else {
                    $event = $_GET['butler_event'];
                }
            }

            $butler = self::factory($service);
            if (!method_exists($butler, $event)) {
                throw new Butler_Exception('Invalid event specified');
            }

            $res           = $butler->$event();
            $res['status'] = 200;
        } catch (Exception $e) {
            $res = array(
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'status' => 500
            );
        }


        $serializer = Butler_Serialize::factory($_GET['butler_type']);
        
        header('HTTP/1.1 ' . $res['status'] . ' ' . self::$httpCodes[$res['status']]);
        header('Content-Type: ' . $serializer->getContentType());
        echo $serializer->serialize($res);
    }
}

?>
