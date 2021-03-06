<?php
/**
 * PostUpdateInput
 *
 * PHP version 5
 *
 * @category Class
 * @package  Swagger\Client
 * @author   http://github.com/swagger-api/swagger-codegen
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache Licene v2
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * discussion
 *
 * No descripton provided (generated by Swagger Codegen https://github.com/swagger-api/swagger-codegen)
 *
 * OpenAPI spec version: 0.1.0-SNAPSHOT
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace Swagger\Client\Discussion\Models;

use \ArrayAccess;

/**
 * PostUpdateInput Class Doc Comment
 *
 * @category    Class */
/** 
 * @package     Swagger\Client
 * @author      http://github.com/swagger-api/swagger-codegen
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache Licene v2
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class PostUpdateInput implements ArrayAccess
{
    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'PostUpdateInput';

    /**
      * Array of property to type mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerTypes = array(
        'body' => 'string',
        'title' => 'string',
        'forum_id' => 'int',
        'open_graph' => '\Swagger\Client\Discussion\Models\OpenGraph',
        'content_images' => '\Swagger\Client\Discussion\Models\ContentImage[]'
    );

    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     * @var string[]
     */
    protected static $attributeMap = array(
        'body' => 'body',
        'title' => 'title',
        'forum_id' => 'forumId',
        'open_graph' => 'openGraph',
        'content_images' => 'contentImages'
    );

    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @var string[]
     */
    protected static $setters = array(
        'body' => 'setBody',
        'title' => 'setTitle',
        'forum_id' => 'setForumId',
        'open_graph' => 'setOpenGraph',
        'content_images' => 'setContentImages'
    );

    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @var string[]
     */
    protected static $getters = array(
        'body' => 'getBody',
        'title' => 'getTitle',
        'forum_id' => 'getForumId',
        'open_graph' => 'getOpenGraph',
        'content_images' => 'getContentImages'
    );

    public static function getters()
    {
        return self::$getters;
    }

    

    

    /**
     * Associative array for storing property values
     * @var mixed[]
     */
    protected $container = array();

    /**
     * Constructor
     * @param mixed[] $data Associated array of property value initalizing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['body'] = isset($data['body']) ? $data['body'] : null;
        $this->container['title'] = isset($data['title']) ? $data['title'] : null;
        $this->container['forum_id'] = isset($data['forum_id']) ? $data['forum_id'] : null;
        $this->container['open_graph'] = isset($data['open_graph']) ? $data['open_graph'] : null;
        $this->container['content_images'] = isset($data['content_images']) ? $data['content_images'] : null;
    }

    /**
     * show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalid_properties = array();
        if ($this->container['body'] === null) {
            $invalid_properties[] = "'body' can't be null";
        }
        if (!is_null($this->container['title']) && (strlen($this->container['title']) > 512)) {
            $invalid_properties[] = "invalid value for 'title', the character length must be smaller than or equal to 512.";
        }

        if (!is_null($this->container['title']) && (strlen($this->container['title']) < 0)) {
            $invalid_properties[] = "invalid value for 'title', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['forum_id']) && ($this->container['forum_id'] < 1.0)) {
            $invalid_properties[] = "invalid value for 'forum_id', must be bigger than or equal to 1.0.";
        }

        return $invalid_properties;
    }

    /**
     * validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properteis are valid
     */
    public function valid()
    {
        if ($this->container['body'] === null) {
            return false;
        }
        if (strlen($this->container['title']) > 512) {
            return false;
        }
        if (strlen($this->container['title']) < 0) {
            return false;
        }
        if ($this->container['forum_id'] < 1.0) {
            return false;
        }
        return true;
    }


    /**
     * Gets body
     * @return string
     */
    public function getBody()
    {
        return $this->container['body'];
    }

    /**
     * Sets body
     * @param string $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->container['body'] = $body;

        return $this;
    }

    /**
     * Gets title
     * @return string
     */
    public function getTitle()
    {
        return $this->container['title'];
    }

    /**
     * Sets title
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        if (strlen($title) > 512) {
            throw new \InvalidArgumentException('invalid length for $title when calling PostUpdateInput., must be smaller than or equal to 512.');
        }
        if (strlen($title) < 0) {
            throw new \InvalidArgumentException('invalid length for $title when calling PostUpdateInput., must be bigger than or equal to 0.');
        }
        $this->container['title'] = $title;

        return $this;
    }

    /**
     * Gets forum_id
     * @return int
     */
    public function getForumId()
    {
        return $this->container['forum_id'];
    }

    /**
     * Sets forum_id
     * @param int $forum_id
     * @return $this
     */
    public function setForumId($forum_id)
    {

        if ($forum_id < 1.0) {
            throw new \InvalidArgumentException('invalid value for $forum_id when calling PostUpdateInput., must be bigger than or equal to 1.0.');
        }
        $this->container['forum_id'] = $forum_id;

        return $this;
    }

    /**
     * Gets open_graph
     * @return \Swagger\Client\Discussion\Models\OpenGraph
     */
    public function getOpenGraph()
    {
        return $this->container['open_graph'];
    }

    /**
     * Sets open_graph
     * @param \Swagger\Client\Discussion\Models\OpenGraph $open_graph
     * @return $this
     */
    public function setOpenGraph($open_graph)
    {
        $this->container['open_graph'] = $open_graph;

        return $this;
    }

    /**
     * Gets content_images
     * @return \Swagger\Client\Discussion\Models\ContentImage[]
     */
    public function getContentImages()
    {
        return $this->container['content_images'];
    }

    /**
     * Sets content_images
     * @param \Swagger\Client\Discussion\Models\ContentImage[] $content_images
     * @return $this
     */
    public function setContentImages($content_images)
    {
        $this->container['content_images'] = $content_images;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     * @param  integer $offset Offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     * @param  integer $offset Offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     * @param  integer $offset Offset
     * @param  mixed   $value  Value to be set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     * @param  integer $offset Offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(\Swagger\Client\ObjectSerializer::sanitizeForSerialization($this), JSON_PRETTY_PRINT);
        }

        return json_encode(\Swagger\Client\ObjectSerializer::sanitizeForSerialization($this));
    }
}


