<?php

namespace Sabre\DAV\Xml\Property;

use Sabre\DAV;
use Sabre\Xml\Writer;
use Sabre\Xml\XmlSerializable;

/**
 * supported-method-set property.
 *
 * This property is defined in RFC3253, but since it's
 * so common in other webdav-related specs, it is part of the core server.
 *
 * This property is defined here:
 * http://tools.ietf.org/html/rfc3253#section-3.1.3
 *
 * @copyright Copyright (C) 2007-2015 fruux GmbH (https://fruux.com/).
 * @author Evert Pot (http://evertpot.com/) 
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class SupportedMethodSet implements XmlSerializable {

    /**
     * List of methods
     *
     * @var string[]
     */
    protected $methods = [];

    /**
     * Creates the property
     *
     * Any reports passed in the constructor
     * should be valid report-types in clark-notation.
     *
     * Either a string or an array of strings must be passed.
     *
     * @param string|string[] $methods
     */
    function __construct($methods = null) {

        $this->methods = (array)$methods;

    }

    /**
     * Returns the list of supported http methods.
     *
     * @return string[]
     */
    function getValue() {

        return $this->methods;

    }

    /**
     * Returns true or false if the property contains a specific method.
     *
     * @param string $methodName
     * @return bool
     */
    function has($methodName) {

        return in_array(
            $methodName,
            $this->methods
        );

    }

    /**
     * The xmlSerialize metod is called during xml writing.
     *
     * Use the $writer argument to write its own xml serialization.
     *
     * An important note: do _not_ create a parent element. Any element
     * implementing XmlSerializble should only ever write what's considered
     * its 'inner xml'.
     *
     * The parent of the current element is responsible for writing a
     * containing element.
     *
     * This allows serializers to be re-used for different element names.
     *
     * If you are opening new elements, you must also close them again.
     *
     * @param Writer $writer
     * @return void
     */
    function xmlSerialize(Writer $writer) {

        foreach($this->getValue() as $val) {
            $writer->startElement('{DAV:}supported-method');
            $writer->writeAttribute('name', $val);
            $writer->endElement();
        }

    }

}
