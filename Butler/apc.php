<?php

/**
 * Butler_apc
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
 * Access and manipulate various APC information
 *
 * @category  System
 * @package   Butler
 * @author    Joe Stump <joe@joestump.net>
 * @copyright 2008 Digg, Inc.
 * @license   http://tinyurl.com/new-bsd-license New BSD License
 * @link      http://code.google.com/p/digg
 */
class Butler_apc extends Butler_Common
{
    /**
     * Get APC information
     *
     * @param string limited Whether to show a limited view
     * @param string type    Set to user to see APC user cache
     * 
     * @return array
     */
    public function info()
    {
        if (isset($_GET['limited'])) {
            $limited = (in_array($_GET['limited'], array('1', 'true', 'y')));
        } else {
            $limited = false;
        }

        if (isset($_GET['type']) && $_GET['type'] == 'user') {
            $type = $_GET['type'];
        } else {
            $type = null;
        }

        return array(
            'limited' => $limited,
            'type'    => $type,
            'info'    => apc_cache_info($type, $limited)
        );
    }

    /**
     * Clear the APC cache
     *
     * @param string type Set to user to see APC user cache
     * 
     * @return void
     */
    public function clear()
    {
        if (isset($_GET['type']) && $_GET['type'] == 'user') {
            $type = $_GET['type'];
        } else {
            $type = null;
        }

        $res = (apc_clear_cache($type)) ? 1 : 0;
        return array(
            'type'    => $type,
            'cleared' => $res
        );
    }

    /**
     * Get APC Shared Memory information
     *
     * @param string $limited Whether to show limited info
     * 
     * @access public
     * @return array
     */
    public function sma()
    {
        if (isset($_GET['limited'])) {
            $limited = (in_array($_GET['limited'], array('1', 'true', 'y')));
        } else {
            $limited = false;
        }
       
        return array(
            'limited' => $limited,
            'info'    => apc_sma_info($limited)
        );
    }

    /**
     * Prime the APC file cache
     *
     * Find all PHP files in the document root that are NOT Smarty templates 
     * and prime them in the APC cache.
     *
     * @access      public
     * @return      array
     */
    public function prime()
    {
        if (!isset($_GET['path'])) {
            throw new Butler_Exception('A valid path is required');
        }

        if (!is_dir($_GET['path']) || !is_readable($_GET['path'])) {
            throw new Butler_Exception('Invalid path provided');
        }

        // Find all *.php files that are NOT Smarty templates that live on
        // this device within this path.
        $cmd   = 'find ' . $_GET['path'] . 
                 ' -xdev -type f -name \*.php -not -name \*.tpl.php';
        $files = explode("\n", shell_exec($cmd));
        $cnt   = count($files);
        $ret   = array();
        for ($i = 0 ; $i < $cnt ; $i++) {
            $file = trim($files[$i]);
            if (file_exists($file)) {
                $res   = apc_compile_file($file);
                $ret[] = array(
                    'file'     => $file,
                    'compiled' => ($res == true) ? 1 : 0
                );
            }
        }

        return array(
            'count'  => $cnt,
            'result' => $ret
        );
    }
}

?>
