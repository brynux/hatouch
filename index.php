<?php include('header.php'); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<!-- <section class="content-header">
	<h1>
		Dashboard
		<small>Optional description</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
		<li class="active">Here</li>
	</ol>
	</section> -->

	<!-- Main content -->
	<h1 v-if="!entities">Loading...</h1>
	<section class="content" v-cloak>
	<div class="row">
		<div class="col-md-6">
			<div class="row">
				<div class="col-sm-6">
					<sensor entity_id="sensor.hallway_thermostat_temperature" title="Indoor Temperature" icon="fa-home" color-class="bg-red"></sensor>
				</div>
				<div class="col-sm-6">
					<sensor entity_id="sensor.aeotec_home_energy_meter_power_11_4" icon="fa-bolt" color-class="bg-yellow"></sensor>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<light entity_id="light.front_porch"></light>
				</div>
				<div class="col-sm-6">
					<light entity_id="light.living_room"></light>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div v-if="entities['sensor.pws_weather']" class="box box-widget widget-user">
				<!-- Add the bg color to the header using any of the bg-* classes -->
				<div class="widget-user-header bg-black" style="background: url('images/fountain.jpg') center center; background-size: cover; height: 108px;">
				<h3 class="widget-user-username" style="font-weight: bold;">Current Conditions</h3>
				<h5 class="widget-user-desc">Home</h5>
				</div>
				<div class="widget-user-image" style="top: 50px;">
				<img class="img-circle" :src="entities['sensor.pws_weather']['attributes']['entity_picture']" :alt="entities['sensor.pws_weather']['state']" style="background-color:white;">
				</div>
				<div class="box-footer" style="padding-top: 20px;">
					<div class="row">
						<div class="col-sm-4 border-right">
						<div class="description-block">
							<h5 class="description-header">{{entities['sensor.pws_temp_f']['state']}}{{entities['sensor.pws_temp_f']['attributes']['unit_of_measurement']}}</h5>
							<span class="description-text">TEMP</span>
						</div>
						</div>
						<div class="col-sm-4 border-right">
						<div class="description-block">
							<h5 class="description-header">{{entities['sensor.pws_relative_humidity']['state']}}{{entities['sensor.pws_relative_humidity']['attributes']['unit_of_measurement']}}</h5>
							<span class="description-text">HUMIDITY</span>
						</div>
						</div>
						<div class="col-sm-4">
						<div class="description-block">
							<h5 class="description-header">{{entities['sensor.pws_wind_dir']['state'].substring(0,1)}} {{entities['sensor.pws_wind_mph']['state']}}{{entities['sensor.pws_wind_mph']['attributes']['unit_of_measurement']}}</h5>
							<span class="description-text">WIND</span>
						</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-sm-6">
			<div class="row">
				<div class="col-sm-6">
					<light entity_id="light.back_porch"></light>
				</div>
				<div class="col-sm-6">
					<light entity_id="light.hallway"></light>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="box box-primary">
						<div class="box-header with-border">
						<h3 class="box-title">Temperature History</h3>
						</div>
						<div class="box-body">
						<div class="chart">
							<canvas id="areaChart" style="height: 128px;" height="128"></canvas>
						</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6">
			<action title="Baby's Bedtime" icon="fa-bed" description="Dims living room lights, turns on cabinet lights. Turns off other lights" entity_id="scene.baby_bedtime"></action>
			<action title="Goodbye" icon="fa-automobile" description="Turns on porch light for 3 minutes (after dark)." entity_id="script.goodbye_lights"></action>
			<action title="Goodnight" icon="fa-bed" description="Turns off all lights. Turns on dim cabinet lights." entity_id="script.goodnight"></action>
		</div>
	</div>

	</section>
	<!-- /.content -->
</div>

</div>

<script type="text/javascript">
$(document).ready(function() {
	/*
	HomeAssistantApi.getHistory('sensor.pws_temp_f', moment().subtract(24,'hours').format(), function(data) {
		var states = {};
		var labels = [];
		for (var i = 0; i < data.length; i++) {
			for (var j = 0; j < data[i].length; j++) {
				if (states[data[i][j]['entity_id']] == undefined) { states[data[i][j]['entity_id']] = [];}
				states[data[i][j]['entity_id']].push(parseFloat(data[i][j]['state']));
				labels.push(moment(data[i][j]['last_updated']).format('h:mm a'));
			}
		}
		areaChartData = {
		  labels: labels,
		  datasets: [
			{
			  label: "Outdoor Temperature",
  			  fillColor: "rgba(60,141,188,0.9)",
			  strokeColor: "rgba(60,141,188,0.8)",
			  pointColor: "#3b8bba",
			  pointStrokeColor: "rgba(60,141,188,1)",
			  pointHighlightFill: "#fff",
			  pointHighlightStroke: "rgba(60,141,188,1)",
			  data: states["sensor.pws_temp_f"]
			},
			{
			  label: "Indoor Temperature",
			  fillColor: "rgba(210, 214, 222, 1)",
			  strokeColor: "rgba(210, 214, 222, 1)",
			  pointColor: "rgba(210, 214, 222, 1)",
			  pointStrokeColor: "#c1c7d1",
			  pointHighlightFill: "#fff",
			  pointHighlightStroke: "rgba(220,220,220,1)",
			  data: states["sensor.hallway_thermostat_temperature"]
			}
		  ]
		};

		areaChartOptions = {
		  showScale: true,
		  pointDot: false,
		  pointHitDetectionRadius: 20,
		  legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
		  legend: {
			  display: false
		  },
		  elements: {
			  point: {
				  radius: 0
			  }
		  },
		  responsive: true,
		  maintainAspectRatio: false,
		  
		};
		
		//This has to be done after vue loads due to a vue bug...
	
		// Get context with jQuery - using jQuery's .get() method.
		var areaChartCanvas = $("#areaChart").get(0).getContext("2d");
		// This will get the first returned node in the jQuery collection.
		var areaChart = new Chart(areaChartCanvas, {
			type: 'line',
			data: areaChartData,
			options: areaChartOptions
		});
	});
	*/
});
</script>
<?php include('footer.php'); ?>