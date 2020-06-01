/*
 *  Document   : base_comp_charts.js
 *  Author     : pixelcave
 *  Description: Custom JS code used in Charts Page
 */

var BaseCompCharts = function() {
    // Chart.js Charts, for more examples you can check out http://www.chartjs.org/docs
    var initChartsChartJS = function () {};

    // jQuery Sparkline Charts, for more examples you can check out http://omnipotent.net/jquery.sparkline/#s-docs
    var initChartsSparkline = function(){};

    // Randomize Easy Pie Chart values
    var initRandomEasyPieChart = function(){};

    // Flot charts, for more examples you can check out http://www.flotcharts.org/flot/examples/
    var initChartsFlot = function(){};

    return {
        init: function () {
            // Init all charts
            initChartsChartJS();
            initChartsSparkline();
            initChartsFlot();

            // Randomize Easy Pie values functionality
            initRandomEasyPieChart();
        }
    };
}();

// Initialize when page loads
jQuery(function(){ BaseCompCharts.init(); });