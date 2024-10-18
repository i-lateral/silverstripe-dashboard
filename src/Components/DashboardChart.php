<?php

namespace Sunnysideup\Dashboard\Components;

use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStripe\View\ViewableData;

/**
 * An API for creating a Google chart in a dashboard panel
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package Dashboard
 */
class DashboardChart extends ViewableData
{
    /**
     * @var int A count of the instances, used to create a unique ID for the chart
     */
    private static $instances = 0;

    /**
     * @var array The chart data, in x/y pairs. The Y value must be an integer
     */
    protected $chartData = [];

    /**
     * @var int The number of points between each text label on the X axis
     */
    public $TextInterval = 5;

    /**
     * @var int The height of the chart in pixels
     */
    public $Height = 200;

    /**
     * @var int The size of the circle on each data point, in pixels
     */
    public $PointSize = 5;

    /**
     * @var int The font size on the chart
     */
    public $FontSize = 10;

    /**
     * @var string The position of the text on the chart
     */
    public $TextPosition = 'in';

    /**
     * Creates a new instance of a DashboardChart
     *
     * @param  string The title of the chart
     * @param  string The label of the X axis
     * @param  string The label for the Y axis
     * @param  array The chart data, in x/y pairs
     * @return DashboardChart
     */
    public static function create(...$args)
    {
        list($title, $x_label, $y_label, $chartData) = $args;

        if ($chartData === null) {
			$chartData = [];
        }

        self::$instances++;

		return parent::create($title, $x_label, $y_label, $chartData);
    }

    /**
     * Constructor for the DashboardChart
     *
     * @param string The title of the chart
     * @param string The label of the X axis
     * @param string The label for the Y axis
     * @param array The chart data, in x/y pairs
     */
    public function __construct(
        $title = null,
        $x_label = null,
        $y_label = null,
        $chartData = []
    ) {
        if (!is_array($chartData)) {
            user_error("DashboardChart: \$chartData must be an array", E_USER_ERROR);
        }

        $this->chartData = $chartData;
        $this->Title = $title;
        $this->YAxisLabel = $y_label;
        $this->XAxisLabel = $x_label;
    }

    /**
     * The ID of the chart. Javascript needs to target a specific element
     *
     * @return string
     */
    public function getChartID()
    {
        return "dashboard-chart-".self::$instances;
    }

    /**
     * Gets a list of x/y pairs for the template
     *
     * @return ArrayList
     */
    public function getChartData(): ArrayList
    {
        $list = ArrayList::create();

        foreach ($this->chartData as $x => $y) {
            $list->push(
                ArrayData::create([
					'XValue' => $x,
					'YValue' => $y
				])
            );
        }

        return $list;
    }

    /**
     * Adds a single data point to the chart
     *
     * @param string The X value
     * @param int The Y value
     */
    public function addData($x, $y): self
    {
        $this->chartData[$x] = $y;
        return $this;
    }

    /**
     * Sets the chart data, in x/y pairs
     *
     * @param array The chart data
     */
    public function setData($data): self
    {
        $this->chartData = $data;
        return $this;
    }

    /**
     * Renders the chart and loads the dependencies
     *
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     */
    public function forTemplate()
    {
        return $this->renderWith($this->getViewerTemplates());
    }
}
