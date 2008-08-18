<?php

/**
 * Butler_Serialize_xml
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

require_once 'XML/Serializer.php';

/**
 * Butler_Serialize_xml
 *
 * @category  System
 * @package   Butler
 * @author    Joe Stump <joe@joestump.net> 
 * @copyright 2008 Digg, Inc.
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://code.google.com/p/digg
 * @see       Butler_Serialize, Butler::dispatch()
 */
class Butler_Serialize_xml
{
    /**
     * Serialize data into XML
     *
     * @param array $data The data to serialize
     *
     * @link http://pear.php.net/package/XML_Serialize
     * @return string
     */
    public function serialize(array $data)
    {
        $serializer = new XML_Serializer(array(
            XML_SERIALIZER_OPTION_INDENT => '    ',
            XML_SERIALIZER_OPTION_TYPEHINTS => true,
            XML_SERIALIZER_OPTION_XML_DECL_ENABLED => true,
            XML_SERIALIZER_OPTION_XML_ENCODING => 'UTF-8',
            XML_SERIALIZER_OPTION_ROOT_NAME => 'response',
            XML_SERIALIZER_OPTION_MODE => XML_SERIALIZER_MODE_SIMPLEXML,
            XML_SERIALIZER_OPTION_RETURN_RESULT => true
        ));

        return $serializer->serialize($data);
    }

    /**
     * Get HTTP Content-Type
     *
     * @return string
     */
    public function getContentType()
    {
        return 'application/xml';
    }
}

?>
