<?php

/**
 * Butler_Common
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
require_once 'Net/Gearman/Manager.php';

/**
 * Butler common class
 * 
 * @category System
 * @package  Butler
 * @author   Joe Stump <joe@joestump.net> 
 * @license  http://tinyurl.com/new-bsd-license New BSD License
 * @link     http://code.google.com/p/digg
 */
class Butler_gearman extends Butler_Common
{
    /**
     * Array of manager instances
     * 
     * Technically, a person can have more than one Gearman server specified
     * in their digg.xml file. As a result everything in this Butler endpoint
     * should iterate over this returning arrays of information.
     *
     * @var array $managers An array of Net_Gearman_Manager
     * @see Net_Gearman_Manager
     */
    protected $managers = array();

    /**
     * Constructor 
     *
     * @access public
     * @throws {@link Butler_Exception} on invalid servers 
     * @return void
     */
    public function __construct()
    {
        if (!isset($_GET['servers']) || 
            !is_array($_GET['servers']) ||
            !count($_GET['servers'])) {
            throw Butler_Exception('No Gearman servers configured');
        }

        foreach ($_GET['servers'] as $server) {
            $this->managers[$server] = new Net_Gearman_Manager($server);
        }
    }

    /**
     * Get the Gearmand version
     *
     * @return array
     */
    public function version()
    {
        $versions = array();
        foreach ($this->managers as $server => $mgr) {
            $versions[] = array(
                'host' => $server,
                'version' => $mgr->version()
            );
        }

        return array('server' => $versions);
    }

    /**
     * Get worker information
     *
     * @return array
     */
    public function workers()
    {
        $workers = array();
        foreach ($this->managers as $server => $mgr) {
            $workers[] = array(
                'host' => $server,
                'worker' => $mgr->workers()
            );
        }

        return array('server' => $workers);
    }

    /**
     * Get status 
     *
     * @return array
     */
    public function status()
    {
        $workers = array();
        foreach ($this->managers as $server => $mgr) {
            $s = array();
            $status = $mgr->status();
            foreach ($status as $key => $stats) {
                $stats['job'] = $key;
                $s[] = $stats;        
            } 

            $workers[] = array(
                'host' => $server,
                'status' => $s
            );
        }

        return array('server' => $workers);
    }

    /**
     * Set the max queue size
     *
     * @return array
     */
    public function setMaxQueueSize()
    {
        if (!isset($_GET['function']) || !isset($_GET['size'])) {
            throw new Butler_Exception('Both function and size are required');
        }

        $res = array();
        foreach ($this->managers as $server => $mgr) {
            $check = $mgr->setMaxQueueSize($_GET['function'], $_GET['size']); 
            $res[] = array(
                'server' => $server,
                'function' => $_GET['function'],
                'size' => $_GET['size'],
                'result' => ($check) ? 1 : 0
            );
        }

        return array('result' => $res);
    }
}

?>
