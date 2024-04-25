<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/api/quota.proto

namespace Google\Api;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Quota configuration helps to achieve fairness and budgeting in service
 * usage.
 * The quota configuration works this way:
 * - The service configuration defines a set of metrics.
 * - For API calls, the quota.metric_rules maps methods to metrics with
 *   corresponding costs.
 * - The quota.limits defines limits on the metrics, which will be used for
 *   quota checks at runtime.
 * An example quota configuration in yaml format:
 *    quota:
 *      limits:
 *      - name: apiWriteQpsPerProject
 *        metric: Library.googleapis.com/write_calls
 *        unit: "1/min/{project}"  # rate limit for consumer projects
 *        values:
 *          STANDARD: 10000
 *      # The metric rules bind all methods to the read_calls metric,
 *      # except for the UpdateBook and DeleteBook methods. These two methods
 *      # are mapped to the write_calls metric, with the UpdateBook method
 *      # consuming at twice rate as the DeleteBook method.
 *      metric_rules:
 *      - selector: "*"
 *        metric_costs:
 *          Library.googleapis.com/read_calls: 1
 *      - selector: google.example.Library.v1.LibraryService.UpdateBook
 *        metric_costs:
 *          Library.googleapis.com/write_calls: 2
 *      - selector: google.example.Library.v1.LibraryService.DeleteBook
 *        metric_costs:
 *          Library.googleapis.com/write_calls: 1
 *  Corresponding Metric definition:
 *      metrics:
 *      - name: Library.googleapis.com/read_calls
 *        display_name: Read requests
 *        metric_kind: DELTA
 *        value_type: INT64
 *      - name: Library.googleapis.com/write_calls
 *        display_name: Write requests
 *        metric_kind: DELTA
 *        value_type: INT64
 *
 * Generated from protobuf message <code>google.api.Quota</code>
 */
class Quota extends \Google\Protobuf\Internal\Message
{
    /**
     * List of `QuotaLimit` definitions for the service.
     * Used by metric-based quotas only.
     *
     * Generated from protobuf field <code>repeated .google.api.QuotaLimit limits = 3;</code>
     */
    private $limits;
    /**
     * List of `MetricRule` definitions, each one mapping a selected method to one
     * or more metrics.
     * Used by metric-based quotas only.
     *
     * Generated from protobuf field <code>repeated .google.api.MetricRule metric_rules = 4;</code>
     */
    private $metric_rules;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \Google\Api\QuotaLimit[]|\Google\Protobuf\Internal\RepeatedField $limits
     *           List of `QuotaLimit` definitions for the service.
     *           Used by metric-based quotas only.
     *     @type \Google\Api\MetricRule[]|\Google\Protobuf\Internal\RepeatedField $metric_rules
     *           List of `MetricRule` definitions, each one mapping a selected method to one
     *           or more metrics.
     *           Used by metric-based quotas only.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Google\Api\Quota::initOnce();
        parent::__construct($data);
    }

    /**
     * List of `QuotaLimit` definitions for the service.
     * Used by metric-based quotas only.
     *
     * Generated from protobuf field <code>repeated .google.api.QuotaLimit limits = 3;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getLimits()
    {
        return $this->limits;
    }

    /**
     * List of `QuotaLimit` definitions for the service.
     * Used by metric-based quotas only.
     *
     * Generated from protobuf field <code>repeated .google.api.QuotaLimit limits = 3;</code>
     * @param \Google\Api\QuotaLimit[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setLimits($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Google\Api\QuotaLimit::class);
        $this->limits = $arr;

        return $this;
    }

    /**
     * List of `MetricRule` definitions, each one mapping a selected method to one
     * or more metrics.
     * Used by metric-based quotas only.
     *
     * Generated from protobuf field <code>repeated .google.api.MetricRule metric_rules = 4;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getMetricRules()
    {
        return $this->metric_rules;
    }

    /**
     * List of `MetricRule` definitions, each one mapping a selected method to one
     * or more metrics.
     * Used by metric-based quotas only.
     *
     * Generated from protobuf field <code>repeated .google.api.MetricRule metric_rules = 4;</code>
     * @param \Google\Api\MetricRule[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setMetricRules($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Google\Api\MetricRule::class);
        $this->metric_rules = $arr;

        return $this;
    }

}

