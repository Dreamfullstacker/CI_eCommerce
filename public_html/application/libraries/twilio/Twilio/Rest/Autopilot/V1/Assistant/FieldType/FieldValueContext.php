<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Autopilot\V1\Assistant\FieldType;

use Twilio\InstanceContext;
use Twilio\Values;
use Twilio\Version;

/**
 * PLEASE NOTE that this class contains preview products that are subject to change. Use them with caution. If you currently do not have developer preview access, please contact help@twilio.com.
 */
class FieldValueContext extends InstanceContext {
    /**
     * Initialize the FieldValueContext
     * 
     * @param \Twilio\Version $version Version that contains the resource
     * @param string $assistantSid The SID of the Assistant that is the parent of
     *                             the FieldType associated with the resource to
     *                             fetch
     * @param string $fieldTypeSid The SID of the Field Type associated with  the
     *                             Field Value to fetch
     * @param string $sid The unique string that identifies the resource
     * @return \Twilio\Rest\Autopilot\V1\Assistant\FieldType\FieldValueContext 
     */
    public function __construct(Version $version, $assistantSid, $fieldTypeSid, $sid) {
        parent::__construct($version);

        // Path Solution
        $this->solution = array(
            'assistantSid' => $assistantSid,
            'fieldTypeSid' => $fieldTypeSid,
            'sid' => $sid,
        );

        $this->uri = '/Assistants/' . rawurlencode($assistantSid) . '/FieldTypes/' . rawurlencode($fieldTypeSid) . '/FieldValues/' . rawurlencode($sid) . '';
    }

    /**
     * Fetch a FieldValueInstance
     * 
     * @return FieldValueInstance Fetched FieldValueInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function fetch() {
        $params = Values::of(array());

        $payload = $this->version->fetch(
            'GET',
            $this->uri,
            $params
        );

        return new FieldValueInstance(
            $this->version,
            $payload,
            $this->solution['assistantSid'],
            $this->solution['fieldTypeSid'],
            $this->solution['sid']
        );
    }

    /**
     * Deletes the FieldValueInstance
     * 
     * @return boolean True if delete succeeds, false otherwise
     * @throws TwilioException When an HTTP error occurs.
     */
    public function delete() {
        return $this->version->delete('delete', $this->uri);
    }

    /**
     * Provide a friendly representation
     * 
     * @return string Machine friendly representation
     */
    public function __toString() {
        $context = array();
        foreach ($this->solution as $key => $value) {
            $context[] = "$key=$value";
        }
        return '[Twilio.Autopilot.V1.FieldValueContext ' . implode(' ', $context) . ']';
    }
}