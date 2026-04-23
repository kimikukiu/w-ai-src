<?php
include("Class/header.php");
?>
<div class="content d-flex flex-column flex-column-fluid">

<div class="container-xxl">

<div class="row g-5 g-xl-8 ">
<div class="col-xl-4">

<div class="card bg-light-primary card-xl-stretch mb-xl-8">

<div class="card-body p-0">
<div class="d-flex flex-stack card-p flex-grow-1">
<span class="symbol symbol-50px me-2">
<span class="symbol-label bg-primary">
<!--begin::FontAwesome Icons-->
<i class="fas fa-users text-white"></i>
</span>
</span>
<div class="d-flex flex-column text-end">
<span class="text-dark fw-bolder fs-2" data-kt-countup="true" data-kt-countup-value="<?php echo $stats -> activeusers($odb); ?>">0</span>
<span class="text-primary fw-bold mt-1">Total Clients</span>
</div>
</div>
</div>

</div>

</div>
<div class="col-xl-4">

<div class="card bg-light-primary card-xl-stretch mb-xl-8">

<div class="card-body p-0">
<div class="d-flex flex-stack card-p flex-grow-1">
<span class="symbol symbol-50px me-2">
<span class="symbol-label bg-primary">
<!--begin::FontAwesome Icons-->
<i class="fas fa-cloud text-white"></i>
</span>
</span>
<div class="d-flex flex-column text-end">
<span class="text-dark fw-bolder fs-2" data-kt-countup="true" data-kt-countup-value="0"><?php echo $stats -> totalBoots($odb); ?></span>
<span class="text-primary fw-bold mt-1">Total Attacks</span>
</div>
</div>
</div>

</div>

</div>
<div class="col-xl-4">

<div class="card bg-light-primary card-xl-stretch mb-xl-8">

<div class="card-body p-0">
<div class="d-flex flex-stack card-p flex-grow-1">
<span class="symbol symbol-50px me-2">
<span class="symbol-label bg-primary">
<!--begin::FontAwesome Icons-->
<i class="fas fa-bolt text-white"></i>
</span>
</span>

<div class="d-flex flex-column text-end">
<span class="text-white fw-bolder fs-2" data-kt-countup="true" data-kt-countup-value="<?php echo $stats -> RunningBoots($odb); ?>"></span>
<span class="text-primary fw-bold mt-1">Active Attacks</span>
</div>
</div>
</div>

</div>

</div>
</div>
<div class="row g-5 g-xl-8 mb-5 mb-xl-0">

<div class="col-xl-6">

<div style="height: 502px; overflow-y: scroll;">


<div class="col-xl-12 mb-5 mb-xl-10">
                                    <!--begin::Body-->
                                        <?php
													$newssql = $odb -> query("SELECT * FROM `news` ORDER BY `date` DESC LIMIT 2");
													while($row = $newssql ->fetch())
													{
													$id = $row['ID'];
													$title = $row['title'];
													$content = $row['content'];
													$date = date("m/d/Y", $row['date']);


                                                    echo ' <div class="card-body pt-0">
													<!--begin::Item-->
													<div class="d-flex bg-light-primary align-items-center rounded p-5 mb-7">
			                                                          <!--begin::FontAwesome Icons-->
                                                                      <span class="icon-item  m-3">
														<i style="color:#0095E8" class="fas fa-bolt fs-2x"></i>
														</span>
														<!--end::FontAwesome Icons-->
														<!--begin::Title-->
														<div class="flex-grow-1 me-2">
															<a class="fw-bold text-white fs-6">'.htmlspecialchars($title).'</a>
															<span class="fw-bold text-primary d-block">'.htmlspecialchars($content).'</span>
														</div>
														<!--end::Title-->
														<!--begin::Lable-->
														<span class="fw-bold text-white py-1">'.$date.'</span>
														<!--end::Lable-->
													</div>
													';
												}
												?>
													<!--end::Item-->
												</div>

</div>

</div>
</div>
</div>

<div class="col-xl-6">

									<!--begin::Slider Widget 3-->
									<div id="kt_sliders_widget_3_slider" class="card card-flush carousel slide h-xl-100" data-bs-ride="carousel" data-bs-interval="5000">
										<!--begin::Header-->
										<div class="card-header pt-5 mb-5">
											<!--begin::Title-->
											<h3 class="card-title align-items-start flex-column">
												<span class="card-label fw-bold text-dark">Client History</span>
												<span class="text-gray-400 mt-1 fw-semibold fs-7">This Shows The client's live & all out attack history</span>
											</h3>
											<!--end::Title-->
										</div>
										<!--end::Header-->
										<?php
                                         $attacks = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `handler` LIKE '%$name%' AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0 AND `user` = '" . $_SESSION['username'] . "'")->fetchColumn(0);
                                         $load = round($attacks / $maxattacks * 100, 2);
                                         ?>
										<!--begin::Body-->
										<div class="card-body p-0">
											<!--begin::Carousel-->
											<div class="carousel-inner">
												<!--begin::Item-->
												<div class="carousel-item active show">
													<!--begin::Statistics-->
													<div class="d-flex align-items-center w-100 px-8">
														<!--begin::Number-->
														<span class="fs-2qx text-gray-800 fw-bold"><?php echo htmlspecialchars($my_running_attacks); ?></span>
														<!--end::Number-->
														<!--begin::Progress-->
														<div class="progress h-6px w-100 mx-3 bg-light-primary">
															<div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $load . '%'; ?>" aria-valuenow="62" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<!--end::Progress-->
														<!--begin::Value-->
														<span class="text-gray-400 fw-bold fs-4"><?php echo $load . '%'; ?></span>
														<!--end::Value-->
													</div>
													<!--end::Statistics-->
													<!--begin::Chart-->
													<div id="kt_sliders_widget_3_chart_1" class="min-h-auto ps-4 pe-6" style="height: 330px"></div>
													<!--end::Chart-->
												</div>
												<!--end::Item-->
												
											</div>
											<!--end::Carousel-->
										</div>
										<!--end::Body-->
									</div>
									<!--end::Slider Widget 3-->
								</div>
								<!--end::Col-->


<div class="card-body">

<div style="height: 350px; min-height: 365px;">

</div>

</div>

</div>

</div>
</div>

				<script>
						// Class definition
var KTSlidersWidget3 = function () {
    var chart1 = {
        self: null,
        rendered: false
    };

    var chart2 = {
        self: null,
        rendered: false
    };

    // Private methods
    var initChart = function(chart, query, color, data) {
        var element = document.querySelector(query);

        if (!element) {
            return;
        }
        
        if ( chart.rendered === true && element.classList.contains("initialized") ) {
            return;
        }

        var height = parseInt(KTUtil.css(element, 'height'));
        var labelColor = KTUtil.getCssVariableValue('--kt-gray-500');
        var borderColor = KTUtil.getCssVariableValue('--kt-border-dashed-color');
        var baseColor = KTUtil.getCssVariableValue('--kt-' + color);

        var options = {
            series: [{
                name: 'Attacks',
                data: data
            }],            
            chart: {
                fontFamily: 'inherit',
                type: 'area',
                height: height,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {

            },
            legend: {
                show: false
            },
            dataLabels: {
                enabled: false
            },
            fill: {
                type: "gradient",
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0,
                    stops: [0, 80, 100]
                }
            },
            stroke: {
                curve: 'smooth',
                show: true,
                width: 3,
                colors: [baseColor]
            },
            xaxis: {
                categories: ['', 'Apr 05', 'Apr 06', 'Apr 07', 'Apr 08', 'Apr 09', 'Apr 11', 'Apr 12', 'Apr 14', 'Apr 15', 'Apr 16', 'Apr 17', 'Apr 18', ''],
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false
                },
                tickAmount: 6,
                labels: {
                    rotate: 0,
                    rotateAlways: true,
                    style: {
                        colors: labelColor,
                        fontSize: '12px'
                    }
                },
                crosshairs: {
                    position: 'front',
                    stroke: {
                        color: baseColor,
                        width: 1,
                        dashArray: 3
                    }
                },
                tooltip: {
                    enabled: true,
                    formatter: undefined,
                    offsetY: 0,
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                tickAmount: 4,
                max: 24,
                min: 10,
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '12px'
                    } 
                }
            },
            states: {
                normal: {
                    filter: {
                        type: 'none',
                        value: 0
                    }
                },
                hover: {
                    filter: {
                        type: 'none',
                        value: 0
                    }
                },
                active: {
                    allowMultipleDataPointsSelection: false,
                    filter: {
                        type: 'none',
                        value: 0
                    }
                }
            },
            tooltip: {
                style: {
                    fontSize: '12px'
                } 
            },
            colors: [baseColor],
            grid: {
                borderColor: borderColor,
                strokeDashArray: 4,
                yaxis: {
                    lines: {
                        show: true
                    }
                }
            },
            markers: {
                strokeColor: baseColor,
                strokeWidth: 3
            }
        };

        chart.self = new ApexCharts(element, options);
        chart.self.render();
        chart.rendered = true;

        element.classList.add('initialized');   
    }

    // Public methods
    return {
        init: function () {
            var data1 = [<?php echo htmlspecialchars($total_my_attacks); ?>];
			var data2 = [<?php echo htmlspecialchars($total_my_attacks); ?>];
            
            // Init default chart
            initChart(chart1, '#kt_sliders_widget_3_chart_1', 'primary', data1);

            var carousel = document.querySelector('#kt_sliders_widget_3_slider');

            if ( !carousel ){
                return;
            }
            
            carousel.addEventListener('slid.bs.carousel', function (e) {
                if (e.to === 1) {
                    // Init second chart
                    initChart(chart2, '#kt_sliders_widget_3_chart_2', 'primary', data2);
                }                
            });

            // Update chart on theme mode change
            KTThemeMode.on("kt.thememode.change", function() {                
                if (chart1.rendered) {
                    chart1.self.destroy();
                    chart1.rendered = false;
                }

                if (chart2.rendered) {
                    chart2.self.destroy();
                    chart2.rendered = false;
                }

                initChart(chart1, '#kt_sliders_widget_3_chart_1', 'danger', data1);
                initChart(chart2, '#kt_sliders_widget_3_chart_2', 'primary', data2);
            });
        }   
    }
}();

// Webpack support
if (typeof module !== 'undefined') {
    module.exports = KTSlidersWidget3;
}

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTSlidersWidget3.init();
});

"use strict";

// Class definition
var KTTablesWidget14 = function () {
    var chart1 = {
        self: null,
        rendered: false
    };

    var chart2 = {
        self: null,
        rendered: false
    };

    var chart3 = {
        self: null,
        rendered: false
    };

    var chart4 = {
        self: null,
        rendered: false
    };

    var chart5 = {
        self: null,
        rendered: false
    };

    // Private methods
    var initChart = function(chart, chartSelector, data, initByDefault) {
        var element = document.querySelector(chartSelector);

        if (!element) {
            return;
        }
        
        var height = parseInt(KTUtil.css(element, 'height'));
        var color = element.getAttribute('data-kt-chart-color');
       
        var strokeColor = KTUtil.getCssVariableValue('--kt-' + 'gray-300');
        var baseColor = KTUtil.getCssVariableValue('--kt-' + color);
        var lightColor = KTUtil.getCssVariableValue('--kt-body-bg');

        var options = {
            series: [{
                name: 'Net Profit',
                data: data
            }],
            chart: {
                fontFamily: 'inherit',
                type: 'area',
                height: height,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                },
                sparkline: {
                    enabled: true
                }
            },
            plotOptions: {},
            legend: {
                show: false
            },
            dataLabels: {
                enabled: false
            },
            fill: {
                type: 'solid',
                opacity: 1
            },
            stroke: {
                curve: 'smooth',
                show: true,
                width: 2,
                colors: [baseColor]
            },
            xaxis: {                 
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false
                },
                labels: {
                    show: false                   
                },
                crosshairs: {
                    show: false,
                    position: 'front',
                    stroke: {
                        color: strokeColor,
                        width: 1,
                        dashArray: 3
                    }
                },
                tooltip: {
                    enabled: false
                }
            },
            yaxis: {
                min: 0,
                max: 60,
                labels: {
                    show: false                     
                }
            },
            states: {
                normal: {
                    filter: {
                        type: 'none',
                        value: 0
                    }
                },
                hover: {
                    filter: {
                        type: 'none',
                        value: 0
                    }
                },
                active: {
                    allowMultipleDataPointsSelection: false,
                    filter: {
                        type: 'none',
                        value: 0
                    }
                }
            },
            tooltip: {
                enabled: false
            },
            colors: [lightColor],
            markers: {
                colors: [lightColor],
                strokeColor: [baseColor],
                strokeWidth: 3
            }
        };

        chart.self = new ApexCharts(element, options);          
        
        if (initByDefault === true) {
            // Set timeout to properly get the parent elements width
            setTimeout(function() {
                chart.self.render();  
                chart.rendered = true;
            }, 200);
        }            // Class definition
var KTSlidersWidget3 = function () {
    var chart1 = {
        self: null,
        rendered: false
    };

    var chart2 = {
        self: null,
        rendered: false
    };

    // Private methods
    var initChart = function(chart, query, color, data) {
        var element = document.querySelector(query);

        if (!element) {
            return;
        }
        
        if ( chart.rendered === true && element.classList.contains("initialized") ) {
            return;
        }

        var height = parseInt(KTUtil.css(element, 'height'));
        var labelColor = KTUtil.getCssVariableValue('--kt-gray-500');
        var borderColor = KTUtil.getCssVariableValue('--kt-border-dashed-color');
        var baseColor = KTUtil.getCssVariableValue('--kt-' + color);

        var options = {
            series: [{
                name: 'Lessons',
                data: data
            }],            
            chart: {
                fontFamily: 'inherit',
                type: 'area',
                height: height,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {

            },
            legend: {
                show: false
            },
            dataLabels: {
                enabled: false
            },
            fill: {
                type: "gradient",
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0,
                    stops: [0, 80, 100]
                }
            },
            stroke: {
                curve: 'smooth',
                show: true,
                width: 3,
                colors: [baseColor]
            },
            xaxis: {
                categories: ['', 'Apr 05', 'Apr 06', 'Apr 07', 'Apr 08', 'Apr 09', 'Apr 11', 'Apr 12', 'Apr 14', 'Apr 15', 'Apr 16', 'Apr 17', 'Apr 18', ''],
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false
                },
                tickAmount: 6,
                labels: {
                    rotate: 0,
                    rotateAlways: true,
                    style: {
                        colors: labelColor,
                        fontSize: '12px'
                    }
                },
                crosshairs: {
                    position: 'front',
                    stroke: {
                        color: baseColor,
                        width: 1,
                        dashArray: 3
                    }
                },
                tooltip: {
                    enabled: true,
                    formatter: undefined,
                    offsetY: 0,
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                tickAmount: 4,
                max: 24,
                min: 10,
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '12px'
                    } 
                }
            },
            states: {
                normal: {
                    filter: {
                        type: 'none',
                        value: 0
                    }
                },
                hover: {
                    filter: {
                        type: 'none',
                        value: 0
                    }
                },
                active: {
                    allowMultipleDataPointsSelection: false,
                    filter: {
                        type: 'none',
                        value: 0
                    }
                }
            },
            tooltip: {
                style: {
                    fontSize: '12px'
                } 
            },
            colors: [baseColor],
            grid: {
                borderColor: borderColor,
                strokeDashArray: 4,
                yaxis: {
                    lines: {
                        show: true
                    }
                }
            },
            markers: {
                strokeColor: baseColor,
                strokeWidth: 3
            }
        };

        chart.self = new ApexCharts(element, options);
        chart.self.render();
        chart.rendered = true;

        element.classList.add('initialized');   
    }

    // Public methods
    return {
        init: function () {
            var data1 = [19, 21, 21, 20, 20, 18, 18, 20, 20, 22, 22, 21, 21, 22];
            var data2 = [18, 22, 22, 20, 20, 18, 18, 20, 20, 18, 18, 20, 20, 22];
            
            // Init default chart
            initChart(chart1, '#kt_sliders_widget_3_chart_1', 'danger', data1);

            var carousel = document.querySelector('#kt_sliders_widget_3_slider');

            if ( !carousel ){
                return;
            }
            
            carousel.addEventListener('slid.bs.carousel', function (e) {
                if (e.to === 1) {
                    // Init second chart
                    initChart(chart2, '#kt_sliders_widget_3_chart_2', 'primary', data2);
                }                
            });

            // Update chart on theme mode change
            KTThemeMode.on("kt.thememode.change", function() {                
                if (chart1.rendered) {
                    chart1.self.destroy();
                    chart1.rendered = false;
                }

                if (chart2.rendered) {
                    chart2.self.destroy();
                    chart2.rendered = false;
                }

                initChart(chart1, '#kt_sliders_widget_3_chart_1', 'danger', data1);
                initChart(chart2, '#kt_sliders_widget_3_chart_2', 'primary', data2);
            });
        }   
    }
}();

// Webpack support
if (typeof module !== 'undefined') {
    module.exports = KTSlidersWidget3;
}

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTSlidersWidget3.init();
});

"use strict";

// Class definition
var KTTablesWidget14 = function () {
    var chart1 = {
        self: null,
        rendered: false
    };

    var chart2 = {
        self: null,
        rendered: false
    };

    var chart3 = {
        self: null,
        rendered: false
    };

    var chart4 = {
        self: null,
        rendered: false
    };

    var chart5 = {
        self: null,
        rendered: false
    };

    // Private methods
    var initChart = function(chart, chartSelector, data, initByDefault) {
        var element = document.querySelector(chartSelector);

        if (!element) {
            return;
        }
        
        var height = parseInt(KTUtil.css(element, 'height'));
        var color = element.getAttribute('data-kt-chart-color');
       
        var strokeColor = KTUtil.getCssVariableValue('--kt-' + 'gray-300');
        var baseColor = KTUtil.getCssVariableValue('--kt-' + color);
        var lightColor = KTUtil.getCssVariableValue('--kt-body-bg');

        var options = {
            series: [{
                name: 'Net Profit',
                data: data
            }],
            chart: {
                fontFamily: 'inherit',
                type: 'area',
                height: height,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                },
                sparkline: {
                    enabled: true
                }
            },
            plotOptions: {},
            legend: {
                show: false
            },
            dataLabels: {
                enabled: false
            },
            fill: {
                type: 'solid',
                opacity: 1
            },
            stroke: {
                curve: 'smooth',
                show: true,
                width: 2,
                colors: [baseColor]
            },
            xaxis: {                 
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false
                },
                labels: {
                    show: false                   
                },
                crosshairs: {
                    show: false,
                    position: 'front',
                    stroke: {
                        color: strokeColor,
                        width: 1,
                        dashArray: 3
                    }
                },
                tooltip: {
                    enabled: false
                }
            },
            yaxis: {
                min: 0,
                max: 60,
                labels: {
                    show: false                     
                }
            },
            states: {
                normal: {
                    filter: {
                        type: 'none',
                        value: 0
                    }
                },
                hover: {
                    filter: {
                        type: 'none',
                        value: 0
                    }
                },
                active: {
                    allowMultipleDataPointsSelection: false,
                    filter: {
                        type: 'none',
                        value: 0
                    }
                }
            },
            tooltip: {
                enabled: false
            },
            colors: [lightColor],
            markers: {
                colors: [lightColor],
                strokeColor: [baseColor],
                strokeWidth: 3
            }
        };

        chart.self = new ApexCharts(element, options);          
        
        if (initByDefault === true) {
            // Set timeout to properly get the parent elements width
            setTimeout(function() {
                chart.self.render();  
                chart.rendered = true;
            }, 200);
        }            
    }

    // Public methods
    return {
        init: function () { 
            var chart1Data = [7, 10, 5, 21, 6, 11, 5, 23, 5, 11, 18, 7, 21,13];            
            initChart(chart1, '#kt_table_widget_14_chart_1', chart1Data, true);

            var chart2Data = [17, 5, 23, 2, 21, 9, 17, 23, 4, 24, 9, 17, 21,7];            
            initChart(chart2, '#kt_table_widget_14_chart_2', chart2Data, true);

            var chart3Data = [2, 24, 5, 17, 7, 2, 12, 24, 5, 24, 2, 8, 12,7];            
            initChart(chart3, '#kt_table_widget_14_chart_3', chart3Data, true);

            var chart4Data = [24, 3, 5, 19, 3, 7, 25, 14, 5, 14, 2, 8, 5,17];            
            initChart(chart4, '#kt_table_widget_14_chart_4', chart4Data, true);

            var chart5Data = [3, 23, 1, 19, 3, 17, 3, 9, 25, 4, 2, 18, 25,3];            
            initChart(chart5, '#kt_table_widget_14_chart_5', chart5Data, true); 

            // Update chart on theme mode change
            KTThemeMode.on("kt.thememode.change", function() {
                if (chart1.rendered) {
                    chart1.self.destroy();
                }

                if (chart2.rendered) {
                    chart2.self.destroy();
                }

                if (chart3.rendered) {
                    chart3.self.destroy();
                }

                if (chart4.rendered) {
                    chart4.self.destroy();
                }

                if (chart5.rendered) {
                    chart5.self.destroy();
                }

                initChart(chart1, '#kt_table_widget_14_chart_1', chart1Data, chart1.rendered);
                initChart(chart2, '#kt_table_widget_14_chart_2', chart2Data, chart2.rendered);  
                initChart(chart3, '#kt_table_widget_14_chart_3', chart3Data, chart3.rendered);
                initChart(chart4, '#kt_table_widget_14_chart_4', chart4Data, chart4.rendered); 
                initChart(chart5, '#kt_table_widget_14_chart_5', chart5Data, chart5.rendered); 
            });
        }   
    }
}();
    }

    // Public methods
    return {
        init: function () { 
            var chart1Data = [7, 10, 5, 21, 6, 11, 5, 23, 5, 11, 18, 7, 21,13];            
            initChart(chart1, '#kt_table_widget_14_chart_1', chart1Data, true);

            var chart2Data = [17, 5, 23, 2, 21, 9, 17, 23, 4, 24, 9, 17, 21,7];            
            initChart(chart2, '#kt_table_widget_14_chart_2', chart2Data, true);

            var chart3Data = [2, 24, 5, 17, 7, 2, 12, 24, 5, 24, 2, 8, 12,7];            
            initChart(chart3, '#kt_table_widget_14_chart_3', chart3Data, true);

            var chart4Data = [24, 3, 5, 19, 3, 7, 25, 14, 5, 14, 2, 8, 5,17];            
            initChart(chart4, '#kt_table_widget_14_chart_4', chart4Data, true);

            var chart5Data = [3, 23, 1, 19, 3, 17, 3, 9, 25, 4, 2, 18, 25,3];            
            initChart(chart5, '#kt_table_widget_14_chart_5', chart5Data, true); 

            // Update chart on theme mode change
            KTThemeMode.on("kt.thememode.change", function() {
                if (chart1.rendered) {
                    chart1.self.destroy();
                }

                if (chart2.rendered) {
                    chart2.self.destroy();
                }

                if (chart3.rendered) {
                    chart3.self.destroy();
                }

                if (chart4.rendered) {
                    chart4.self.destroy();
                }

                if (chart5.rendered) {
                    chart5.self.destroy();
                }

                initChart(chart1, '#kt_table_widget_14_chart_1', chart1Data, chart1.rendered);
                initChart(chart2, '#kt_table_widget_14_chart_2', chart2Data, chart2.rendered);  
                initChart(chart3, '#kt_table_widget_14_chart_3', chart3Data, chart3.rendered);
                initChart(chart4, '#kt_table_widget_14_chart_4', chart4Data, chart4.rendered); 
                initChart(chart5, '#kt_table_widget_14_chart_5', chart5Data, chart5.rendered); 
            });
        }   
    }
}();
						</script>
