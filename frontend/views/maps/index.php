<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use conquer\select2\Select2Widget;

$this->title = Yii::t('app','Map');
$this->params['breadcrumbs'][] = $this->title;
?>
<div>
	<?php echo $this->render('/reports/_paramsfilter1.php', [ 'model'=>$model]); ?>
</div>

<div id="map" style="left:0%;width:100%;height:600px"></div>

<script>
	var myLatLng = {lat: 49.981, lng: 36.252};		//Координаты Харькова
	var map;
    var markers = [];
	var infowindow;
	var UIDiv1;
	var TicketsList = [];
	var markerCluster;
	var UseCluster = true;
	var MinMarkerLat = myLatLng.lat;
	var MinMarkerLng = myLatLng.lng;
	var MaxMarkerLat = myLatLng.lat;
	var MaxMarkerLng = myLatLng.lng;
	var MrkCountExecutet = 0;
	var MrkCountInWork = 0;
	var MrkCountOverdue = 0;
	var MrkCountOther = 0;
	var GMarkerIndex = 0;


	function initMap() {

		map = new google.maps.Map(document.getElementById('map'), {
			center: myLatLng,
			zoom: 11
			});

		AddPanel1();

 		markerCluster = new MarkerClusterer(map, markers, {
 			//imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
 			gridSize: 50,
 			averageCenter: true,
 			maxZoom: 15,
			styles: [{
					url: "/img/cluster-green.png",
					height: 55,
					width: 55,
					textSize:14
				},
				{
					url: "/img/cluster-blue.png",
					height: 55,
					width: 55,
					textSize:14
				},
				{
					url: "/img/cluster-red.png",
					height: 55,
					width: 55,
					textSize:14
				},
				{
					url: "/img/cluster-pinc.png",
					height: 55,
					width: 55,
					textSize:14
				},
				{
					url: "/img/cluster-orange.png",
					height: 55,
					width: 55,
					textSize:14
				}
			]
 		});
 		markerCluster.setCalculator(calc);
	}

	function AddPanel1(){
		var Panel1HTML = ''+
			'<div id="userUI1" style="width:150px">'+
				'<div class="row">'+
				'	<div class="col-md-8"> Кол-во заявок: </div>'+
				'	<div class="col-md-4"><b id="UI1cnt"> 0 </b></div>'+
				'</div>'+
				'<div class="row">'+
				'	<font color="#008000">'+
				'	<div class="col-md-8"> Выполнено:  </div>'+
				'	<div class="col-md-4"><b id="UI1cntExecutet"> 0 </b></div>'+
				'	</font>'+
				'</div>'+
				'<div class="row">'+
				'	<font color="#0000B0">'+
				'	<div class="col-md-8"> В работе:  </div> '+
				'	<div class="col-md-4"><b id="UI1cntInWork"> 0 </b></div>'+
				'	</font>'+
				'</div>'+
				'<div class="row">'+
				'	<font color="#C0000">'+
				'	<div class="col-md-8">Просрочено:  </div> '+
				'	<div class="col-md-4"><b id="UI1cntOverdue"> 0 </b></div>'+
				'	</font>'+
				'</div>'+
				'<input id="chkbxClusterUse" type="checkbox" '+
					' style=" position:relative; top:3px; " '+
					' checked onclick="ClusterUseKlik();"> &nbsp;&nbsp;&nbsp; Группировать </input>'+
				'</br>'+
				'<input id="chkbxRedrawAutoScale" type="checkbox" '+
					' style=" position:relative; top:3px; " '+
					' checked > &nbsp;&nbsp;&nbsp; Автомасштаб </input>'+
			'</div>'
		UIDiv1 = document.createElement('DIV');
		UIDiv1.index = 1;
		UIDiv1.style.display = 'block';
		UIDiv1.style.padding = '7px';
		UIDiv1.style.backgroundColor = 'white';
		//UIDiv1.style.borderStyle = 'solid';
		UIDiv1.style.borderWidth = '1px';
		UIDiv1.style.marginLeft = '10px';
		UIDiv1.innerHTML = Panel1HTML;
		map.controls[google.maps.ControlPosition.LEFT_TOP].push(UIDiv1);
	}

	function ClusterUseKlik(){
		var newUseCluster = document.getElementById('chkbxClusterUse').checked;
		if (newUseCluster != UseCluster){
			UseCluster = newUseCluster;
			if (UseCluster) markerCluster.addMarkers(markers,false);
			else {
				markerCluster.clearMarkers();
				markers.forEach(function(item){
					item.setMap( map );
				});
			}

		}
	}


	function OnMarkerClick(marker) {
		var ticket = TicketsList[marker.ticketindex];
		var contentString ='<div>'+
			'</div>'+
			'<div>'+
				'Заявка №'+
				'<a href="<?php echo Url::toRoute(['tickets/view']); ?>&id='+ ticket.id +'" target="_blank">'+
					'<h2 id="firstHeading" class="firstHeading">'+ ticket.ticode +'</h1>'+
				'</a>'+
				( ticket.ticoderemote ? '<p> №1562: <b><font color="#800080">' + ticket.ticoderemote +'</font></b> </p>':"") +
				'<p> Адрес: &nbsp;&nbsp;<b>' + ticket.tiaddress + ' </b> </p>' +
				'<p> Открыта: &nbsp;&nbsp;<b> '+ ticket.tiopenedtime +' </b> </p>'+
				'<p> Проблема: &nbsp;&nbsp;<b>'+ ticket.tidescription +'</b> </p>'+
				//'<p> Статус: &nbsp;&nbsp;<b>'+Yii::$app->params['TicketStatus'][marker.ticket.tidescription] +'</b> </p>'+
				'<p> Диспетчер: &nbsp;&nbsp;<b>'+( ticket.tioriginator ? ticket.tioriginator : " ") +'</b> </p>'+
				'<p> Участок: &nbsp;&nbsp;<b>'+( ticket.divisionname ? ticket.divisionname : " " ) +'</b> </p>'+
				'<p> Исполнитель: &nbsp;&nbsp;<b>'+( ticket.executant ? ticket.executant : " " ) +'</b> </p>'+
				//'<p> статус: <b>'+(marker.ticket.tistatus?marker.ticket.tistatus:" ??? ") +'</b> </p>'+
			'</div>';
		if (marker.tickcount > 1) {
			contentString += '<div  onclick=OnInfoTickListClick(this)>' +
							 '<img class="ITLHImg1" src="/img/list_open.png " />'+
							 '<img class="ITLHImg2" src="/img/list_close.png" style="display:none"/>'+
							 '&nbsp;&nbsp; Всего заявок этому адресу: &nbsp;&nbsp; <b>'+ (marker.tickcount) +' </b></div>';
			contentString += '<div class="infoticklist" style="display:none"><ul class="ul-tree">';
			TicketsList.forEach(function(tkt){
				if (tkt.MarkerIndex == marker.index) {
					contentString += '<li> '+
					'<a href="<?php echo Url::toRoute(['tickets/view']); ?>&id='+ tkt.id +'" target="_blank">'+
					tkt.ticode +'<a>'+
					' </li>';
				}
			});
			contentString += '</ul></div>';
			contentString += '<div>'+
				'<p> <a href="<?php echo Url::toRoute(["go-to-tikets-list"]);?>&facilityid='+(ticket.tifacility_id)+'&x'+filtrrequest+'" target="_blank">'+
					'Смотреть список в отчетах'+
				' </a>'+'<div>';
		}
		var InfoStr = '<div id="content">'+ contentString + '</div>';
		infowindow = new google.maps.InfoWindow({
			content: contentString
			});
		infowindow.open(map,marker);

	}
	function OnInfoTickListClick(element){
		console.log("click");
		if (  $(element).next('.infoticklist', element).css('display') == 'none' ) {
			$('.ITLHImg1',element).hide();
			$('.ITLHImg2',element).show();
		}else{
			$('.ITLHImg2',element).hide();
			$('.ITLHImg1',element).show();
		}
		$(element).next('.infoticklist', element).slideToggle();
	}

	function IsFirstTicketForFacility(index)
	{
		var res = true;
		if (0 == index)
			res = true;
		else
			if (( TicketsList[index-1].falatitude == TicketsList[index].falatitude ) &&
				( TicketsList[index-1].falongitude == TicketsList[index].falongitude ) ) {
					res = false;
					TicketsList[index].MarkerIndex = TicketsList[index-1].MarkerIndex;
				}
			else
				res = true;
		return res;
	}

	function GetMarkerParamForTickets(tindex)
	{
		var res = {color:0, count:1};
		var icolor = 0;

		res.color = GetTicketColor( TicketsList[tindex] );

		for (var i = tindex+1, len = TicketsList.length; i < len; ++i) {
			if (( TicketsList[i].falatitude == TicketsList[tindex].falatitude ) &&
				( TicketsList[i].falongitude == TicketsList[tindex].falongitude ) ) {
					res.count++;
					icolor = GetTicketColor( TicketsList[i] );
					if ( res.color <  icolor) res.color = icolor;
					TicketsList[i].MarkerIndex = TicketsList[tindex].MarkerIndex;
			}else{
				break;
			}
		}		
		return res;
	}

	function GetTicketColor(ticket)
	{
		var color = 0;
		if (( "DISPATCHER_COMPLETE" == ticket.tistatus ) ||
			( "OPERATOR_COMPLETE" == ticket.tistatus) ||
			( "KAO_COMPLETE" == ticket.tistatus) ){
			color = 1;					// green
		} else {
			if ( ticket.obsflag > 0) {
				color = 3; 				// red
			} else {
    			color = 2; 			// blue
			}
		}
		return color;
	}

	function addMarker( index ) {
		var ficon = '/img/orange_Marker.png';
		var ticket = TicketsList[index];
		var flat = 0;
		var flng = 0;
		var ticolor = GetTicketColor(ticket);

		// Подсчитываем кол-во заявок по типам
		switch(ticolor){
			case 1: MrkCountExecutet++; break;
			case 2: MrkCountInWork++; break;
			case 3: MrkCountOverdue++; break;
			default: MrkCountOther++;
		}

		// Если это первая заявка для координаты
		if ( IsFirstTicketForFacility(index) ) {
	        ticket.MarkerIndex = GMarkerIndex;

			var tiparam = GetMarkerParamForTickets(index);	
			switch(tiparam.color){
				case 1: 	
					ficon = '/img/green_Marker.png'; break;
				case 2: 	
					ficon = '/img/blue_Marker.png'; break;
				case 3: 	
					ficon = '/img/red_Marker.png'; break;
				case 0: 
				default:
					ficon = '/img/orange_Marker.png'; break;
			}

			flat = Number (ticket.falatitude);
			flng = Number (ticket.falongitude);
			// Пересчитываем область размещения заявок
			/*
			if ( 0 == markers.length) {
				MinMarkerLat = flat;
				MaxMarkerLat = flat;
				MinMarkerLng = flng;
				MaxMarkerLng = flng;
			}else{
				*/
				if ( MinMarkerLat > flat ) MinMarkerLat = flat;
				if ( MaxMarkerLat < flat ) MaxMarkerLat = flat;
				if ( MinMarkerLng > flng ) MinMarkerLng = flng;
				if ( MaxMarkerLng < flng ) MaxMarkerLng = flng;
				/*
			}
			*/

	    	var marker = new google.maps.Marker({
	          	position: {lat: flat, lng: flng},
		        map: ( UseCluster ? null : map ),
		        icon: {
		        	labelOrigin: new google.maps.Point(11, 12),
		        	url:ficon},
		        label: String(tiparam.count),
		        ticketindex: index,
		        tickcount : tiparam.count,
		        statecolor : tiparam.color,
		        index: GMarkerIndex
	    	});
			marker.addListener('click', function(){OnMarkerClick(marker);});
	        markers.push(marker);
	        GMarkerIndex++;
    	}
	}

	function ClearMarkers() {
		for (var i = 0; i < markers.length; i++) {
          markers[i].setMap(null);
	    }
		markers = [];
		markerCluster.clearMarkers()
		MinMarkerLat = myLatLng.lat;
		MinMarkerLng = myLatLng.lng;
		MaxMarkerLat = myLatLng.lat;
		MaxMarkerLng = myLatLng.lng;
		MrkCountExecutet = 0;
		MrkCountInWork = 0;
		MrkCountOverdue = 0;
		MrkCountOther = 0;
		GMarkerIndex = 0;
	}

	function VisualizeTickets(_TicketsList) {
		ClearMarkers(); 
		TicketsList =[];
		TicketsList = _TicketsList;

		var len = TicketsList.length;
		for (var index = 0; index < len; ++index) {
			addMarker( index );
		}

		console.log(  "Have responce. (" + TicketsList.length + ")");
		document.getElementById('UI1cnt').innerHTML= TicketsList.length;
		document.getElementById('UI1cntExecutet').innerHTML= MrkCountExecutet;
		document.getElementById('UI1cntInWork').innerHTML= MrkCountInWork;
		document.getElementById('UI1cntOverdue').innerHTML= MrkCountOverdue;

		if (UseCluster) markerCluster.addMarkers(markers,false);
	}

    var calc = function(markers, numStyles) {
    	var weight=0;
        var tStyle = 1;   
        var tcolor = 0;
        for(var i=0;i<markers.length;++i){
			weight+=markers[i].tickcount;
			if (tcolor < markers[i].statecolor) tcolor = markers[i].statecolor;
		}

		/*
		if (weight < 10) tStyle = 1;
		else if (weight < 100) tStyle = 2;
		else if (weight < 1000) tStyle = 3;
		else  tStyle = 4;
         */

        tStyle = tcolor;
        if (0 == tStyle) tStyle = 4;

		return {
			text: weight,
			index: tStyle
		};
	}
</script>

<script type="text/javascript">
	var filtrrequest = "";
	function sendAjaxForm() {
		filtrrequest = $("#formFltr1").serialize();
		console.log("filtr",filtrrequest);
		$.ajax({
			url:      '<?=Url::toRoute(["get-marker-list"]);?>' ,     	//url страницы 
			type:     "POST",                     						//метод отправки
			dataType: "html",                     						//формат данных
			data: filtrrequest,    										// Сеарилизуем объект
			success: function(response) {         						//Данные отправлены успешно
				result = $.parseJSON(response);
				VisualizeTickets(result);
				if (document.getElementById('chkbxRedrawAutoScale').checked) {
					// масштабируем карту автоматически
					if (markers.length > 1) {
						var bounds = new google.maps.LatLngBounds();
				   		bounds.extend( new google.maps.LatLng( MinMarkerLat, MinMarkerLng ) );
				   		bounds.extend( new google.maps.LatLng( MaxMarkerLat, MaxMarkerLng ) );
				   		map.setCenter(bounds.getCenter()) ;
						map.fitBounds(bounds);
					}else{
						// Если заявка одна, или их нет вообще
						// центрируем на весь город
				   		map.setCenter( myLatLng );
				   		map.setZoom( 11 );
					}
				}
			},
			error: function(response) {           // Данные не отправлены
			}
		});
	} 
</script>

<?php
$script = <<< JS

	$("#submitFltr1").click(
		function(){
			sendAjaxForm();
			return false; 
		}
	);  

	sendAjaxForm();

JS;

$this->registerJs($script, yii\web\View::POS_LOAD);
?>

<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
</script>

<script async defer
   src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhnTSqYgyKlAwxXqg4_fFkbo-KkdwLpN8&callback=initMap">
</script >



